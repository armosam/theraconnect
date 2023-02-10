<?php

namespace tests\codeception\frontend\functional;

use Yii;
use common\models\User;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use tests\codeception\frontend\FunctionalTester;

/**
 * Class SignUpProviderCest
 * @package tests\codeception\frontend\functional
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
     * @param $I FunctionalTester
     */
    public function testSignUpProvider($I)
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
     * @param $I FunctionalTester
     */
    private function testSignUpProviderWithoutActivation($I)
    {
        $I->wantTo('ensure that sign-up-provider page works');
        $I->amOnRoute('site/sign-up-provider');
        $I->see('Sign Up');
        $I->see('Sign up as a specialist');

        //-- submit form with no data --//
        $I->amGoingTo('submit sign-up-provider form with no data');
        $I->fillField('input[name="SignUpProviderForm[username]"]', '');
        $I->fillField('input[name="SignUpProviderForm[email]"]', '');
        $I->fillField('input[name="SignUpProviderForm[password]"]', '');
        $I->uncheckOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

        $I->expectTo('see validation errors');
        $I->see('Username cannot be blank.', '.help-block');
        $I->see('Email cannot be blank.', '.help-block');
        $I->see('Password cannot be blank.', '.help-block');
        $I->see('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');

        //-- submit sign-up-provider form with not correct email --//
        $I->amGoingTo('submit sign-up-provider form with not correct email');
        $I->fillField('input[name="SignUpProviderForm[username]"]', 'demoProvider');
        $I->fillField('input[name="SignUpProviderForm[email]"]', 'demoProvider');
        $I->fillField('input[name="SignUpProviderForm[password]"]', 'asDF@#12asdf');
        $I->checkOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

        $I->expectTo('see that email address is wrong');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');
        $I->see('Email is not a valid email address.', '.help-block');

        //-- submit sign-up-provider form with not strong password --//
        $I->amGoingTo('submit sign-up-provider form with less than 6 letter password');
        $I->fillField('input[name="SignUpProviderForm[username]"]', 'demoProvider');
        $I->fillField('input[name="SignUpProviderForm[email]"]', 'demoProvider@example.com');
        $I->fillField('input[name="SignUpProviderForm[password]"]', '111');
        $I->checkOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

        $I->expectTo('see that password should be more than 6 letters');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Email cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('You are requested to agree with Terms of Service and Privacy Policy', '.help-block');
        $I->see('Password should contain at least 6 characters.', '.help-block');

        //-- submit sign-up-provider form with correct email --//
        $I->amGoingTo('submit sign-up-provider form with correct email');
        $I->fillField('input[name="SignUpProviderForm[username]"]', 'demoProvider');
        $I->fillField('input[name="SignUpProviderForm[email]"]', 'demoProvider@example.com');
        $I->fillField('input[name="SignUpProviderForm[password]"]', 'asDF@#12asdf');
        $I->checkOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

        $I->expectTo('see that user is logged in');
        $I->seeLink('Logout (demoProvider)');

        $I->canSeeRecord(User::class, ['username' => 'demoprovider', 'email' => 'demoprovider@example.com', 'status' => 'A']);
    }

    /**
     * Tests user sign-up-provider with activation process.
     *
     * @param $I FunctionalTester
     */
    private function testSignUpProviderWithActivation($I)
    {
        $I->wantTo('ensure that sign-up-provider with activation works');
        $I->amOnRoute('site/sign-up-provider');
        $I->see('Sign Up');
        $I->see('Sign up as a specialist');

        //-- submit sign-up-provider form with correct data --//
        $I->amGoingTo('submit sign-up-provider form with correct data');
        $I->fillField('input[name="SignUpProviderForm[username]"]', 'demoProvider');
        $I->fillField('input[name="SignUpProviderForm[email]"]', 'demoProvider@example.com');
        $I->fillField('input[name="SignUpProviderForm[password]"]', 'asDF@#12asdf');
        $I->checkOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up-provider');
        $I->see('Hello demoProvider, To be able to log in you need to confirm your registration. Please check your email, we have sent you a message.', '.alert-success');
        $I->dontSeeLink('Logout (demoProvider)');

        $I->canSeeRecord(User::class, ['username' => 'demoprovider', 'email' => 'demoprovider@example.com', 'status' => 'N']);
    }

    /**
     * Tests user sign-up-provider without agreeing terms of service and privacy policy.
     *
     * @param $I FunctionalTester
     */
    private function testSignUpWithoutAgreeing($I)
    {
        $I->wantTo('ensure that sign-up-provider is not allowed until client agrees with policy');
        $I->amOnRoute('site/sign-up-provider');
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a specialist');

        //-- submit sign-up-provider form with correct data --//
        $I->amGoingTo('submit sign-up-provider form with correct data without agreeing policy');
        $I->fillField('input[name="SignUpProviderForm[username]"]', 'demoProvider');
        $I->fillField('input[name="SignUpProviderForm[email]"]', 'demoProvider@example.com');
        $I->fillField('input[name="SignUpProviderForm[password]"]', 'asDF@#12asdf');
        $I->uncheckOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

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
     * @param $I FunctionalTester
     */
    private function testSignUpWithIncorrectUsername($I)
    {
        $I->wantTo('ensure that sign-up-provider is not allowed with username containing non alphanumeric or alphabetic symbols');
        $I->amOnRoute('site/sign-up-provider');
        $I->see('Sign Up', '.btn-primary');
        $I->see('Sign up as a specialist');

        //-- submit sign-up-provider form with incorrect username data --//
        $I->amGoingTo('submit sign-up-provider form with username containing underscore');
        $I->fillField('input[name="SignUpProviderForm[username]"]', 'demo_first_provider');
        $I->fillField('input[name="SignUpProviderForm[email]"]', 'demoProvider@example.com');
        $I->fillField('input[name="SignUpProviderForm[password]"]', 'asDF@#12asdf');
        $I->checkOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

        $I->expectTo('see an error that username should contain alphanumeric or alphabetic symbols');
        $I->dontSee('Username cannot be blank.', '.help-block');
        $I->dontSee('Password cannot be blank.', '.help-block');
        $I->dontSee('Email is not a valid email address.', '.help-block');
        $I->see('The username should contain only alphanumeric or alphabetic characters.', '.help-block');

        $I->expectTo('see that user is not logged in');
        $I->seeInCurrentUrl('sign-up-provider');
        $I->dontSeeLink('Logout (demo_first_provider)');

        $I->amGoingTo('submit sign-up-provider form with username containing space');
        $I->fillField('input[name="SignUpProviderForm[username]"]', 'demo first provider');
        $I->fillField('input[name="SignUpProviderForm[email]"]', 'demoProvider@example.com');
        $I->fillField('input[name="SignUpProviderForm[password]"]', 'asDF@#12asdf');
        $I->checkOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
        $I->click('sign-up-button');

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