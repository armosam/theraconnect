<?php

namespace tests\codeception\backend\acceptance;

use Yii;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\base\LoginAttempt;
use tests\codeception\common\_pages\LoginPage;
use tests\codeception\backend\AcceptanceTester;

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
     * Test login process when wrong attempts exit limit in configuration.
     * It will try to login with username and email as well
     *
     * @param $I AcceptanceTester
     */
    public function testLoginAttemptsExitLimit($I)
    {
        $attemptLimit = Yii::$app->params['countOfAllowedFailedAttempts'];
        $halfAttempts = $attemptLimit/2;

        $I->wantTo('ensure that after maximal wrong attempts captcha shows on the page');
        $loginPage = LoginPage::openBy($I);

        $I->see('Login', 'button[type="submit"]');

        $I->amGoingTo('do wrong login attempts (login with username)');
        for ($i = 1; $i<=$halfAttempts;$i++){
            $loginPage->login('wrong', 'wrong');
            $I->expectTo('see no captcha on the page');
            $I->dontSee('Verification Code');
        }

        $I->amGoingTo('do wrong login attempts (login with email)');
        for ($j = 1; $j<$halfAttempts;$j++) {
            $loginPage->login('wrong@test.com', 'wrong');
            $I->expectTo('see no captcha on the page');
            $I->dontSee('Verification Code');
        }

        $loginPage->login('wrong', 'wrong');

        $I->expectTo('see captcha on the page');
        $I->see('Verification Code');
        $I->see('Please click on the code to change it.');

        $I->amGoingTo('do login with existing credentials (login with username)');
        $loginPage->login('member', 'member123');

        $I->expectTo('see user logged successfully');
        $I->seeLink('Logout (member)');
    }

    /**
     * Test login with email/password combo.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginWithEmail($I)
    {
        $I->wantTo('ensure that active user can login with email');
        $loginPage = LoginPage::openBy($I);

        $I->see('Login', 'button[type="submit"]');

        //-- submit form with no data --//
        $I->amGoingTo('(login with email): submit login form with no data');
        $loginPage->login('', '');

        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with email): try to login with wrong credentials');
        $loginPage->login('wrong@example.com', 'wrong');

        $I->expectTo('see validations errors');
        $I->see('Incorrect email or password.');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with email): try to login with non activated user');
        $loginPage->login('tester@example.com', 'test123');

        $I->expectTo('see flash error message that user is not activated');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');

        //-- login user with correct credentials --//
        $I->amGoingTo('try to log in correct user');
        $loginPage->login('member@example.com', 'member123');

        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (member)');

        $loginPage->logout();
    }

    /**
     * Test login with username/password combo.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginWithUsername($I)
    {
        $I->wantTo('ensure that active user can login with username');
        $loginPage = LoginPage::openBy($I);

        //-- submit form with no data --//
        $I->amGoingTo('(login with username): submit login form with no data');
        $loginPage->login('', '');

        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with username): try to login with wrong credentials');
        $loginPage->login('wrong', 'wrong');

        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with username): try to login with non activated user');
        $loginPage->login('tester', 'test123');

        $I->expectTo('see error flash message that user is not activated');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');

        //-- login user with correct credentials --//
        $I->amGoingTo('try to log in correct user');
        $loginPage->login('member', 'member123');

        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (member)');

        $loginPage->logout();

    }

    /**
     * We want to be sure that not activated user can not login with username.
     * If he try to login, he should get error flash message and activation email again.
     * 
     * @param $I AcceptanceTester
     */
    public function testLoginNotActivatedUserWithUsername($I)
    {
        $I->wantTo("ensure that not active user can't login");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in not activated user with username');
        $loginPage->login('tester', 'test123');

        $I->expectTo('see error flash message');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');
        $I->see('Login', 'button[type="submit"]');
    }

    /**
     * We want to be sure that not activated user can not login with email.
     * If he try to login, he should get error flash message and activation email again.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginNotActivatedUserWIthEmail($I)
    {
        $I->wantTo("ensure that not active user can't login");
        $loginPage = LoginPage::openBy($I);

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in not activated user with email');
        $loginPage->login('tester@example.com', 'test123');

        $I->expectTo('see error flash message');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');
        $I->see('Login', 'button[type="submit"]');
    }

    /**
     * We want to be sure that inactive user can login and see error message with username.
     * If he try to login, he should get error flash message that account is not active.
     * Also logged user can set own account active from profile or special link
     *
     * @param $I AcceptanceTester
     */
    public function testLoginInactiveUserWithUsername($I)
    {
        $I->wantTo("ensure that inactive user can login with username and see message");
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
        $I->see(Yii::t('app','Your account is currently inactive. Please set your account as active in your profile to be visible in the system.'), '.alert-warning');
    }

    /**
     * We want to be sure that inactive user can login and see error message with email.
     * If he try to login, he should get error flash message that account is not active.
     * Also logged user can set own account active from profile or special link
     *
     * @param $I AcceptanceTester
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
     * @param $I AcceptanceTester
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
     * @param $I AcceptanceTester
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
     * @param $I AcceptanceTester
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
     * @param $I AcceptanceTester
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

    /**
     * @param AcceptanceTester $I
     */
    public function testPermissionForMemberRole($I)
    {
        //-- login as member user with member role --//
        $loginPage = LoginPage::openBy($I);

        $I->amGoingTo('try to login as member user');
        $loginPage->login('member', 'member123');

        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (member)');

        $I->wantTo('ensure that member user does not have access to any menu in the backend');
        $I->dontSee('Users', '//ul.navbar-nav/li/a');
        $I->dontSee('Languages', '//ul.navbar-nav/li/a');
        $I->dontSee('Log', '//ul.navbar-nav/li/a');

        $I->wantTo('ensure that after logout user still does not have access');
        $loginPage->logout();

    }

}