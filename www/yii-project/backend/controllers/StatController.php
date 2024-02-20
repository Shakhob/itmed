<?php

namespace backend\controllers;

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
        $url = 'https://api.stat.uz/api/v1.0/data/doimiy-aholi-soni-erkaklar?lang=ru&format=json';
        return $this->fetchData($url);
    }

    public function actionCountWomen()
    {
        $url = 'https://api.stat.uz/api/v1.0/data/doimiy-aholi-soni-ayollar?lang=ru&format=json';
        return $this->fetchData($url);
    }
}
