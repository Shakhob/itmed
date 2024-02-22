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

class UserController extends Controller
{


    public function actionIndex()
    {
        $headers = Yii::$app->getRequest()->getHeaders();
        return new ActiveDataProvider([
            'query'=>User::find()
        ]);
    }

    public function actionAuth($token)
    {
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
            'user_token' => $userToken->token,
        ]);
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
                $existingUser = \common\models\User::findByUsername($responseData['username']);
                if ($existingUser) {
                    $expiresIn = 3600*6; // Token expires in 1 hour
                    $userToken = \backend\models\UserToken::createToken($expiresIn->id, $expiresIn->role, $expiresIn);
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
                Yii::$app->session->setFlash('error', 'Ошибка при выполнении запроса: ' . $response->getStatusCode() . ' ' . $this->getStatusCodeMessage($response->getStatusCode()));
            }
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            Yii::$app->session->setFlash('error', 'Ошибка при выполнении запроса: ' . $e->getMessage());
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

        return isset($codes[$statusCode]) ? $codes[$statusCode] : 'Unknown Status Code';
    }
}