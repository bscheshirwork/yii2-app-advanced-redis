<?php
namespace frontend\tests\Page;

use yii\helpers\Url;

class GdprDelete
{
    public static $URL = '/user/settings/gdprdelete';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $passwordField = 'form[action*="gdpr"] input[name*="password"]';
    public static $submitButton = 'form[action*="gdpr"] button[type=submit]';

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
     * @param $password
     * @return $this
     */
    public function delete($password)
    {
        $I = $this->tester;

        $I->amOnPage(Url::toRoute(self::$URL));

        $I->fillField(self::$passwordField, $password);
        $I->click(self::$submitButton);

        return $this;
    }


}
