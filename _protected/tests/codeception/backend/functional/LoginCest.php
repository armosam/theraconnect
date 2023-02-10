<?php

namespace tests\codeception\backend\functional;

use Yii;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\User;
use common\models\LoginAttempt;
use tests\codeception\common\_pages\LoginPage;
use tests\codeception\backend\FunctionalTester;

class LoginCest
{
    /**
     * This method is called before each test method.
     *
     * @param TestEvent $event
     */
    public function _before($event)
    {
        LoginAttempt::deleteAll();
    }

    /**
     * This method is called after each test method, even if test failed.
     *
     * @param TestEvent $event
     */
    public function _after($event)
    {
    }

    /**
     * This method is called when test fails.
     *
     * @param FailEvent $event
     */
    public function _fail($event)
    {
    }

    /**
     * Test login process when wrong attempt updates database table value.
     * It will try to login with username and email as well
     *
     * @param $I FunctionalTester
     */
    public function testLoginWhenAttemptsNotExitingLimit($I)
    {
        $I->wantTo('ensure that after wrong attempts it updates counter in the DB.');
        $loginPage = LoginPage::openBy($I);

        $I->see('Login', 'button[type="submit"]');

        $I->amGoingTo('set 1 attempt in the database manually');
        $I->haveRecord(LoginAttempt::class, ['ip'=>'::1', 'failed_attempts' => 1]);

        $I->amGoingTo("do wrong login attempt (login with username)");
        $loginPage->login('wrong', 'wrong');

        $I->expectTo('see that Verification Code is not on the page');
        $I->dontSee('Verification Code');

        $I->amGoingTo("do wrong login attempt (login with email)");
        $loginPage->login('wrong@test.com', 'wrong');

        $I->expectTo('see that Verification Code is not on the page and wrong attempts in DB equal 3');
        $I->dontSee('Verification Code');
        $I->canSeeRecord(LoginAttempt::class, ['ip'=>'::1', 'failed_attempts' => 3]);
    }

    /**
     * Test login process when wrong attempts exit limit in configuration.
     * It will try to login with username and email as well
     *
     * @param $I FunctionalTester
     */
    public function testLoginWhenAttemptsExitingLimit($I)
    {
        $I->wantTo('ensure that after maximal wrong attempts captcha shows on the page.');
        $loginPage = LoginPage::openBy($I);

        $I->see('Login', 'button[type="submit"]');

        $attemptLimit = Yii::$app->params['countOfAllowedFailedAttempts'];
        $I->amGoingTo('set maximal '.$attemptLimit.' attempts in the database manually');
        $I->haveRecord(LoginAttempt::class, ['ip'=>'::1', 'failed_attempts' => $attemptLimit]);

        $I->amGoingTo("do wrong login attempt (login with username)");
        $loginPage->login('wrong', 'wrong');

        $I->expectTo('see Verification Code on the page');
        $I->see('Verification Code');
        $I->see('Please click on the code to change it.');

        $I->amGoingTo("do wrong login attempt (login with email)");
        $loginPage->login('wrong@test.com', 'wrong');

        $I->expectTo('see Verification Code on the page');
        $I->see('Verification Code');
        $I->see('Please click on the code to change it.');

        $I->expectTo('see that DB counter is equal to '. ($attemptLimit + 2) );
        $I->canSeeRecord(LoginAttempt::class, ['ip'=>'::1', 'failed_attempts' => ($attemptLimit + 2)]);
    }


    /**
     * Test login with empty user and password.
     *
     * @param $I FunctionalTester
     */
    public function testLoginWithEmptyData($I)
    {
        $I->wantTo('ensure that user cannot login without user and password');
        $loginPage = LoginPage::openBy($I);

        $I->see('Login', 'button[type="submit"]');

        //-- submit form with no data --//
        $I->amGoingTo('submit login form with no data');
        $loginPage->login('', '');
        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }
        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    /**
     * Test if active user can login with email/password combo.
     *
     * @param $I FunctionalTester
     */
    public function testLoginWithEmail($I)
    {
        $I->wantTo('ensure that active user can login with email');
        $loginPage = LoginPage::openBy($I);

        $I->see('Login', 'button[type="submit"]');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with email): try to login with wrong credentials');
        $loginPage->login('wrong@example.com', 'wrong');
        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }
        $I->expectTo('see validations errors');
        $I->see('Incorrect email or password.');

        //-- login user with correct credentials --//
        $I->amGoingTo('try to log in correct user');
        $loginPage->login('member@example.com', 'member123');
        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }
        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (member)');
    }

    /**
     * Test if active user can login with username/password combo.
     *
     * @param $I FunctionalTester
     */
    public function testLoginWithUsername($I)
    {
        $I->wantTo('ensure that active user can login with username');
        $loginPage = LoginPage::openBy($I);

        $I->see('Login', 'button[type="submit"]');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with username): try to login with wrong credentials');
        $loginPage->login('wrong', 'wrong');
        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }
        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');

        //-- login user with correct credentials --//
        $I->amGoingTo('try to log in correct user');
        $loginPage->login('member', 'member123');
        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }
        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (member)');
    }

    /**
     * We want to be sure that not activated user can not login with username.
     * If he try to login, he should get error flash message and activation email again.
     *
     * @param $I FunctionalTester
     */
    public function testLoginNotActivatedUserWithUsername($I)
    {
        $I->wantTo("ensure that not activated user can't login with username");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in not activated user');
        $loginPage->login('tester', 'test123');
        $I->expectTo('see error flash message');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');
        $I->see('Login', 'button[type="submit"]');

        // Check if activation email sent to user
        $I->seeEmailIsSent();
        $mail = $I->grabLastSentEmail();
        $I->expectTo('see correct To address');
        expect('email has correct To recipient', $mail->getTO())->hasKey('tester@example.com');
        $I->expectTo('see correct From address');
        expect('email has correct From recipient', $mail->getFrom())->hasKey(Yii::$app->params['fromEmailAddress']);
        $I->expectTo('see correct subject');
        expect('email has correct subject', $mail->getSubject())->equals(Yii::t('app', 'Account activation for {user}', ['user' => Yii::$app->name]));
        $I->expectTo('see correct activation token');
        expect('email has correct activation token', utf8_encode(quoted_printable_decode($mail->toString())))->stringContainsString($I->grabRecord(User::class, ['username' => 'tester'])->account_activation_token);
    }

    /**
     * We want to be sure that not activated user can not login with email.
     * If he try to login, he should get error flash message and activation email again.
     *
     * @param $I FunctionalTester
     */
    public function testLoginNotActivatedUserWithEmail($I)
    {
        $I->wantTo("ensure that not activated user can't login with email");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in not activated user');
        $loginPage->login('tester@example.com', 'test123');
        $I->expectTo('see error flash message');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');
        $I->see('Login', 'button[type="submit"]');

        // Check if activation email sent to user
        $I->seeEmailIsSent();
        $mail = $I->grabLastSentEmail();
        $I->expectTo('see correct To address');
        expect('email has correct To recipient', $mail->getTO())->hasKey('tester@example.com');
        $I->expectTo('see correct From address');
        expect('email has correct From recipient', $mail->getFrom())->hasKey(Yii::$app->params['fromEmailAddress']);
        $I->expectTo('see correct subject');
        expect('email has correct subject', $mail->getSubject())->equals(Yii::t('app', 'Account activation for {user}', ['user' => Yii::$app->name]));
        $I->expectTo('see correct activation token');
        expect('email has correct activation token', utf8_encode(quoted_printable_decode($mail->toString())))->stringContainsString($I->grabRecord(User::class, ['email' => 'tester@example.com'])->account_activation_token);
    }

    /**
     * We want to be sure that inactive user can login and see error message with username.
     * If he try to login, he should get error flash message that account is not active.
     * Also logged user can set own account active from profile or special link
     *
     * @param $I FunctionalTester
     */
    public function testLoginInactiveUserWithUsername($I)
    {
        $I->wantTo("ensure that inactive user can login with email and see message");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in with inactive user account');
        $loginPage->login('inactive', 'inactive123');
        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }
        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (inactive)');
        $I->dontSee('Login', 'button[type="submit"]');
        $I->expectTo('see error flash message');
        $I->canSee(Yii::t('app','Your account is currently inactive. Please set your account as active in your profile to be visible in the system.'), '.alert-warning');
    }

    /**
     * We want to be sure that inactive user can login and see error message with email.
     * If he try to login, he should get error flash message that account is not active.
     * Also logged user can set own account active from profile or special link
     *
     * @param $I FunctionalTester
     */
    public function testLoginInactiveUserWithEmail($I)
    {
        $I->wantTo("ensure that inactive user can login with email and see message");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in with inactive user account');
        $loginPage->login('inactive@example.com', 'inactive123');
        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }
        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (inactive)');
        $I->dontSee('Login', 'button[type="submit"]');
        $I->expectTo('see error flash message');
        $I->see(Yii::t('app','Your account is currently inactive. Please set your account as active in your profile to be visible in the system.'), '.alert-warning');
    }

    /**
     * We want to be sure that suspended user can login and see error message with username.
     * If he try to login, he should get error flash message that account is suspended.
     * Also logged user can not set own account active
     *
     * @param $I FunctionalTester
     */
    public function testLoginSuspendedUserWithUsername($I)
    {
        $I->wantTo("ensure that suspended user can't login with username");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has suspended account --//
        $I->amGoingTo('try to log in suspended user by username');
        $loginPage->login('suspended', 'suspended123');
        $I->expectTo('see error flash message');
        $I->see('Your account is currently suspended. Please contact us with your information', '.alert-danger');
        $I->see('Login', 'button[type="submit"]');
        $I->dontSeeLink('Logout (suspended)');
    }

    /**
     * We want to be sure that suspended user can login and see error message with email.
     * If he try to login, he should get error flash message that account is suspended.
     * Also logged user can not set own account active
     *
     * @param $I FunctionalTester
     */
    public function testLoginSuspendedUserWithEmail($I)
    {
        $I->wantTo("ensure that suspended user can't login with email");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has suspended account --//
        $I->amGoingTo('try to log in suspended user by email');
        $loginPage->login('suspended@example.com', 'suspended123');
        $I->expectTo('see error flash message');
        $I->see('Your account is currently suspended. Please contact us with your information', '.alert-danger');
        $I->see('Login', 'button[type="submit"]');
        $I->dontSeeLink('Logout (suspended)');
    }

    /**
     * We want to be sure that terminated user can not login with username.
     * If he try to login, he should get error flash message that account is terminated.
     * Also another user can not sign-up with same username who's account was terminated
     *
     * @param $I FunctionalTester
     */
    public function testLoginTerminatedUserWithUsername($I)
    {
        $I->wantTo("ensure that terminated user can't login with username");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has terminated account --//
        $I->amGoingTo('try to log in terminated user by username');
        $loginPage->login('terminated', 'terminated123');
        $I->expectTo('see that user cannot login');
        $I->see('Login', 'button[type="submit"]');
        $I->dontSeeLink('Logout (terminated)');
    }

    /**
     * We want to be sure that terminated user can not login with email.
     * If he try to login, he should get error flash message that account is terminated.
     * Also another user can not sign-up with same email who's account was terminated
     *
     * @param $I FunctionalTester
     */
    public function testLoginTerminatedUserWithEmail($I)
    {
        $I->wantTo("ensure that terminated user can't login with email");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has terminated account --//
        $I->amGoingTo('try to log in terminated user by email');
        $loginPage->login('terminated@example.com', 'terminated123');
        $I->expectTo('see that user cannot login');
        $I->see('Login', 'button[type="submit"]');
        $I->dontSeeLink('Logout (terminated)');
    }
}
