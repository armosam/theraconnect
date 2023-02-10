<?php

namespace tests\codeception\frontend\acceptance;

use Yii;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use Codeception\Scenario;
use tests\codeception\frontend\_pages\SignUpPage;
use tests\codeception\frontend\AcceptanceTester;

/**
 * Class SignUpCest
 * @package tests\codeception\frontend\acceptance
 * @group sign-up
 */
class SignUpCest
{
    /**
     * This method is called before each test method.
     *
     * @param TestEvent $event
     */
    public function _before($event)
    {
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
     * Test user sign-up process.
     * Based on your system settings for 'Registration Needs Activation' it will
     * run either testSignUpWithActivation() or testSignUpWithoutActivation() method.
     *
     * @param $I AcceptanceTester
     * @param $scenario Scenario
     */
    public function testSignUp($I, $scenario = null)
    {
        // get setting value for 'Registration Needs Activation'
        if(Yii::$app->params['rna']){
            $this->testSignUpWithActivation($I);
        }else{
            $this->testSignUpWithoutActivation($I);
        }
    }

    /**
     * Tests user normal sign-up process.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpWithoutActivation($I)
    {
        $I->wantTo('ensure that normal sign-up works');
        $signUpPage = SignUpPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a customer');

        //-- submit form with no data --//
        $I->amGoingTo('submit sign-up form with no data');
        $signUpPage->submit([]);

        $I->expectTo('see validation errors');
        $I->see('Username cannot be blank.', '.help-block');
        $I->see('Email cannot be blank.', '.help-block');
        $I->see('Password cannot be blank.', '.help-block');
        $I->see('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');

        //-- submit sign-up form with not correct email --//
        $I->amGoingTo('submit sign-up form with not correct email');
        $signUpPage->submit([
            'username' => 'demo',
            'email' => 'demo',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see that email address is wrong');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');
        $I->see('Email is not a valid email address.', '.help-block');

        //-- submit sign-up form with correct email --//
        $I->amGoingTo('submit sign-up form with correct email');
        $signUpPage->submit([
            'username' => 'demo',
            'email' => 'demo@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (demo)');     
    }

    /**
     * Tests user sign-up with activation process.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpWithActivation($I)
    {
        $I->wantTo('ensure that sign-up with activation works');
        $signUpPage = SignUpPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a customer');

        //-- submit sign-up form with correct data --//
        $I->amGoingTo('submit sign-up form with correct data');
        $signUpPage->submit([
            'username' => 'demo',
            'email' => 'demo@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up');

        $I->see('Hello demo, To be able to log in you need to confirm your registration. Please check your email, we have sent you a message.', '.alert-success');
        $I->dontSeeElement('.alert-danger');
        $I->dontSeeElement('.alert-warning');
        $I->dontSeeLink('Logout (demo)');
    }

    /**
     * Tests user sign-up without agreeing terms of service and privacy policy.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpWithoutAgreeing($I)
    {
        $I->wantTo('ensure that sign-up is not allowed until client agrees with policy');
        $signUpPage = SignUpPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a customer');

        //-- submit sign-up form with correct data --//
        $I->amGoingTo('submit sign-up form with correct data without agreeing policy');
        $signUpPage->submit([
            'username' => 'demo',
            'email' => 'demo@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 0
        ]);

        $I->expectTo('see an error that Agree checkbox is not checked');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('Email is not a valid email address.', '.help-block');
        $I->see('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up');
        $I->dontSeeLink('Logout (demo)');
    }

    /**
     * Tests user sign-up with username containing non alphanumeric or alphabetic symbols.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpWithIncorrectUsername($I)
    {
        $I->wantTo('ensure that sign-up is not allowed with username containing non alphanumeric or alphabetic symbols');
        $signUpPage = SignUpPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a customer');

        //-- submit sign-up form with incorrect username data --//
        $I->amGoingTo('submit sign-up form with username containing underscore');
        $signUpPage->submit([
            'username' => 'demo_first',
            'email' => 'demo@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see an error that username should contain alphanumeric or alphabetic symbols');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('Email is not a valid email address.', '.help-block');
        $I->see('The username should contain only alphanumeric or alphabetic characters.', '.help-block');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up');
        $I->dontSeeLink('Logout (demo)');

        $I->amGoingTo('submit sign-up form with username containing space');
        $signUpPage->submit([
            'username' => 'demo first',
            'email' => 'demo@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see an error that username should contain alphanumeric or alphabetic symbols');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('Email is not a valid email address.', '.help-block');
        $I->see('The username should contain only alphanumeric or alphabetic characters.', '.help-block');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up');
        $I->dontSeeLink('Logout (demo)');
    }
}
