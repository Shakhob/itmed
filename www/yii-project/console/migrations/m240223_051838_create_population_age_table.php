<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%population_age}}`.
 */
class m240223_051838_create_population_age_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%population_age}}', [
            'id' => $this->primaryKey(),
            'sort' => $this->tinyInteger(),
            'age' => $this->string(),
            'year' => $this->integer(),
            'male' => $this->float(),
            'female' => $this->float(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%population_age}}');
    }
}
