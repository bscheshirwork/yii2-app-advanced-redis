<?php

namespace components\queue;



/**
 * Class SetStatusJob.
 */
class SendMailJob extends \yii\swiftmailer\Mailer implements \yii\queue\Job
{

    private $_message;

    /**
     * @inheritDoc
     */
    public function send($message)
    {
        $this->_message = $message;
        \Yii::$app->queue->push($this);
        return true;
    }


    /**
     * @inheritdoc
     */
    public function execute($queue)
    {
        parent::send($this->_message);
    }

}
