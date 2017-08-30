<?php

namespace components\queue;



/**
 * Class SendMailJob. Use it in 'mailer' component instead of \yii\swiftmailer\Mailer with all old settings.
 * Messages can be form, serialize and stored in queue.
 * Workers will be unserialize and send stored mail.
 * Each mail message will be send separated.
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
