<?php
return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=db;dbname=yii2advanced',
            'username' => 'yii2advanced',
            'password' => 'yii2advanced',
        ],
        'redis' => [
            'password' => 'yii2advancedredis',
        ],
        'mailer' => [
            // 'class' => 'yii\swiftmailer\Mailer',
            // 'viewPath' => '@common/mail',
        ],
    ],
];
