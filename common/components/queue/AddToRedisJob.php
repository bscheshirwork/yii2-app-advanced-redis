<?php

namespace components\queue;

use Yii;
use yii\base\Object;

class AddToRedisJob extends Object implements \yii\queue\Job
{
    public $value;

    public function execute($queue)
    {
        Yii::$app->redis->set('mykey', $this->value);
    }
}
