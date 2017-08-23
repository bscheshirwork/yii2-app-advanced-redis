<?php

namespace console\controllers;

use components\queue\AddToRedisJob;
use components\queue\DownloadJob;
use Yii;
use yii\console\Controller;
use yii\console\widgets\Table;

class TestController extends Controller
{
    public function actionTest()
    {
//        Yii::$app->queue->push(new DownloadJob([
//            'url' => 'http://ambi.ru/img/logo.png',
//            'file' => '/tmp/image.jpg',
//        ]));
        Yii::$app->queue->push(new AddToRedisJob([
            'value' => 'http://ambi.ru/img/logo.png',
        ]));

        echo Table::widget([
            'headers' => ['Component', 'Status', 'result'],
            'rows' => [
//                ['redis', 'set', Yii::$app->redis->set('mykey', 'some value')],
                ['redis', 'get', Yii::$app->redis->get('mykey')],
            ],
        ]);
    }

}