<?php
return [
    'adminEmail' => 'admin@example.com',
    'yiisoft/yii-swagger' => [
        'annotation-paths' => [
            '@backend/Controller' // Директория, где используются аннотации
        ],
        'cacheTTL' => 60 // Включает кэширование и устанавливает TTL, значение "null" означает бесконечный TTL кэша.
    ],
];
