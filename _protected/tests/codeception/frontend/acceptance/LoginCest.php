<?php

namespace tests\codeception\frontend\acceptance;

use Codeception\Scenario;
use Yii;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\base\LoginAttempt;
use tests\codeception\common\_pages\LoginPage;
use tests\codeception\frontend\AcceptanceTester;
use yii\web\Application;

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
        $I->wantTo('ensure that after maximal wrong attempts captcha shows on the page');
        $attemptLimit = Yii::$app->params['countOfAllowedFailedAttempts'];
        $halfAttempts = $attemptLimit/2;

        $I->amGoingTo('do wrong login attempts (login with username)');
        for ($i = 1; $i<=$halfAttempts;$i++){
            $I->loginAs('wrong', 'wrong');
            $I->expectTo('see no captcha on the page');
            $I->dontSee('Verification Code');
        }

        $I->amGoingTo('do wrong login attempts (login with email)');
        for ($j = 1; $j<$halfAttempts;$j++) {
            $I->loginAs('wrong@test.com', 'wrong');
            $I->expectTo('see no captcha on the page');
            $I->dontSee('Verification Code');
        }

        $I->loginAs('wrong', 'wrong');

        $I->expectTo('see captcha on the page');
        $I->see('Verification Code');
        $I->see('Please click on the code to change it.');

        $I->amGoingTo('login with existing credentials (login with username)');
        $I->loginAs('member', 'member123');

        $I->expectTo('see user logged successfully');
        $I->seeLoggedInAs('member');
    }

    /**
     * Test login with empty email/password combo.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginWithEmptyData($I)
    {
        $I->wantTo('ensure that login is not possible with empty data');

        //-- submit form with no data --//
        $I->amGoingTo('submit login form with no data');
        $I->loginAs('', '');

        $I->expectTo('see validations errors and user is not logged in');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
        $I->seeLink('Login');

        //-- submit form with no password --//
        $I->amGoingTo('submit login form with no password');
        $I->loginAs('member', '');

        $I->expectTo('see validations errors and user is not logged in');
        $I->see('Password cannot be blank.');
        $I->seeLink('Login');

        //-- submit form with no username --//
        $I->amGoingTo('submit login form with no password');
        $I->loginAs('', 'member123');

        $I->expectTo('see validations errors and user is not logged in');
        $I->see('Username cannot be blank.');
        $I->seeLink('Login');
    }


    /**
     * Test if active user can login with email/password combo.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginWithEmail($I)
    {
        $I->wantTo('ensure that active user can login with email');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with email): try to login with wrong credentials');
        $I->loginAs('wrong@example.com', 'wrong');

        $I->expectTo('see validations errors');
        $I->see('Incorrect email or password.');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with email): try to login with non activated user');
        $I->loginAs('tester@example.com', 'test123');

        $I->expectTo('see flash error message that user is not activated');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');

        //-- login user with correct credentials --//
        $I->amGoingTo('try to log in correct user');
        $I->loginAs('member@example.com', 'member123');

        $I->expectTo('see that user is logged in');
        $I->seeLoggedInAs('member');
    }

    /**
     * Test if active user can login with username/password combo.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginWithUsername($I)
    {
        $I->wantTo('ensure that active user can login with username');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with username): try to login with wrong credentials');
        $I->loginAs('wrong', 'wrong');

        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');

        //-- submit form with wrong credentials --//
        $I->amGoingTo('(login with username): try to login with non activated user');
        $I->loginAs('tester', 'test123');

        $I->expectTo('see error flash message that user is not activated');
        $I->see(yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');

        //-- login user with correct credentials --//
        $I->amGoingTo('try to log in correct user');
        $I->loginAs('member', 'member123');

        $I->expectTo('see that user is logged in');
        $I->seeLoggedInAs('member');
    }

    /**
     * We want to be sure that not activated user can not login with username.
     * If he try to login, he should get error flash message and activation email again.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginNotActivatedUserWithUsername($I)
    {
        $I->wantTo("ensure that not activated user can't login");

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in not activated user with username');
        $I->loginAs('tester', 'test123');

        $I->expectTo('see error flash message');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');
        $I->seeLink('Login');
    }

    /**
     * We want to be sure that not activated user can not login with email.
     * If he try to login, he should get error flash message and activation email again.
     *
     * @param $I AcceptanceTester
     */
    public function testLoginNotActivatedUserWithEmail($I)
    {
        $I->wantTo("ensure that not activated user can't login");

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in not activated user with email');
        $I->loginAs('tester@example.com', 'test123');

        $I->expectTo('see error flash message');
        $I->see(Yii::t('app', 'You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'), '.alert-danger');
        $I->see('Login');
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

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in with inactive user account');
        $I->loginAs('inactive', 'inactive123');
        $I->expectTo('see that user is logged in as inactive');
        $I->seeLoggedInAs('inactive');
        $I->dontSeeLink('Login');
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

        //-- try to login user that has not activated his account yet --//
        $I->amGoingTo('try to log in with inactive user account');
        $I->loginAs('inactive@example.com', 'inactive123');
        $I->expectTo('see that user is logged in');
        $I->seeLoggedInAs('inactive');
        $I->dontSeeLink('Login');
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
        //$loginPage = LoginPage::openBy($I);

        //-- try to login user that has suspended account --//
        $I->amGoingTo('try to log in suspended user by username');
        $I->loginAs('suspended', 'suspended123');
        $I->expectTo('see error flash message');
        $I->see('Your account is currently suspended. Please contact us with your information', '.alert-danger');
        $I->seeLink('Login');
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

        //-- try to login user that has suspended account --//
        $I->amGoingTo('try to log in suspended user by email');
        $I->loginAs('suspended@example.com', 'suspended123');
        $I->expectTo('see error flash message');
        $I->see('Your account is currently suspended. Please contact us with your information', '.alert-danger');
        $I->seeLink('Login');
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

        //-- try to login user that has terminated account --//
        $I->amGoingTo('try to log in terminated user by username');
        $I->loginAs('terminated', 'terminated123');
        $I->expectTo('see that user cannot login');
        $I->seeLink('Login');
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

        //-- try to login user that has terminated account --//
        $I->amGoingTo('try to log in terminated user by email');
        $I->loginAs('terminated@example.com', 'terminated123');
        $I->expectTo('see that user cannot login');
        $I->seeLink('Login');
        $I->dontSeeLink('Logout (terminated)');
    }

    /**
     * @param AcceptanceTester $I
     * @param Scenario $scenario
     */
    public function testPermissionForMemberRole($I, $scenario)
    {
        //-- login as member user with member role --//
        $I->wantTo("ensure that permission is working ");

        $I->amGoingTo('try to login as member user');
        $I->loginAs('member', 'member123');

        $I->expectTo('see that user is logged in');
        $I->seeLoggedInAs('member');

        $I->wantTo('ensure that member user does see article admin link');
        $I->see('Home');
        $I->cantSeeInSource('article/admin');
        $I->canSeeInSource('articles');
        $I->canSee('About Us');
        $I->canSee('Contact Us');

        $I->wantTo('ensure that member role and guest have not access to article admin page');

        $I->amGoingTo('open restricted for member article admin page');
        $I->amOpeningPage('article/admin');
        $I->canSee('You are not allowed to perform this action.', '.alert-danger');

        $I->amGoingTo('logout member user and ensure that guest still does not have access');
        $I->logout();

        $I->expectTo('see user logged out');
        if (method_exists($I, 'wait')) {
            $I->see('Login');
            $I->see('Sign Up');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testPermissionForGuest($I)
    {
        $I->wantTo('ensure guest user restricted to see article admin page');
        $I->amOpeningPage('article/admin');
        $I->expectTo('see user redirected to login page');
        $I->canSee('Sign in to continue', '.panel-heading');
        $I->canSee('Sign In', 'button');
    }
}
