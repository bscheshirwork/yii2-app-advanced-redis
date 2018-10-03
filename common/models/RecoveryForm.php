<?php

namespace common\models;


class RecoveryForm extends \Da\User\Form\RecoveryForm {

    public $captcha;

    public function rules() {

        $rules = parent::rules();

        $rules[] = [['captcha'], 'required'];
        $rules[] = [['captcha'], \Da\User\Validator\ReCaptchaValidator::class];

        return $rules;
    }
}