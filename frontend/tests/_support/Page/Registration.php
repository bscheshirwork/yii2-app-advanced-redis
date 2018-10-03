<?php
namespace frontend\tests\Page;

use yii\helpers\Url;

class Registration
{
    public static $URL = '/user/registration/register';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $emailField = 'form[action*="register"] input[name*="email"]';
    public static $usernameField = 'form[action*="register"] input[name*="username"]';
    public static $passwordField = 'form[action*="register"] input[name*="password"]';
    public static $gdprCheckbox = 'form[action*="register"] input[type="checkbox"][name*="gdpr"]';
    public static $submitButton = 'form[action*="register"] button[type=submit]';

    /**
     * @var \Codeception\Actor
     */
    protected $tester;

    public function __construct(\Codeception\Actor $I)
    {
        $this->tester = $I;
    }

    /**
     * Resend email action
     * @param $email
     * @return $this
     */
    public function register($email, $username = '', $password = null, $consent = false)
    {
        $I = $this->tester;

        $I->amOnPage(Url::toRoute(self::$URL));

        $I->fillField(self::$emailField, $email);
        $I->fillField(self::$usernameField, $username);
        if ($password !== null) {
            $I->fillField(self::$passwordField, $password);
        }
        if ($consent)
            $I->checkOption(self::$gdprCheckbox);
        $I->click(self::$submitButton);

        return $this;
    }


}
