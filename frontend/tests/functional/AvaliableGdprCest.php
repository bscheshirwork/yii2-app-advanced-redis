<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use frontend\fixtures\ProfileFixture;
use frontend\tests\Page\GdprDelete;
use Yii;

class AvaliableGdprCest
{
    /**
     * Load fixtures before db transaction begin
     * Called in _before()
     * @see \Codeception\Module\Yii2::_before()
     * @see \Codeception\Module\Yii2::loadFixtures()
     * @return array
     */
    public function _fixtures(){
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
            'profile' => [
                'class' => ProfileFixture::class,
                'dataFile' => codecept_data_dir() . 'profile.php'
            ],
        ];
    }

    public function _after(FunctionalTester $I)
    {
        /** @var \Da\User\Module $moduleUser */
        $moduleUser = \Yii::$app->getModule('user');
        $moduleUser->enableGdprCompliance = false;
    }

    /**
     * Test privacy page
     *
     * @param FunctionalTester $I
     * @throws \yii\base\InvalidConfigException
     */
    public function testPrivacyPage(FunctionalTester $I)
    {
        /** @var \Da\User\Module $moduleUser */
        $moduleUser = \Yii::$app->getModule('user');
        $moduleUser->enableGdprCompliance = true;

        $page = new GdprDelete($I);

        $I->amGoingTo('try that privacy page works');
        $I->amLoggedInAs(1);
        $I->amOnRoute('/user/settings/privacy');
        $I->see(Yii::t('user','Export my data'));
        $I->see(Yii::t('user','Delete my account'));
        $page->delete('wrongpassword');
        $I->see(Yii::t('user','Invalid password'));
        $page->delete('qwerty');
        $I->see(Yii::t('user','Your personal information has been removed'));
    }

    /**
     * Test privacy page
     *
     * @param FunctionalTester $I
     */
    public function testPrivacyPageAccess(FunctionalTester $I)
    {
        /** @var \Da\User\Module $moduleUser */
        $moduleUser = \Yii::$app->getModule('user');
        $moduleUser->enableGdprCompliance = false;

        $I->amGoingTo('Try that a user cant access to privacy if GDPR is not enabled');
        $I->amLoggedInAs(1);
        $I->amOnRoute('/user/settings/privacy');
        $I->seeResponseCodeIs(404);
    }

}
