<?php
namespace frontend\tests\Page;

use yii\helpers\Url;

class RecoveryReset
{
    public static $URL = '/user/recovery/reset';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     * public static $usernameField = '#username';
     * public static $formSubmitButton = "#mainForm input[type=submit]";
     */

    public static $newPasswordField = 'form[action*="reset"] input[name*="password"]';
    /**
     * @see \Da\User\Module::$urlRules
     */
    public static $submitButton = 'form[action*="reset"] button[type=submit]';

    /**
     * @var \Codeception\Actor
     */
    protected $tester;

    /**
     * @var array
     */
    protected $param;

    public function __construct(\Codeception\Actor $I)
    {
        $this->tester = $I;
    }

    /**
     * Update user action
     * @param array $param
     * @return $this
     */
    public function check(array $param = [])
    {
        $I = $this->tester;
        $this->param = $param;
        $route = [self::$URL] + $this->param;
        $I->amOnPage(Url::toRoute($route));
        return $this;
    }

    /**
     * Update user action
     * @param $password
     * @param array $param
     * @return $this
     */
    public function reset($password, array $param = [])
    {
        $this->check($param);

        $I = $this->tester;
        $I->fillField(self::$newPasswordField, $password);
        $I->click(self::$submitButton);

        return $this;
    }

}
