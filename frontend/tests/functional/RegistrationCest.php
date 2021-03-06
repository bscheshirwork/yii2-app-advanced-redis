<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use Da\User\Form\RegistrationForm;
use Da\User\Model\User;
use Da\User\Model\Token;
use common\tests\Page\Login as LoginPage;
use frontend\tests\Page\Registration as RegistrationPage;
use Yii;
use yii\helpers\Html;

class RegistrationCest
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
            ]
        ];
    }

    public function _after(FunctionalTester $I)
    {
        /** @var \Da\User\Module $moduleUser */
        $moduleUser = \Yii::$app->getModule('user');
        $moduleUser->enableEmailConfirmation = true;
        $moduleUser->generatePasswords = false;
        $moduleUser->enableGdprCompliance = false;
    }

    /**
     * Tests registration with email, username and password without any confirmation.
     * @param FunctionalTester $I
     * @throws \yii\base\InvalidConfigException
     */
    public function testRegistration(FunctionalTester $I)
    {
        /** @var \Da\User\Module $moduleUser */
        $moduleUser = \Yii::$app->getModule('user');
        $moduleUser->enableEmailConfirmation = false;
        $moduleUser->generatePasswords = false;
        $moduleUser->enableGdprCompliance = false;

        $model = \Yii::createObject(RegistrationForm::class);
        $page = new RegistrationPage($I);

        $I->amGoingTo('try to register with empty credentials');
        $page->register('', '', '');
        $I->see(Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $model->getAttributeLabel('username')]));
        $I->see(Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $model->getAttributeLabel('email')]));
        $I->see(Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => $model->getAttributeLabel('password')]));

        $I->amGoingTo('try to register with already used email and username');
        $user = $I->grabFixture('user', 'user');

        $page->register($user->email, $user->username, 'qwerty');
        $I->see(Html::encode(Yii::t('user', 'This username has already been taken')));
        $I->see(Html::encode(Yii::t('user', 'This email address has already been taken')));

        $page->register('tester@example.com', 'tester', 'tester');
        $I->see(Yii::t('user', 'Your account has been created'));
        $user = $I->grabRecord(User::class, ['email' => 'tester@example.com']);
        $I->assertTrue($user->isConfirmed);

        $page = new LoginPage($I);
        $page->login('tester', 'tester');
        $I->dontSee(Yii::t('user', 'Login'));
        $I->see($user->username);
    }

    /**
     * Tests registration when confirmation message is sent.
     * @param FunctionalTester $I
     */
    public function testRegistrationWithConfirmation(FunctionalTester $I)
    {
        /** @var \Da\User\Module $moduleUser */
        $moduleUser = \Yii::$app->getModule('user');
        $moduleUser->enableEmailConfirmation = true;
        $moduleUser->enableGdprCompliance = false;

        $page = new RegistrationPage($I);
        $page->register('tester@example.com', 'tester', 'tester');
        $I->see(Yii::t('user', 'Your account has been created and a message with further instructions has been sent to your email'));
        $user  = $I->grabRecord(User::class, ['email' => 'tester@example.com']);
        $token = $I->grabRecord(Token::class, ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRMATION]);
        /** @var yii\swiftmailer\Message $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertContains(Html::encode($token->getUrl()), utf8_encode(quoted_printable_decode($message->getSwiftMessage()->toString())));
        $I->assertFalse($user->isConfirmed);
    }

    /**
     * Tests registration when password is generated automatically and sent to user.
     * @param FunctionalTester $I
     */
    public function testRegistrationWithoutPassword(FunctionalTester $I)
    {
        /** @var \Da\User\Module $moduleUser */
        $moduleUser = \Yii::$app->getModule('user');
        $moduleUser->enableEmailConfirmation = false;
        $moduleUser->generatePasswords = true;
        $moduleUser->enableGdprCompliance = false;

        $page = new RegistrationPage($I);
        $page->register('tester@example.com', 'tester');
        $I->see(Yii::t('user', 'Your account has been created'));
        $user = $I->grabRecord(User::class, ['email' => 'tester@example.com']);
        $I->assertEquals('tester', $user->username);
        /** @var yii\swiftmailer\Message $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertContains(Yii::t('user', 'We have generated a password for you'), quoted_printable_decode($message->getSwiftMessage()->toString()));
    }

}
