<?php

namespace backend\controllers;

use common\models\User;
use frontend\models\UserToken;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;

class PostController extends Controller
{
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
    public function actionIndex()
    {
        $headers = Yii::$app->getRequest()->getHeaders();


        // Возвращаем провайдер данных
        return new ActiveDataProvider([
            'query'=>User::find()
        ]);
    }



    public function actionLogin()
    {
        $request = Yii::$app->request;
        $username = $request->getBodyParam('username');
        $password = $request->getBodyParam('password');
        $scope = Yii::$app->request->post('scope');
//        echo "<pre>"; print_r($request); echo "</pre>";DIE;

        // Проверка логина и пароля, например, с использованием модели пользователя
        $user = User::findByUsername($username);
        if (!$user || !$user->validatePassword($password)) {
            // Логин или пароль неверны
            Yii::$app->response->setStatusCode(401);
            return $this->asJson(['success' => false, 'message' => 'Invalid username or password']);
        }
        // Create a new token for the user
        $expiresIn = 3600*6; // Token expires in 1 hour
        $userToken = UserToken::createToken($user->id, $scope, $expiresIn);
        return $this->asJson([
            'user_token' => $userToken->token,
        ]);
    }

}