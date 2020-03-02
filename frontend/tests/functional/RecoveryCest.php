<?php

namespace frontend\tests\functional;

use frontend\tests\FunctionalTester;
use common\fixtures\UserFixture;
use frontend\fixtures\TokenFixture;
use Da\User\Model\User;
use Da\User\Model\Token;
use common\tests\Page\Login as LoginPage;
use frontend\tests\Page\Recovery as RecoveryPage;
use frontend\tests\Page\RecoveryReset as ResetPage;
use Yii;
use yii\helpers\Html;

class RecoveryCest
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
            'token' => [
                'class' => TokenFixture::class,
                'dataFile' => codecept_data_dir() . 'token.php'
            ],
        ];
    }

    /**
     * @param FunctionalTester $I
     */
    public function recoveryUserPassword(FunctionalTester $I)
    {
        $page = new RecoveryPage($I);
        $resetPage = new ResetPage($I);
        $loginPage = new LoginPage($I);

        $I->wantTo('ensure that password recovery works');

        $I->amGoingTo('try to request recovery token for unconfirmed account');
        $user = $I->grabFixture('user', 'unconfirmed');
        $page->recover($user->email);
        $I->see(Yii::t('user', 'An email with instructions to create a new password has been sent to {email} if it is associated with an {appName} account. Your existing password has not been changed.', ['email' => $user->email, 'appName' => Yii::$app->name]));

        $I->amGoingTo('try to request recovery token');
        $user = $I->grabFixture('user', 'user');
        $page->recover($user->email);
        $I->see(Yii::t('user', 'An email with instructions to create a new password has been sent to {email} if it is associated with an {appName} account. Your existing password has not been changed.', ['email' => $user->email, 'appName' => Yii::$app->name]));
        $user = $I->grabRecord(User::class, ['email' => $user->email]);
        $token = $I->grabRecord(Token::class, ['user_id' => $user->id, 'type' => Token::TYPE_RECOVERY]);
        /** @var yii\swiftmailer\Message $message */
        $message = $I->grabLastSentEmail();
        $I->assertArrayHasKey($user->email, $message->getTo());
        $I->assertContains(Html::encode($token->getUrl()), utf8_encode(quoted_printable_decode($message->getSwiftMessage()->toString())));

        $I->amGoingTo('reset password with invalid token');
        $user = $I->grabFixture('user', 'user_with_expired_recovery_token');
        $token = $I->grabRecord(Token::class, ['user_id' => $user->id, 'type' => Token::TYPE_RECOVERY]);
        $resetPage->check(['id' => $user->id, 'code' => $token->code]);
        $I->see(Yii::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.'));

        $I->amGoingTo('reset password');
        $user = $I->grabFixture('user', 'user_with_recovery_token');
        $token = $I->grabRecord(Token::class, ['user_id' => $user->id, 'type' => Token::TYPE_RECOVERY]);
        $resetPage->reset('newpass', ['id' => $user->id, 'code' => $token->code]);
        $I->see(Yii::t('user', 'Password has been changed'));

        $loginPage->login($user->email, 'qwerty');
        $I->see(Yii::t('user', 'Invalid login or password'));
        $loginPage->login($user->email, 'newpass');
        $I->dontSee(Yii::t('user', 'Invalid login or password'));
    }
}