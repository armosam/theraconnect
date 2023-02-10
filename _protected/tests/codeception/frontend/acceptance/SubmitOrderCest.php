<?php

namespace tests\codeception\frontend\acceptance;

use tests\codeception\frontend\FunctionalTester;
use Yii;
use Exception;
use tests\codeception\frontend\AcceptanceTester;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\User;
use common\models\LoginAttempt;


/**
 * Class SubmitOrderCest
 * @package tests\codeception\frontend\functional
 * @group order
 * @group submit_order_acceptance
 */
class SubmitOrderCest
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
     */
    public function testSearchPageWithProvider($I)
    {
        $I->wantTo('open search page with login as customer');
        $I->amOpeningPage('search/index', ['username' => 'member', 'password' => 'member123']);

        $I->expectTo('see specialist data on the search page');
        $I->see('Advanced Search', '#providersearch-advanced_search');
        $I->see('Provider User2', 'a[href*="search/10"] div h4');
        $I->see('Birth Service', 'div[data-key="10"] div');
        $I->seeLink('Request Service', 'order/submit/10');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testSubmitOrderFormForLoggedInCustomer($I)
    {
        $I->wantTo('ensure that service request form contains necessary fields for logged in customer');
        $I->amOpeningPage('search/index', ['username' => 'member', 'password' => 'member123']);

        $I->am(User::USER_CUSTOMER);

        $I->seeLink('Request Service', 'order/submit/10');
        $I->click(['link' => 'Request Service', 'css' => 'a[href$="order/submit/10"]']);
        if (method_exists($I, 'wait')) {
            $I->waitForText('Your information might be shared with service provider', 3);
        }

        $I->expectTo('see service request form opened with necessary fields');
        $I->canSee('Your information might be shared with service provider', 'h3.panel-title');
        $I->canSee('Please provide your current information and be informed that by submitting service request to selected specialist your following information might be shared with specialist ', 'div.hint-block');

        $I->canSee('Service Name', '.control-label');
        $I->seeElement('select#submitorderform-service_id');

        $I->canSee('Service Start', '.control-label');
        $I->canSeeElement('input#submitorderform-order_start-disp');

        $I->canSee('Service End', '.control-label');
        $I->canSeeElement('input#submitorderform-order_end-disp');

        $I->canSee('Service Location (Hospital name, address)', '.control-label');
        $I->canSeeElement('input#submitorderform-order_address');

        $I->canSee('Verification Code', '.control-label');
        $I->canSeeElement('input#submitorderform-verifycode');
        $I->canSeeElement('img#submitorderform-verifycode-image');

        $I->canSee('Secondary Phone Number', '.control-label');
        $I->canSeeElement('input#submitorderform-phone2');

        $I->cantSee('First Name', '.control-label');
        $I->cantSeeElement('input#submitorderform-first_name');

        $I->cantSee('Last Name', '.control-label');
        $I->cantSeeElement('input#submitorderform-last_name');

        $I->cantSee('Email', '.control-label');
        $I->cantSeeElement('input#submitorderform-email');

        $I->cantSee('Primary Phone Number', '.control-label');
        $I->cantSeeElement('input#submitorderform-phone1');

        $I->cantSee('Password', '.control-label');
        $I->cantSeeElement('input#submitorderform-password');

        $I->see('Submit Request', 'button[type="submit"]');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testSubmitOrderFormForGuest($I)
    {
        $I->wantTo('open search page for guest');
        $I->amOpeningPage('search/index', []);

        $I->seeLink('Request Service', 'order/submit/10');
        $I->click(['link' => 'Request Service', 'css' => 'a[href$="order/submit/10"]']);
        if (method_exists($I, 'wait')) {
            $I->waitForText('Your information might be shared with service provider', 3);
        }

        $I->expectTo('see service request form opened with all fields');
        $I->canSee('Your information might be shared with service provider', 'h3.panel-title');
        $I->canSee('Please provide your current information and be informed that by submitting service request to selected specialist your following information might be shared with specialist ', 'div.hint-block');

        $I->canSee('Service Name', '.control-label');
        $I->seeElement('select#submitorderform-service_id');

        $I->canSee('Service Start', '.control-label');
        $I->canSeeElement('input#submitorderform-order_start-disp');

        $I->canSee('Service End', '.control-label');
        $I->canSeeElement('input#submitorderform-order_end-disp');

        $I->canSee('Service Location (Hospital name, address)', '.control-label');
        $I->canSeeElement('input#submitorderform-order_address');

        $I->canSee('Verification Code', '.control-label');
        $I->canSeeElement('input#submitorderform-verifycode');
        $I->canSeeElement('img#submitorderform-verifycode-image');

        $I->canSee('Secondary Phone Number', '.control-label');
        $I->canSeeElement('input#submitorderform-phone2');

        $I->canSee('First Name', '.control-label');
        $I->canSeeElement('input#submitorderform-first_name');

        $I->canSee('Last Name', '.control-label');
        $I->canSeeElement('input#submitorderform-last_name');

        $I->canSee('Email', '.control-label');
        $I->canSeeElement('input#submitorderform-email');

        $I->canSee('Primary Phone Number', '.control-label');
        $I->canSeeElement('input#submitorderform-phone1');

        $I->canSee('Please provide your password to create your account automatically.', '.hint-block');
        $I->canSee('login to continue', '.hint-block');
        $I->canSeeElement('input#submitorderform-password');

        $I->see('Submit Request', 'button[type="submit"]');
    }

    /**
     * As it opens in modal window so it needs javascript
     * That's why this test case works only in webdriver
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testSubmitOrderFormValidationForLoggedInCustomer($I)
    {
        $I->wantTo('open search page for logged customer');
        $I->amOpeningPage('search/index', ['username' => 'member', 'password' => 'member123']);

        $I->am(User::USER_CUSTOMER);

        $I->wantTo('open service request form for provider #10');
        $I->click(['link' => 'Request Service', 'css' => 'a[href$="order/submit/10"]']);
        if (method_exists($I, 'wait')) {
            $I->waitForText('Your information might be shared with service provider', 3);
        }

        $I->expectTo('see that some required fields not on the form because customer has on the profile');
        $I->canSeeElement('select[name="SubmitOrderForm[service_id]"]');
        $I->canSeeElement('#submitorderform-order_start-disp');
        $I->canSeeElement('#submitorderform-order_end-disp');
        $I->canSeeElement('input[name="SubmitOrderForm[order_address]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[verifyCode]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[phone2]"]');
        $I->cantSeeElement('input[name="SubmitOrderForm[first_name]"]');
        $I->cantSeeElement('input[name="SubmitOrderForm[last_name]"]');
        $I->cantSeeElement('input[name="SubmitOrderForm[email]"]');
        $I->cantSeeElement('input[name="SubmitOrderForm[phone1]"]');
        $I->cantSeeElement('input[name="SubmitOrderForm[password]"]');

        if (method_exists($I, 'wait')) {
            $I->wantTo('submit service request form without data for logged customer');
            $I->selectOption('SubmitOrderForm[service_id]', ['value' => '1']);
            $I->fillField('#submitorderform-order_start-disp', '');
            $I->fillField('#submitorderform-order_end-disp', '');
            $I->fillField('SubmitOrderForm[order_address]', '');
            $I->fillField('SubmitOrderForm[verifyCode]', '');
            $I->fillField('SubmitOrderForm[phone2]', '');
            $I->click('Submit Request');

            $I->waitForElement('button[class="btn btn-warning"]', 3); // only for selenium
            $I->click('button[class="btn btn-warning"]');
            $I->waitForText('Your information might be shared with service provider', 3); // only for selenium

            $I->expectTo('see error messages for required fields on the form');
            $I->cantSee('Service Name cannot be blank.', '.help-block');
            $I->canSee('Service Start cannot be blank.', '.help-block');
            $I->canSee('Service End cannot be blank.', '.help-block');
            $I->canSee('Verification Code cannot be blank.', '.help-block');
            $I->dontSee('First Name cannot be blank.', '.help-block');
            $I->dontSee('Last Name cannot be blank.', '.help-block');
            $I->dontSee('Email cannot be blank.', '.help-block');
            $I->dontSee('Primary Phone Number cannot be blank.', '.help-block');
            $I->dontSee('Secondary Phone Number cannot be blank.', '.help-block');
            $I->dontSee('Password cannot be blank.', '.help-block');
        }
    }

    /**
     * As it opens in modal window so it needs javascript
     * That's why this test case works only in webdriver
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testSubmitOrderFormValidationForGuest($I)
    {
        $I->wantTo('open search page for guest');
        $I->amOpeningPage('search/index');

        $I->wantTo('open form for provider #10 and check fields');
        $I->click(['link' => 'Request Service', 'css' => 'a[href$="order/submit/10"]']);
        if (method_exists($I, 'wait')) {
            $I->waitForText('Your information might be shared with service provider', 3);
        }

        $I->expectTo('see service request form with all fields');
        $I->canSeeElement('select[name="SubmitOrderForm[service_id]"]');
        $I->canSeeElement('#submitorderform-order_start-disp');
        $I->canSeeElement('#submitorderform-order_end-disp');
        $I->canSeeElement('input[name="SubmitOrderForm[order_address]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[verifyCode]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[first_name]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[last_name]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[email]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[phone1]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[phone2]"]');
        $I->canSeeElement('input[name="SubmitOrderForm[password]"]');

        if (method_exists($I, 'wait')) {
            $I->wantTo('submit service request form without data for guest');
            $I->selectOption('SubmitOrderForm[service_id]', ['value' => '1']);
            $I->fillField('#submitorderform-order_start-disp', '');
            $I->fillField('#submitorderform-order_end-disp', '');
            $I->fillField('SubmitOrderForm[order_address]', '');
            $I->fillField('SubmitOrderForm[verifyCode]', '');
            $I->fillField('SubmitOrderForm[first_name]', '');
            $I->fillField('SubmitOrderForm[last_name]', '');
            $I->fillField('SubmitOrderForm[email]', '');
            $I->fillField('SubmitOrderForm[phone1]', '');
            $I->fillField('SubmitOrderForm[phone2]', '');
            $I->fillField('SubmitOrderForm[password]', '');
            $I->click('Submit Request');

            $I->waitForElement('button[class="btn btn-warning"]', 3); // only for selenium
            $I->click('button[class="btn btn-warning"]');
            $I->waitForText('Your information might be shared with service provider', 3); // only for selenium

            $I->expectTo('see error messages for required fields on the form');
            $I->cantSee('Service Name cannot be blank.', '.help-block');
            $I->canSee('Service Start cannot be blank.', '.help-block');
            $I->canSee('Service End cannot be blank.', '.help-block');
            $I->canSee('Verification Code cannot be blank.', '.help-block');
            $I->canSee('First Name cannot be blank.', '.help-block');
            $I->canSee('Last Name cannot be blank.', '.help-block');
            $I->canSee('Email cannot be blank.', '.help-block');
            $I->canSee('Primary Phone Number cannot be blank.', '.help-block');
            $I->cantSee('Secondary Phone Number cannot be blank.', '.help-block');
            $I->canSee('Password cannot be blank.', '.help-block');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testSubmitOrderForGuest($I)
    {
        $I->wantToTest('service request submission for guest');
        $I->amOpeningPage(['order/submit', 'id' => 10]);

        $I->amGoingTo('submit service request from guest customer');
        $I->submitForm('#service_request_form', ['SubmitOrderForm' => [
            'service_id' => '1',
            'order_start' => '2020-01-01 10:00:00',
            'order_end' => '2020-01-01 11:00:00',
            'order_address' => 'Test Address',
            'verifyCode' => 'testme',
            'first_name' => 'Tested',
            'last_name' => 'User',
            'email' => 'newtestuser@user.com',
            'phone1' => '+18889991234',
            'phone2' => '',
            'password' => 'user123',
        ]], 'Submit Request');

        $I->expectTo('see messages that new customer user created and request could be sent to provider after account activation');
        $I->see('Notification about service request is unable to send to the specialist. Please first activate your account.', '.alert-danger');
        $I->see('Please activate your account to make your request visible for specialist Provider User2. Check your email to activate your account.', '.alert-success');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testSubmitOrderForLoggedCustomer($I)
    {
        $I->wantToTest('service request submission for logged member user');
        $I->amOpeningPage(['order/submit', 'id' => 10], ['username' => 'member', 'password' => 'member123']);

        $I->amGoingTo('submit service request from logged in customer');
        $I->submitForm('#service_request_form', ['SubmitOrderForm' => [
            'service_id' => '1',
            'order_start' => '2020-01-01 10:00:00',
            'order_end' => '2020-01-01 11:00:00',
            'order_address' => 'Test Address',
            'verifyCode' => 'testme',
            'phone2' => '',
        ]], 'Submit Request');

        $I->expectTo('see message that service request submitted successfully and provider will contact soon');
        $I->dontSee('Notification about service request is unable to send to the specialist. Please first activate your account.', '.alert-danger');
        $I->dontSee('Please activate your account to make your request visible for specialist Provider User2. Check your email to activate your account.', '.alert-success');
        $I->see('Your service request successfully submitted. Specialist Provider User2 will check your request and respond you back.', '.alert-success');
    }

}