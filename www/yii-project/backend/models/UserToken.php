<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;

class UserToken extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_token}}';
    }

    public static function generateToken($userId)
    {
        return Yii::$app->security->generateRandomString(32);
    }

    public static function createToken($userId, $scope, $expiresIn)
    {
        $token = self::generateToken($userId);
        $expiresAt = time() + $expiresIn;
        
        $userToken = new UserToken();
        $userToken->user_id = $userId;
        $userToken->token = $token;
        $userToken->scope = $scope;
        $userToken->expires_at = date('Y-m-d H:i:s', $expiresAt);

        $userToken->refresh_token = self::generateToken($userId);

        $d  = time() + 86400;
        $userToken->refresh_token_expires_at = date('Y-m-d H:i:s', $d);
        $userToken->created_at = date('Y-m-d H:i:s');
        $userToken->save();
        
        return $userToken;
    }
    
    public function isExpired(): bool
    {
        return strtotime($this->expires_at) < time();
    }
    
    public static function deleteExpiredTokens()
    {
        self::deleteAll(['<', 'expires_at', date('Y-m-d H:i:s')]);
    }


    public function refreshTokenIsExpired(): bool
    {
        return strtotime($this->refresh_token_expires_at) < time();
    }
    
    public static function refreshTokendeleteExpiredTokens()
    {
        self::deleteAll(['<', 'expires_at', date('Y-m-d H:i:s')]);
    }


}
