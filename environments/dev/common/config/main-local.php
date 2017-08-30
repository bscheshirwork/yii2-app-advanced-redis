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
            // 'class' => 'components\queue\SendMailJob',// class SendMailJob extends \yii\swiftmailer\Mailer implements \yii\queue\Job
            // 'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
