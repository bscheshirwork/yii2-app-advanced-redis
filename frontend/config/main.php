<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'modules' => [
        'user' => [
            'controllerMap' => [
                'profile' => [
                    'class' => \Da\User\Controller\ProfileController::class,
                    'as access' => [
                        'class' => yii\filters\AccessControl::class,
                        'rules' => [
                            ['allow' => true, 'actions' => ['index'], 'roles' => ['@']], //redirect на show. id в данном действии нет
                            ['allow' => true, 'actions' => ['show'], 'permissions' => ['showUserProfile']], // просмотр других запрещён
                        ],
                    ],
                ],
                'settings' => [
                    'class' => \Da\User\Controller\SettingsController::class,
                    'as access' => [
                        'class' => yii\filters\AccessControl::class,
                        'rules' => [
                            ['allow' => true, 'actions' => ['confirm']], // email confirmation from new email
                            ['allow' => true, 'actions' => ['privacy', 'export'], 'permissions' => ['updateSelfProfile']],
                            ['allow' => true, 'actions' => ['gdprdelete',], 'permissions' => ['updateSelfAccount']],
                            ['allow' => true, 'actions' => ['two-factor', 'two-factor-enable', 'two-factor-disable'], 'permissions' => ['updateSelfAccount']],
                            ['allow' => true, 'actions' => ['account'], 'permissions' => ['updateSelfAccount']],
                            ['allow' => true, 'actions' => ['profile'], 'permissions' => ['updateSelfProfile']],
                            ['allow' => true, 'actions' => ['networks', 'disconnect'], 'permissions' => ['updateSelfAccount']],
                        ],
                    ],
                ],
//                'security' => [
//                    'class' => \Da\User\Controller\SettingsController::class,
//                    'as access' => [
//                        'class' => yii\filters\AccessControl::class,
//                        'rules' => [
//                            ['allow' => true, 'actions' => ['login', 'logout', 'confirm']],
//                            ['allow' => true, 'actions' => ['auth', 'blocked']],
//                        ],
//                    ],
//                ],
                'recovery' => [
                    'class' => \Da\User\Controller\RecoveryController::class,
                    'on ' . \Da\User\Event\FormEvent::EVENT_AFTER_REQUEST => function (\Da\User\Event\FormEvent $event) {
                        \Yii::$app->controller->redirect(['/user/security/login']);
                        \Yii::$app->end();
                    },
                    'on ' . \Da\User\Event\ResetPasswordEvent::EVENT_AFTER_RESET => function (\Da\User\Event\ResetPasswordEvent $event) {
                        if ($event->token->user ?? false) {
                            \Yii::$app->user->login($event->token->user);
                        }
                        \Yii::$app->controller->redirect(\Yii::$app->getUser()->getReturnUrl());
                        \Yii::$app->end();
                    },
                ],
                'registration' => [
                    'class' => \Da\User\Controller\RegistrationController::class,
                    'on ' . \Da\User\Event\FormEvent::EVENT_AFTER_REGISTER => function (\Da\User\Event\FormEvent $event) {
                        \Yii::$app->controller->redirect(['/user/security/login']);
                        \Yii::$app->end();
                    },
                    'on ' . \Da\User\Event\FormEvent::EVENT_AFTER_RESEND => function (\Da\User\Event\FormEvent $event) {
                        \Yii::$app->controller->redirect(['/user/security/login']);
                        \Yii::$app->end();
                    },
                ],
                'admin' => [
                    'class' => Da\User\Controller\AdminController::class,
                    'as access' => [
                        'class' => yii\filters\AccessControl::class,
                        'rules' => [['allow' => false]],
                    ],
                ],
                'role' => [
                    'class' => Da\User\Controller\RoleController::class,
                    'as access' => [
                        'class' => yii\filters\AccessControl::class,
                        'rules' => [['allow' => false]],
                    ],
                ],
                'permission' => [
                    'class' => Da\User\Controller\PermissionController::class,
                    'as access' => [
                        'class' => yii\filters\AccessControl::class,
                        'rules' => [['allow' => false]],
                    ],
                ],
                'rule' => [
                    'class' => Da\User\Controller\RuleController::class,
                    'as access' => [
                        'class' => yii\filters\AccessControl::class,
                        'rules' => [['allow' => false]],
                    ],
                ],
            ],
        ],
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            // disable on redis
            // 'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];
