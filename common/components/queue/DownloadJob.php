<?php

namespace components\queue;

use yii\base\Object;

class DownloadJob extends Object implements \yii\queue\Job
{
    public $url;
    public $file;

    public function execute($queue)
    {
        file_put_contents($this->file, file_get_contents($this->url));
    }
}
