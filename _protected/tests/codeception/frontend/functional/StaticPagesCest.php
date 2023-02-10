<?php

namespace tests\codeception\frontend\functional;

use Yii;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use Codeception\Scenario;
use tests\codeception\frontend\_pages\ContactPage;
use tests\codeception\frontend\FunctionalTester;

class StaticPagesCest
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
     * Test home page.
     *
     * @param $I FunctionalTester
     * @param $scenario Scenario
     */
    public function testHomePage($I, $scenario)
    {
        $I->wantTo('ensure that home page works');
        $I->amOnRoute(Yii::$app->homeUrl);
        $I->see(Yii::$app->name);
        $I->seeLink('Sign Up as Client');
        $I->click('Sign Up as Client');
        $I->canSeeInCurrentUrl('/sign-up');

        $I->seeLink('Home');
        $I->click('Home');
        $I->seeLink('Sign Up as Specialist');
        $I->click('Sign Up as Specialist');
        $I->canSeeInCurrentUrl('/sign-up-provider');

        $I->seeLink('Home');
        $I->click('Home');
        $I->seeLink('Sign Up as Specialist');
        $I->seeLink('Login your account');
        $I->click('Login your account');
        $I->see('Sign in to continue');

        $I->seeLink('Home');
        $I->click('Home');
        $I->seeLink('Find a in your location');
        $I->click('Find a in your location');
        $I->canSeeInCurrentUrl('/search');
    }

    /**
     * Test about page.
     *
     * @param $I FunctionalTester
     * @param $scenario Scenario
     */
    public function testAboutPage($I, $scenario)
    {
        $I->wantTo('ensure that about page works');
        $I->amOnRoute('site/about');
        $I->see('About Us', 'h1');
    }

    /**
     * Test contact page.
     *
     * @param $I FunctionalTester
     * @param $scenario Scenario
     */
    public function testContact($I, $scenario)
    {
        $I->wantTo('ensure that contact works');
        $contactPage = ContactPage::openBy($I);
        $I->see('Contact Us', 'h1');

        //-- submit form with no data --//
        $I->amGoingTo('submit contact form with no data');
        $contactPage->submit([]);

        $I->expectTo('see validations errors');
        $I->see('Contact Us', 'h1');
        $I->see('Name cannot be blank.');
        $I->see('Email cannot be blank.');
        $I->see('Subject cannot be blank.');
        $I->see('Text cannot be blank.');
        $I->see('Verification Code cannot be blank.');

        //-- submit form with not correct email --//
        $I->amGoingTo('submit contact form with not correct email');
        $contactPage->submit([
            'name'       => 'tester',
            'email'      => 'tester.email',
            'subject'    => 'test subject',
            'body'       => 'test content',
            'verifyCode' => 'testme',
        ]);

        $I->expectTo('see that email adress is wrong');
        $I->dontSee('Name cannot be blank', '.help-inline');
        $I->see('Email is not a valid email address.');
        $I->dontSee('Subject cannot be blank', '.help-inline');
        $I->dontSee('Body cannot be blank', '.help-inline');
        $I->dontSee('The verification code is incorrect', '.help-inline');

        //-- submit form with correct data --//
        $I->amGoingTo('submit contact form with correct data');
        $contactPage->submit([
            'name'       => 'tester',
            'email'      => 'tester@example.com',
            'subject'    => 'test subject',
            'body'       => 'test content',
            'verifyCode' => 'testme',
        ]);

        $I->see('Thank you for contacting us. We will respond to you as soon as possible.');
    }
}
