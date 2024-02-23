<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "population_age".
 *
 * @property int $id
 * @property int|null $sort
 * @property string|null $age
 * @property int|null $year
 * @property float|null $male
 * @property float|null $female
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class PopulationAge extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'population_age';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'year'], 'integer'],
            [['male', 'female'], 'number'],
            [['age', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sort' => 'Sort',
            'age' => 'Age',
            'year' => 'Year',
            'male' => 'Male',
            'female' => 'Female',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Retrieves total population by age for a specific year.
     * @param int $year the year
     * @return array the total population by age
     */
    public static function getTotalPopulationByAge(int $year=2023, $gender=null): array
    {
        $query = (new Query());

        if ($gender === null) {
            $query->select(['sort','age','SUM(male) + SUM(female) as total']);
        } elseif ($gender === 'M') {
            $query->select(['sort','age','SUM(male) as total']);
        } elseif ($gender === 'F') {
            $query->select(['sort','age','SUM(female) as total']);
        }
        $query->from(self::tableName())
            ->where(['year' => $year])
            ->groupBy(['sort', 'age'])
            ->orderBy('sort');
        return $query->all();
    }


}
