<?php

use yii\db\ActiveRecord;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(__DIR__, 2) . '/vendor',
    'components' => [
        'cache' => [
            'class' => yii\redis\Cache::class,
            'redis' => 'redis' // id of the connection application component
        ],
        'authManager' => [
            'class' => yii\rbac\DbManager::class,
            'cache' => 'cache',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => yii\i18n\PhpMessageSource::class,
                    'basePath' => '@common/messages',
                ],
            ],
        ],
        'redis' => [
            'class' => yii\redis\Connection::class,
            'hostname' => 'redis',
            'port' => 6379,
            'database' => 0,
        ],
        'queue' => [
            'class' => yii\queue\redis\Queue::class,
            'redis' => 'redis', // id of the connection application component
            'channel' => 'queue', // Ключ канала очереди
        ],
        'session' => [
            'class' => bscheshirwork\redis\Session::class,
            'redis' => 'redis', // id of the connection application component
        ],
        'mailer' => [
            'class' => components\queue\SendMailJob::class,// class SendMailJob extends \yii\swiftmailer\Mailer implements \yii\queue\Job
            'viewPath' => '@common/mail',
        ],
        'authClientCollection' => [
            'class' => yii\authclient\Collection::class,
        ],
    ],
    'modules' => [
        'user' => [
            'class' => Da\User\Module::class,

            'enableRegistration' => true,
            'enableGdprCompliance' => true,
            'enableTwoFactorAuthentication' => true,

            'allowPasswordRecovery' => true,
            'allowAdminPasswordRecovery' => true,

            'classMap' => [
                'User' => [
                    'class' => \Da\User\Model\User::class,
                    'on ' . ActiveRecord::EVENT_AFTER_INSERT => function ($event) {
                        $auth = Yii::$app->authManager;
                        $userRole = $auth->getRole('user');
                        $auth->assign($userRole, $event->sender->getId());
                    },
                    'on ' . ActiveRecord::EVENT_BEFORE_DELETE => function ($event) {
                        $id = $event->sender->getId();
                        $auth = Yii::$app->authManager;

                        return $auth->revokeAll($id);
                    },
                ],
            ],
        ],
    ],
];
