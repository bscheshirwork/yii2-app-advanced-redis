<?php

namespace frontend\fixtures;

use yii\test\ActiveFixture;

class ProfileFixture extends ActiveFixture
{
    public $modelClass = \Da\User\Model\Profile::class;

    public $depends = [
        'common\fixtures\UserFixture'
    ];
}
