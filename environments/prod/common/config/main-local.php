<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=db;dbname=yii2advanced',
            'username' => 'yii2advanced',
            'password' => 'yii2advanced',
            'charset' => 'utf8',
        ],
        'redis' => [
            'password' => 'yii2advancedredis',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
