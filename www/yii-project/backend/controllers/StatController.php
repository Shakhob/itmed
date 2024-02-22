<?php

namespace backend\controllers;

use common\models\Population;
use common\models\Regions;
use Yii;
use yii\db\Expression;
use yii\web\Controller;

class StatController extends Controller
{
    private function fetchData($url)
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request('GET', $url, [
                'verify' => false
            ]);

            $body = $response->getBody();
            return json_decode($body, true);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return 'Ошибка при выполнении запроса: ' . $e->getMessage();
        }
    }

    public function actionCount()
    {
        $year = [];
        $url = 'https://api.stat.uz/api/v1.0/data/doimiy-aholi-soni-erkaklar?lang=ru&format=json';
        $data = $this->fetchData($url);
        $women = 'https://api.stat.uz/api/v1.0/data/doimiy-aholi-soni-ayollar?lang=ru&format=json';
        $data_w = $this->fetchData($women);
        $regions = Regions::find()->all();

        $regions_value = [];
        foreach ($regions as $region) {
            $regions_value[] = [
                'id'=>  $region->id,
                'name'=>  $region->name,
            ];
        }

        foreach ($data_w['data'] as $keys=>$records) {
            foreach ($records as $key=>$record){
                if(is_numeric($key)){
                    $year_w[] = $key;
                }
                if(is_numeric($record)){
                    $count_w[] = $record;
                }
            }
            $combined_data_w[$keys] = array_combine($year_w, $count_w);
        }

        foreach ($data['data'] as $keys=>$records) {
            foreach ($records as $key=>$record){
                if(is_numeric($key)){
                    $year[] = $key;
                }
                if(is_numeric($record)){
                    $count[] = $record;
                }else{
                    $region_data[] = $record;
                }
            }
            $combined_data[$keys] = array_combine($year, $count);

        }
        for ($i = 0; $i < count($region_data); $i += 4) {
            $region_data_value[]= $region_data[$i];
        }

        foreach ($combined_data as $keys => $item) {
            $temp = [];
            foreach ($item as $year => $value) {
                $region_name = $region_data_value[$keys]; // Получаем имя региона для текущего элемента
                $region_id = null;
                // Проверяем, есть ли регион с таким именем в списке регионов
                foreach ($regions_value as $region_value) {
                    $trimmed = substr($region_value['name'], 2, 4);
                    $trimmed_name = mb_substr($region_name, 2, 4);

                    if ($trimmed === $trimmed_name) {
                        $region_id = $region_value['id']; // Если нашли совпадение, получаем ID региона
                        break;
                    }
                }

                if ($region_id !== null) {
                    $temp[] = [$region_id, $year, $value,$combined_data_w[$keys][$year]]; // Используем ID региона вместо его имени
                } else {
                    // Если не удалось найти соответствие, обработка ошибки или другие действия
                }
            }
            $transformed_data[] = $temp;
        }

        foreach ($transformed_data as $transformed){
            foreach ($transformed as $item){
//                echo "<pre>"; print_r($item); echo "</pre>";

                $population = new Population();

                // Заполняем поля модели данными
                $population->region_id = $item[0]; // ID региона
                $population->year = $item[1]; // Год
                $population->male = $item[2]; // Количество мужчин
                $population->female =$item[3];
                $population->created_at = new Expression('CURRENT_TIMESTAMP');
                $population->updated_at = new Expression('CURRENT_TIMESTAMP');
//                if (!$population->save()) {
//                    // Обработка ошибок сохранения, если необходимо
//                    return $population->errors;
//                }
            }
        }


//        DIE;
        return $transformed_data ;
    }

    public function actionAge()
    {
        $year = [];
        $url = 'https://api.stat.uz/api/v1.0/data/aholining-yosh-tarkibi-boyicha-taqsimlanishi-1?lang=ru&format=json';
        $data = $this->fetchData($url);
        $women = 'https://api.stat.uz/api/v1.0/data/aholining-yosh-tarkibi-boyicha-taqsimlanishi-2?lang=ru&format=json';
        $data_w = $this->fetchData($women);
        $regions = Regions::find()->all();
        $regions_value = [];
        foreach ($regions as $region) {
            $regions_value[] = [
                'id'=>  $region->id,
                'name'=>  $region->name,
            ];
        }

//        foreach ($data_w['data'] as $keys=>$records) {
//
//            foreach ($records as $key=>$record){
//                if(is_numeric($key)){
//                    $year_w[] = $key;
//                }
//                if(is_numeric($record)){
//                    $count_w[] = $record;
//                }
//            }
//            $combined_data_w[$keys] = array_combine($year_w, $count_w);
//        }

        foreach ($data['data'] as $keys=>$records) {

            foreach ($records as $key=>$record){
                if(is_numeric($key)){
                    $year[] = $key;
                }
                if(is_numeric($record)){
                    $count[] = $record;
                }else{
                    $region_data[] = $record;
                }
            }
            $combined_data[$keys] = array_combine($year, $count);

        }
        for ($i = 0; $i < count($region_data); $i += 4) {
            $region_data_value[]= $region_data[$i];
        }

        foreach ($combined_data as $keys => $item) {
            $temp = [];
            foreach ($item as $year => $value) {
                $region_name = $region_data_value[$keys]; // Получаем имя региона для текущего элемента
                $region_id = null;
                // Проверяем, есть ли регион с таким именем в списке регионов
                foreach ($regions_value as $region_value) {
                    $trimmed = substr($region_value['name'], 2, 4);
                    $trimmed_name = mb_substr($region_name, 2, 4);

                    if ($trimmed === $trimmed_name) {
                        $region_id = $region_value['id']; // Если нашли совпадение, получаем ID региона
                        break;
                    }
                }

                if ($region_id !== null) {
                    $temp[] = [$region_id, $year, $value,$combined_data_w[$keys][$year]]; // Используем ID региона вместо его имени
                } else {
                    // Если не удалось найти соответствие, обработка ошибки или другие действия
                }
            }
            $transformed_data[] = $temp;
        }
        echo "<pre>"; print_r($transformed_data); echo "</pre>";DIE;

        foreach ($transformed_data as $transformed){
            foreach ($transformed as $item){
//                echo "<pre>"; print_r($item); echo "</pre>";

                $population = new Population();

                // Заполняем поля модели данными
                $population->region_id = $item[0]; // ID региона
                $population->year = $item[1]; // Год
                $population->male = $item[2]; // Количество мужчин
                $population->female =$item[3];
                $population->created_at = new Expression('CURRENT_TIMESTAMP');
                $population->updated_at = new Expression('CURRENT_TIMESTAMP');
//                if (!$population->save()) {
//                    // Обработка ошибок сохранения, если необходимо
//                    return $population->errors;
//                }
            }
        }


//        DIE;
        return $transformed_data ;
    }

    public function actionPopulation($year=2023)
    {
        if($year<2000 || $year>2023){
            return "Bu yilda ma'lumot mavjud emas";
            exit();
        }

        $query = (new \yii\db\Query())
            ->select(['id', 'region_id', 'year', 'male', 'female', new Expression('male + female AS total_population')])
            ->from('population')
            ->where(['region_id' => 17, 'year' => $year]);

        $command = $query->createCommand();
        $rows = $command->queryAll();
        // Преобразуем значения мужчин и женщин в числа
        $male = floatval($rows[0]['male']);
        $totalPopulation = ($rows[0]['total_population']);
        // Вычисляем процентное соотношение мужчин и женщин от общего населения
        $malePercentage = ($male / $totalPopulation) * 100;
        $malePercentage = ceil($malePercentage); // Округляем значение до ближайшего целого числа
        $femalePercentage = 100-$malePercentage;
        // Округляем значения до двух знаков после запятой
        $malePercentage = round($malePercentage, 2);
        $femalePercentage = round($femalePercentage, 2);
        // Выводим результаты
        return [
            'year'=>$rows[0]['year'],
            'male'=>$malePercentage,
            'female'=>$femalePercentage,
            'total'=>round($totalPopulation,-2)/1000,
        ];
    }
    public function actionCountWomen()
    {
        $url = 'https://api.stat.uz/api/v1.0/data/doimiy-aholi-soni-ayollar?lang=ru&format=json';
        return $this->fetchData($url);
    }
}
