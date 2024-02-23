<?php

namespace backend\controllers;

use backend\models\User;
use backend\models\UserToken;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\httpclient\Client;
use yii\rest\ActiveController;
use yii\rest\Controller;
use yii\web\UnauthorizedHttpException;

class UserController extends Controller
{


    public function actionIndex()
    {
        $accessToken = $this->getToken();
        echo "<pre>"; print_r($accessToken); echo "</pre>";DIE;
//        $headers = Yii::$app->getRequest()->getHeaders();
//        return new ActiveDataProvider([
//            'query'=>User::find()
//        ]);
    }

    public function actionAuth($token)
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        $this->getUserAndSave($token);
    }


    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->getBodyParam('username');
        $password = $request->getBodyParam('password');


        $user = \common\models\User::findByUsername($username);
        if (!$user || !$user->validatePassword($password)) {
            Yii::$app->response->setStatusCode(401);
            return $this->asJson(['success' => false, 'message' => 'Invalid username or password']);
        }
        $scope =$user->role;
        $expiresIn = 3600*6; // Token expires in 1 hour
        $userToken = UserToken::createToken($user->id, $scope, $expiresIn);
        return $this->asJson([
            'role' => $user->role,
            'user_token' => $userToken->token,
        ]);
    }

    public function actionRefresh()
    {
        $refresh_token = Yii::$app->request->headers->get('Authorization');
        $refresh_token = str_replace("Bearer ", '', $refresh_token) ;

        if (!$refresh_token) {
            throw new UnauthorizedHttpException('Token not provided.');
        }

        $user_refresh_token = UserToken::findOne(['refresh_token' => $refresh_token]);
        if (!$user_refresh_token || $user_refresh_token->refreshTokenIsExpired()) {
            throw new UnauthorizedHttpException('Invalid or expired token.');
        }

        $user = \backend\models\User::findOne($user_refresh_token->user_id);
        $userTokenScope = $user_refresh_token->scope;

        // Delete the expired token
        $user_refresh_token->delete();

        // Create a new token for the user
        $expiresIn = 3600*6; // Token expires in 12 hour
        $userToken = UserToken::createToken($user_refresh_token->user_id, $userTokenScope, $expiresIn);

        return [
            'role' => $user->role,
            'user_token' => $userToken
        ];
    }
    public function actionLogout()
    {
        $token = Yii::$app->request->headers->get('Authorization');

        if (!$token) {
            throw new UnauthorizedHttpException('Token not provided.');
        }

        $userToken = UserToken::findOne(['token' => $token]);

        if (!$userToken) {
            throw new UnauthorizedHttpException('Invalid token.');
        }

        // Delete the token
        $userToken->delete();

        return [
            'message' => 'Logged out successfully.'
        ];
    }
    public function actionUpdate()
    {
        return new ActiveDataProvider([
            'query'=>User::find()
        ]);
    }
    private function getUserAndSave($accessToken)
    {
        $client = new Client();

        $url = 'https://test-sso.ssv.uz/api/user';

        try {
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($url)
                ->addHeaders(['Authorization' => 'Bearer ' . $accessToken])
                ->setData([
                    'client_id' => '94f83670-f841-4ec5-a593-72d2873f054b',
                    'client_secret' => 'IAIkBdrmSVPWPH5vtvihY4vOfLW3V19oDEC9wHIs',
                ])
                ->send();

            if ($response->isOk) {
                $responseData = $response->getData();

                // Проверяем, существует ли пользователь с указанным именем пользователя
                $existingUser = \common\models\User::findByUsername($responseData['identify']);
//                echo "<pre>"; print_r($expiresIn->id); echo "</pre>";DIE;
                if ($existingUser) {
                    $expiresIn = 3600*6; // Token expires in 1 hour
                    $userToken = \backend\models\UserToken::createToken($existingUser->id, $existingUser->role, $expiresIn);
                    return $this->asJson([
                        'user_token' => $userToken->token,
                    ]);
                } else {
                    // Создаем нового пользователя
                    $user = new User();
                    $user->username = $responseData['identify']; // Предположим, что 'username' - это поле с именем пользователя в ответе
                    $user->email = $responseData['username'] . "@mail.ru"; // Предположим, что 'email' - это поле с email пользователя в ответе
                    $user->password_hash = Yii::$app->security->generateRandomString(12); // Генерируем случайный пароль
                    $user->status = User::STATUS_ACTIVE; // Устанавливаем статус пользователя
                    $user->role ='guest'; // Устанавливаем роль пользователя
                    $user->generateAuthKey(); // Генерируем auth_key
                    if ($user->save()) {
                        $expiresIn = 3600*6; // Token expires in 1 hour
                        $userToken = UserToken::createToken($user->id, $user->role, $expiresIn);
                        return $this->asJson([
                            'user_token' => $userToken->token,
                        ]);
                    }
                }
            } else {
                Yii::$app->response->setStatusCode(401);
                return [
                    'success' => false,
                    'data' => 'Ошибка при выполнении запроса: ' . $response->getStatusCode()
                ];
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Yii::$app->response->setStatusCode(401);
            return [
                'success' => false,
                'data' =>  'Ошибка при выполнении запроса: ' . $e->getMessage()
            ];
        }
    }

    private function getStatusCodeMessage($statusCode)
    {
        $codes = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            // Другие коды состояния HTTP
        ];

        return $codes[$statusCode] ?? 'Unknown Status Code';
    }
}