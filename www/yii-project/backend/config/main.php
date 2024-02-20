<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => 'ru',
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
            'cookieValidationKey' => 'itmed-api', // Добавьте эту строку
        ],
        'response' => [
            'format' =>  \yii\web\Response::FORMAT_JSON,
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode == 200) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'success' => true,
                        'data' => $response->data,
                        // 'headers_info' => Yii::$app->response->headers,

                    ];
                }elseif ($response->statusCode == 201) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'success' => true,
                        'data' => $response->data,
                        // 'headers_info' => Yii::$app->response->headers,
                    ];
                }elseif ($response->statusCode == 204) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'success' => true,
                        'data' => $response->data,
                        // 'headers_info' => Yii::$app->response->headers,
                    ];
                }elseif ($response->statusCode == 401) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'success' => false,
                        'data' => $response->data,
                        // 'headers_info' => Yii::$app->response->headers,
                    ];
                }elseif ($response->statusCode == 403) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'success' => false,
                        'data' => $response->data,
                        // 'headers_info' => Yii::$app->response->headers,
                    ];
                }elseif ($response->statusCode == 404) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'success' => false,
                        'data' => $response->data,
                        // 'headers_info' => Yii::$app->response->headers,
                    ];
                }elseif ($response->statusCode == 500) {
                    $response->data = [
                        'code' => $response->statusCode,
                        'success' => false,
                        // 'error' => 'An error occurred',
                        'data' => $response->data,
                        // 'headers_info' => Yii::$app->response->headers,
                    ];
                }
            },

        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@backend/messages',
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app'       => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],
            ],
        ],
        'urlManager' => [
            'class'                        => 'codemix\localeurls\UrlManager',
            'showScriptName'               => false,
            'enableLanguageDetection'      => false,
            'enablePrettyUrl'              => true,
            'enableDefaultLanguageUrlCode' => true,
            'languages'                    => ['uz', 'ru','en'],
            'rules' => [
                ['class' => 'yii\rest\UrlRule',
                    'controller' => [
                        'user',
                        'post',
                        'auth',

                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['swagger/default'],
                    'extraPatterns' => [
                        'GET json-url' => 'json-url'
                    ],
                    'prefix' => 'swagger',
                    'pluralize' => false,
                ],
            ],

        ],
    ],
    'params' => $params,
];
