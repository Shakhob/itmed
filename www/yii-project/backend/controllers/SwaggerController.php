<?php

namespace backend\controllers;

use Yii;
use yii\console\ExitCode;
use yii\web\Controller;
use OpenApi\Annotations\Info;
use function OpenApi\scan;
class SwaggerController extends Controller
{
    public function actionIndex()
    {
        $openApi = scan('@app/modules/api/controllers');
        $file = Yii::getAlias('@web/api-doc/swagger.yaml');
        $handle = fopen($file, 'wb');
        fwrite($handle, $openApi->toYaml());
        fclose($handle);
        echo $this->ansiFormat('Created \n", Console::FG_BLUE');
        return ExitCode::OK;
    }

    public function actionJsonUrl()
    {
        // Здесь вам нужно вернуть JSON-документацию Swagger, сгенерированную вашим приложением
    }
}
