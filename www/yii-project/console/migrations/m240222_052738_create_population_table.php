<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%population}}`.
 */
class m240222_052738_create_population_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%population}}', [
            'id' => $this->primaryKey(),
            'region_id' => $this->integer(),
            'year' => $this->integer(),
            'male' => $this->float(),
            'female' => $this->float(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
        $this->addForeignKey(
            'fk-population-region_id-regions-id',
            '{{%population}}',
            'region_id',
            '{{%regions}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%population}}');
    }
}
