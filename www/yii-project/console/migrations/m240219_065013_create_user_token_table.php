<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%user_token}}`.
 */
class m240219_065013_create_user_token_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%user_token}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'scope' => $this->string('255')->null(),
            'token' => $this->string('255')->notNull(),
            'expires_at' => $this->dateTime()->notNull(),
            'refresh_token' => $this->string('255')->notNull(),
            'refresh_token_expires_at' => $this->dateTime()->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex(
            'idx_user_token_user_id',
            'user_token',
            'user_id'
        );

        $this->createIndex(
            'idx_user_token_token',
            'user_token',
            'token'
        );

        $this->createIndex(
            'idx_user_token_refresh_token',
            'user_token',
            'refresh_token'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_token}}');
    }
}
