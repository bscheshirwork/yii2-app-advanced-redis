<?php
namespace frontend\tests\acceptance;

use frontend\tests\AcceptanceTester;
use common\fixtures\UserFixture;
use frontend\fixtures\ProfileFixture;
use Da\User\Model\User;
use Da\User\Model\Token;
use common\tests\Page\Login as LoginPage;
use frontend\tests\Page\UpdateSelfAccount as UpdatePage;
use frontend\tests\Page\ChangeEmailConfirm as ConfirmPage;
use Yii;

class UpdateSelfAccountCest
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

    /**
     * @param AcceptanceTester $I
     */
    public function updateSelfAccount(AcceptanceTester $I)
    {
        $loginPage = new LoginPage($I);
        $confirmPage = new ConfirmPage($I);
        $user = $I->grabFixture('user', 'user');
        $loginPage->login($user->email, 'qwerty');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_010_login');

        $I->wantTo('ensure that self account update works');

        $page = new UpdatePage($I);

        $I->amGoingTo('try to update self account with empty fields');
        $page->update('', '', '', '');
        $I->wait(4); // wait for page to be opened with all errors
        $I->makeScreenshot('updateSelfAccount_020_update_validation_error');
        $I->expectTo('see validations errors');
        $I->see(Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => Yii::t('user', 'Username')]));
        $I->see(Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => Yii::t('user', 'Email')]));
        $I->see(Yii::t('yii', '{attribute} cannot be blank.', ['attribute' => Yii::t('user', 'Current password')]));

        $I->amGoingTo('check that email is changing properly');
        $page->update($user->username, 'new_user@example.com', 'qwerty');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_030_email_send');
        $I->seeRecord(User::class, ['email' => $user->email, 'unconfirmed_email' => 'new_user@example.com']);
        $I->see(Yii::t('user', 'A confirmation message has been sent to your new email address'));
        $user = $I->grabRecord(User::class, ['id' => $user->id]);
        $token = $I->grabRecord(Token::class, ['user_id' => $user->id, 'type' => Token::TYPE_CONFIRM_NEW_EMAIL]);

        $I->click($user->username);
        $I->click(Yii::t('main', 'Logout ({username})', ['username' => $user->username]));
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_040_logout');

        $I->amGoingTo('log in using new email address before clicking the confirmation link');
        $loginPage->login('new_user@example.com', 'qwerty');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_050_login_new_email_fail');
        $I->see(Yii::t('user', 'Invalid login or password'));

        $I->amGoingTo('Confirm new email address by clicking the confirmation link');
        $confirmPage->check(['id' => $token->user_id, 'code' => $token->code]);
        $I->wait(2); // wait for page to be opened
        $I->see(Yii::t('user', 'Your email address has been changed'));
        $I->makeScreenshot('updateSelfAccount_051_after_confirm_new_email');

        $I->amGoingTo('log in using new email address after clicking the confirmation link');
        $loginPage->login('new_user@example.com', 'qwerty');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_060_login_new_email_success');
        $I->see($user->username);
        $I->seeRecord(User::class, [
            'id' => 1,
            'email' => 'new_user@example.com',
            'unconfirmed_email' => null,
        ]);

        $I->amGoingTo('reset email changing process');
        $page->update($user->username, 'user@example.com', 'qwerty');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_070_email_send');
        $I->see(Yii::t('user', 'A confirmation message has been sent to your new email address'));
        $I->seeRecord(User::class, [
            'id' => 1,
            'email' => 'new_user@example.com',
            'unconfirmed_email' => 'user@example.com',
        ]);
        $page->update($user->username, 'new_user@example.com', 'qwerty');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_080_email_data_revert');
        $I->see(Yii::t('user', 'Your account details have been updated'));
        $I->seeRecord(User::class, [
            'id' => 1,
            'email' => 'new_user@example.com',
            'unconfirmed_email' => null,
        ]);

        $I->amGoingTo('change username and password');
        $page->update('nickname', 'new_user@example.com', 'qwerty', '123654');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_090_change_data');
        $I->see(Yii::t('user', 'Your account details have been updated'));
        $I->seeRecord(User::class, [
            'username' => 'nickname',
            'email' => 'new_user@example.com',
        ]);

        $I->click('nickname');
        $I->click(Yii::t('main', 'Logout ({username})', ['username' => 'nickname']));
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_100_logout');

        $I->amGoingTo('login with new credentials');
        $loginPage->login('nickname', '123654');
        $I->wait(2); // wait for page to be opened
        $I->makeScreenshot('updateSelfAccount_110_login');
        $I->see('nickname');
    }
}