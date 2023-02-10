<?php

namespace tests\codeception\frontend\acceptance;

use Yii;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use Codeception\Scenario;
use tests\codeception\frontend\_pages\SignUpProviderPage;
use tests\codeception\frontend\AcceptanceTester;

/**
 * Class SignUpProviderCest
 * @package tests\codeception\frontend\acceptance
 * @group sign-up
 */
class SignUpProviderCest
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
     * Test user sign-up-provider process.
     * Based on your system settings for 'Registration Needs Activation' it will
     * run either testSignUpWithActivation() or testSignUpWithoutActivation() method.
     *
     * @param $I AcceptanceTester
     * @param $scenario Scenario
     */
    public function testSignUpProvider($I, $scenario = null)
    {
        // get setting value for 'Registration Needs Activation'
        if(Yii::$app->params['rna']){
            $this->testSignUpProviderWithActivation($I);
        }else{
            $this->testSignUpProviderWithoutActivation($I);
        }
    }

    /**
     * Tests user normal sign-up-provider process.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpProviderWithoutActivation($I)
    {
        $I->wantTo('ensure that sign-up-provider page works');
        $signUpProviderPage = SignUpProviderPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a specialist');

        //-- submit form with no data --//
        $I->amGoingTo('submit sign-up-provider form with no data');
        $signUpProviderPage->submit([]);

        $I->expectTo('see validation errors');
        $I->see('Username cannot be blank.', '.help-block');
        $I->see('Email cannot be blank.', '.help-block');
        $I->see('Password cannot be blank.', '.help-block');
        $I->see('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');

        //-- submit sign-up-provider form with not correct email --//
        $I->amGoingTo('submit sign-up-provider form with not correct email');
        $signUpProviderPage->submit([
            'username' => 'demoProvider',
            'email' => 'demoProvider',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see that email address is wrong');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');
        $I->see('Email is not a valid email address.', '.help-block');

        //-- submit sign-up-provider form with correct email --//
        $I->amGoingTo('submit sign-up-provider form with correct email');
        $signUpProviderPage->submit([
            'username' => 'demoProvider',
            'email' => 'demoProvider@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (demoProvider)');
    }

    /**
     * Tests user sign-up-provider with activation process.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpProviderWithActivation($I)
    {
        $I->wantTo('ensure that sign-up-provider page with activation works');
        $signUpPage = SignUpProviderPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a specialist');

        //-- submit sign-up-provider form with correct data  --//
        $I->amGoingTo('submit sign-up-provider form with correct data');
        $signUpPage->submit([
            'username' => 'demoProvider',
            'email' => 'demoProvider@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up-provider');

        $I->see('Hello demoProvider, To be able to log in you need to confirm your registration. Please check your email, we have sent you a message.', '.alert-success');
        $I->dontSeeElement('.alert-danger');
        $I->dontSeeElement('.alert-warning');
        $I->dontSeeLink('Logout (demoProvider)');
    }

    /**
     * Tests user sign-up-provider without agreeing terms of service and privacy policy.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpProviderWithoutAgreeing($I)
    {
        $I->wantTo('ensure that sign-up-provider is not allowed until client agrees with policy');
        $signUpPage = SignUpProviderPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a specialist');

        //-- submit sign-up-provider form with correct data --//
        $I->amGoingTo('submit sign-up-provider form with correct data without agreeing policy');
        $signUpPage->submit([
            'username' => 'demoProvider',
            'email' => 'demoProvider@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 0
        ]);

        $I->expectTo('see an error that Agree checkbox is not checked');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('Email is not a valid email address.', '.help-block');
        $I->see('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up-provider');
        $I->dontSeeLink('Logout (demoProvider)');
    }

    /**
     * Tests user sign-up-provider with username containing non alphanumeric or alphabetic symbols.
     *
     * @param $I AcceptanceTester
     */
    private function testSignUpWithIncorrectUsername($I)
    {
        $I->wantTo('ensure that sign-up-provider is not allowed with username containing non alphanumeric or alphabetic symbols');
        $signUpPage = SignUpProviderPage::openBy($I);
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a specialist');

        //-- submit sign-up-provider form with incorrect username data --//
        $I->amGoingTo('submit sign-up-provider form with username containing underscore');
        $signUpPage->submit([
            'username' => 'demo_first_provider',
            'email' => 'demoProvider@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see an error that username should contain alphanumeric or alphabetic symbols');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('Email is not a valid email address.', '.help-block');
        $I->see('The username should contain only alphanumeric or alphabetic characters.', '.help-block');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up-provider');
        $I->dontSeeLink('Logout (demo_first_provider)');

        $I->amGoingTo('submit sign-up-provider form with username containing space');
        $signUpPage->submit([
            'username' => 'demo first provider',
            'email' => 'demoProvider@example.com',
            'password' => 'asDF@#12asdf',
            'agreed' => 1
        ]);

        $I->expectTo('see an error that username should contain alphanumeric or alphabetic symbols');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('Email is not a valid email address.', '.help-block');
        $I->see('The username should contain only alphanumeric or alphabetic characters.', '.help-block');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up-provider');
        $I->dontSeeLink('Logout (demo first provider)');
    }

}