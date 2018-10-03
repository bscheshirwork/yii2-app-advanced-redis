<?php

namespace frontend\fixtures;

use yii\test\ActiveFixture;

class TokenFixture extends ActiveFixture
{
    public $modelClass = \Da\User\Model\Token::class;

    public $depends = [
        'common\fixtures\UserFixture'
    ];
}
