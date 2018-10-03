<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'bootstrap' => [
        'log',
        'queue', // Компонент регистрирует свои консольные команды
    ],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => yii\console\controllers\FixtureController::class,
            'namespace' => 'common\fixtures',
            'globalFixtures' => [
                [
                    'class' => yii\test\InitDbFixture::class,
                    'initScript' => '@app/fixtures/initdb.php'
                ],
            ],
        ],
        'migrate' => [
            'class' => yii\console\controllers\MigrateController::class,
            // Since version 2.0.12 an array can be specified for loading migrations from multiple sources.
            'migrationPath' => [
                '@app/migrations',
                '@yii/rbac/migrations/',
                '@mdm/admin/migrations',
            ],
            'migrationNamespaces' => [
                'Da\User\Migration',
            ],
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
