<?php

namespace tests\codeception\frontend\acceptance;

use Yii;
use Exception;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use Codeception\Scenario;
use tests\codeception\frontend\_pages\ContactPage;
use tests\codeception\frontend\AcceptanceTester;

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
     * @param AcceptanceTester $I
     * @param Scenario $scenario
     */
    public function testNotExistingPage($I, $scenario)
    {
        $I->amGoingTo('open page that not exist in our system');
        $I->amOpeningPage('site/not_exist');

        $I->expectTo('see message about page not found');
        $I->see('Page not found.', '.alert-danger');
    }

    /**
     * Test home page.
     *
     * @param $I AcceptanceTester
     * @param $scenario Scenario
     * @throws Exception
     */
    public function testHomePage($I, $scenario)
    {
        $I->wantTo('ensure that home page works');

        $I->amOpeningPage(Yii::$app->homeUrl);
        $I->see(Yii::$app->name);
        $I->seeLink('Sign Up as Client');
        $I->click('Sign Up as Client');
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3);
        }
        $I->canSeeInCurrentUrl('/sign-up');

        $I->seeLink('Home');
        $I->click('Home');
        if (method_exists($I, 'wait')) {
            $I->waitForText('Sign Up as Specialist', 3);
        }
        $I->seeLink('Sign Up as Specialist');
        $I->click('Sign Up as Specialist');
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3);
        }
        $I->canSeeInCurrentUrl('/sign-up-provider');

        $I->seeLink('Home');
        $I->click('Home');
        if (method_exists($I, 'wait')) {
            $I->waitForText('Login your account', 3);
        }
        $I->seeLink('Login your account');
        $I->click('Login your account');
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3);
        }
        $I->see('Sign in to continue');

        $I->seeLink('Home');
        $I->click('Home');
        if (method_exists($I, 'wait')) {
            $I->waitForText('Find a in your location', 3);
        }
        $I->cantSee('My Requests');
        $I->seeLink('Find a in your location');
        $I->click('Find a in your location');
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3);
        }
        $I->canSee('Search', 'button');
    }

    /**
     * Test about page.
     *
     * @param $I AcceptanceTester
     * @param $scenario Scenario
     */
    public function testAboutPage($I, $scenario)
    {
        $I->wantTo('ensure that about page works');
        $I->amOpeningPage('site/about');
        $I->see('About Us', 'h1');
    }

    /**
     * Test contact page.
     *
     * @param $I AcceptanceTester
     * @param $scenario Scenario
     */
    public function testContact($I, $scenario)
    {
        $I->wantTo('ensure that contact works');
        $contactPage = ContactPage::openBy($I);
        $I->see('Contact', 'h1');

        //-- submit form with no data --//
        $I->amGoingTo('submit contact form with no data');
        $contactPage->submit([]);
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3); // only for selenium
        }
        $I->expectTo('see validations errors');
        $I->see('Contact', 'h1');
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
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3); // only for selenium
        }
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
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3); // only for selenium
        }       
        $I->see('Thank you for contacting us. We will respond to you as soon as possible.');
    }
}

