<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%regions}}`.
 */
class m240221_090121_create_regions_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%regions}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        $this->batchInsert('{{%regions}}', ['id', 'name'], [
            [17, 'O\'zbekiston Respublikasi'],
            [1703, 'Andijon viloyati'],
            [1706, 'Buxoro viloyati'],
            [1708, 'Jizzax viloyati'],
            [1710, 'Qashqadaryo viloyati'],
            [1712, 'Navoiy viloyati'],
            [1714, 'Namangan viloyati'],
            [1718, 'Samarqand viloyati'],
            [1722, 'Surxondaryo viloyati'],
            [1724, 'Sirdaryo viloyati'],
            [1726, 'Toshkent shahri'],
            [1727, 'Toshkent viloyati'],
            [1730, 'Farg\'ona viloyati'],
            [1733, 'Xorazm viloyati'],
            [1735, 'Qoraqalpog\'iston Respublikasi'],
        ]);
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%regions}}');
    }
}
