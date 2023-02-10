<?php

namespace tests\codeception\frontend\functional;

use common\models\ChangeHistory;
use Yii;
use Exception;
use DateInterval;
use DateTimeZone;
use tests\codeception\frontend\FunctionalTester;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\Order;
use common\models\base\ServiceCategory;
use common\models\UserCredential;
use common\models\CredentialType;
use common\models\Service;
use common\models\User;
use common\models\UserLanguage;
use common\models\UserQualification;
use common\models\UserService;
use common\rbac\models\Role;


/**
 * Class SubmitOrderCest
 * @package tests\codeception\frontend\functional
 * @group order
 * @group submit_order_functional
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
     * @param FunctionalTester $I
     */
    public function testNecessaryData($I)
    {
        $I->wantTo('ensure that necessary data is loaded in the database');
        $I->seeRecord(User::class, ['id' => 10, 'status' => 'A']);
        $I->seeRecord(Role::class, ['user_id' => 10, 'item_name' => 'provider']);

        $I->seeRecord(ServiceCategory::class, ['id' => 1, 'status' => 'A']);
        $I->seeRecord(Service::class, ['id' => 1, 'service_category_id' => 1, 'status' => 'A']);
        $I->seeRecord(UserService::class, ['user_id' => 10, 'service_id' => 1]);

        $I->seeRecord(CredentialType::class, ['id' => 1, 'status' => 'A']);
        $I->seeRecord(UserCredential::class, ['id' => 1, 'qualification_category_id' => 1, 'status' => 'A']);
        $I->seeRecord(UserQualification::class, ['user_id' => 10, 'qualification_id' => 1]);

        $I->seeRecord(UserLanguage::class, ['user_id' => 10, 'language_code' => 'hy']);
    }

    /**
     * @param FunctionalTester $I
     */
    public function testSearchPageWithProvider($I)
    {
        $I->wantTo('ensure that search page contains only allowed specialists (only Provider User #4 and Provider User2 #10)');
        $I->amOnRoute('site/index');
        $I->click('Find Your Specialist');
        $I->expectTo('see specialists (who has non empty email, first name, last name, phone1 and address) on the search page');
        $I->canSeeNumberOfElements('.provider-item', 2);
        $I->see('Advanced Search', '#providersearch-advanced_search');
        $I->see('Provider User', 'a[href*="search/4"] >div >h4');
        $I->see('Birth Service', 'div[data-key="4"] div');
        $I->seeLink('Request Service', '/order/submit/4');
        $I->see('Provider User2', 'a[href*="search/10"] >div >h4');
        $I->see('Birth Service', 'div[data-key="10"] div');
        $I->seeLink('Request Service', '/order/submit/10');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testSubmitOrderFormForLoggedInCustomer($I)
    {
        $I->wantTo('ensure that service request form contains necessary fields for logged in customer');
        $I->amLoggedInAs(3); // member
        $I->am(User::USER_CUSTOMER);
        $I->amOnRoute('search/index');
        $I->canSee('Logout (member)', '.dropdown-menu');

        $I->seeLink('Request Service', '/order/submit/10');
        $I->click('Request Service', 'a[href$="order/submit/10"]');

        $I->expectTo('see service request form opened with necessary fields');
        $I->canSee('Your information might be shared with service provider', '.panel-title');
        $I->canSee('Please provide your current information and be informed that by submitting service request to selected specialist your following information might be shared with specialist ', '.hint-block');

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
     * @param FunctionalTester $I
     */
    public function testSubmitOrderFormForGuest($I)
    {
        $I->wantTo('ensure that service request form contains necessary fields for guest customer');
        $I->amOnRoute('search/index');
        $I->cantSee('Logout', '.dropdown-menu');
        $I->canSee('Login', '.navbar-nav');

        $I->seeLink('Request Service', '/order/submit/10');
        $I->click('Request Service', 'a[href$="order/submit/10"]');

        $I->expectTo('see service request form opened with all fields');
        $I->canSee('Your information might be shared with service provider', '.panel-title');
        $I->canSee('Please provide your current information and be informed that by submitting service request to selected specialist your following information might be shared with specialist ', '.hint-block');

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
     * @param FunctionalTester $I
     */
    public function testSubmitOrderForGuest($I)
    {
        $I->wantToTest('service request submission for guest');
        $I->amOnRoute('order/submit', ['id'=>10]);

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

        /** @var User $new_customer */
        $new_customer = $I->grabRecord(User::class, ['email' => 'newtestuser@user.com', 'status' => User::USER_STATUS_NOT_ACTIVATED]);

        $I->expectTo('see messages that new customer user created and request could be sent to provider after account activation');
        $I->see('Notification about service request is unable to send to the specialist. Please first activate your account.', '.alert-danger');
        $I->see('Please activate your account to make your request visible for specialist Provider User2. Check your email to activate your account.', '.alert-success');

        $I->expectTo('see new customer user created by email newtestuser@user.com, status as not activated and phone1 not changed');
        $I->seeRecord(User::class, ['email' => 'newtestuser@user.com', 'phone1' => null, 'phone2' => null, 'first_name' => 'Tested', 'last_name' => 'User', 'status' => User::USER_STATUS_NOT_ACTIVATED]);

        $I->expectTo('see new record in the ChangeHistory for verification of phone1');
        $I->dontSeeRecord(ChangeHistory::class, ['user_id' => $new_customer->id, 'field_name' => 'email', 'new_value' => 'newtestuser@user.com', 'status' => 'Y']);
        $I->seeRecord(ChangeHistory::class, ['user_id' => $new_customer->id, 'field_name' => 'phone1', 'new_value' => '+18889991234', 'status' => 'Y']);
        $I->dontSeeRecord(ChangeHistory::class, ['user_id' => $new_customer->id, 'field_name' => 'phone2', 'status' => 'Y']);

        $I->expectTo('see new order record for service request saved in the database');
        $I->seeRecord(Order::class, ['customer_id' => $new_customer->id, 'provider_id' => 10, 'service_id' => '1', 'order_address' => 'Test Address']);

        $I->expectTo('see 2 email notifications has been sent');
        $I->seeEmailIsSent(2);

        $I->expectTo('see emails contains correct information');
        $emails = $I->grabSentEmails();
        $I->assertIsArray($emails);
        $I->assertEquals(2, count($emails));
        $I->assertArrayHasKey(0, $emails);

        /** @var yii\swiftmailer\Message $email1 */
        $email1 = $emails[0];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email1->getFrom());
        $I->assertEquals([$new_customer->email => $new_customer->getUserFullName()], $email1->getTo());
        $I->assertNull($email1->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email1->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email1->getBcc());
        $I->assertEquals('Your account has been created.', $email1->getSubject());
        $I->assertStringContainsString('Your account has been created.', $I->amNormalizingEmailContent($email1->toString()));

        $I->assertIsArray($emails);
        $I->assertArrayHasKey(1, $emails);

        /** @var yii\swiftmailer\Message $email2 */
        $email2 = $emails[1];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email2->getFrom());
        $I->assertEquals([$new_customer->email => $new_customer->getUserFullName()], $email2->getTo());
        $I->assertNull($email2->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email2->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email2->getBcc());
        $I->assertEquals('Account activation for THERA Connect', $email2->getSubject());
        $I->assertStringContainsString('Follow this link to activate your account', $I->amNormalizingEmailContent($email2->toString()));
        $I->assertStringContainsString($new_customer->account_activation_token, $I->amNormalizingEmailContent($email2->toString()));
    }

    /**
     * @param FunctionalTester $I
     * @throws Exception
     */
    public function testActivateAccountCreatedWhenSubmittedRequest($I)
    {
        $I->wantToTest('account activation when account created during service request submission');
        $I->expectTo('see account activated, notification about request was sent to provider if time is in limits');

        $I->amGoingTo('submit service request from guest customer');
        $I->amOnRoute('order/submit', ['id' => 10]);
        $I->submitForm('#service_request_form', ['SubmitOrderForm' => [
            'service_id' => '1',
            'order_start' => date_create('now', new DateTimeZone(Yii::$app->timeZone))->add(new DateInterval('P3D'))->format('Y-m-d H:i:s'),
            'order_end' => date_create('now', new DateTimeZone(Yii::$app->timeZone))->add(new DateInterval('P2DT23H'))->format('Y-m-d H:i:s'),
            'order_address' => 'Test Address',
            'verifyCode' => 'testme',
            'first_name' => 'Tested',
            'last_name' => 'User',
            'email' => 'newnewtestuser@user.com',
            'phone1' => '+18889994321',
            'phone2' => '',
            'password' => 'user123',
        ]], 'Submit Request');

        $I->expectTo('see messages that new customer user created and request could be sent to provider after account activation');
        $I->see('Notification about service request is unable to send to the specialist. Please first activate your account.', '.alert-danger');
        $I->see('Please activate your account to make your request visible for specialist Provider User2. Check your email to activate your account.', '.alert-success');

        $I->expectTo('see user record created and order record crested in the database');
        $I->seeRecord(User::class, ['email' => 'newnewtestuser@user.com', 'status' => User::USER_STATUS_NOT_ACTIVATED]);
        /** @var User $new_customer */
        $new_customer = $I->grabRecord(User::class, ['email' => 'newnewtestuser@user.com', 'status' => User::USER_STATUS_NOT_ACTIVATED]);
        /** @var User $provider */
        $provider = $I->grabRecord(User::class, ['id' => 10]);
        $I->seeRecord(Order::class, ['customer_id' => $new_customer->id, 'provider_id' => $provider->id, 'service_id' => 1, 'status' => Order::ORDER_STATUS_SUBMITTED]);

        $I->wantTo('click on activation link in the received email message and activate account');
        $I->amOnRoute('site/activate-account', ['token' => $new_customer->account_activation_token]);

        $I->expectTo('see message on about successfully activation');
        $I->see(Yii::t('app', 'You Successfully activated your account. Thank you {user} for joining us!', ['user' => $new_customer->username]), '.alert-success');

        $I->expectTo('see 5 email notifications has been sent');
        $I->seeEmailIsSent(5);

        $I->expectTo('see emails contains correct information');
        $emails = $I->grabSentEmails();
        $I->assertIsArray($emails);
        $I->assertEquals(5, count($emails));

        $I->expectTo('see email about success customer creation');
        $I->assertArrayHasKey(0, $emails);

        /** @var yii\swiftmailer\Message $email1 */
        $email1 = $emails[0];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email1->getFrom());
        $I->assertEquals([$new_customer->email => $new_customer->getUserFullName()], $email1->getTo());
        $I->assertNull($email1->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email1->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email1->getBcc());
        $I->assertEquals('Your account has been created.', $email1->getSubject());
        $I->assertStringContainsString('Your account has been created.', $I->amNormalizingEmailContent($email1->toString()));

        $I->expectTo('see email about success sending of account activation email');
        $I->assertArrayHasKey(1, $emails);

        /** @var yii\swiftmailer\Message $email2 */
        $email2 = $emails[1];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email2->getFrom());
        $I->assertEquals([$new_customer->email => $new_customer->getUserFullName()], $email2->getTo());
        $I->assertNull($email2->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email2->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email2->getBcc());
        $I->assertEquals('Account activation for THERA Connect', $email2->getSubject());
        $I->assertStringContainsString('Follow this link to activate your account', $I->amNormalizingEmailContent($email2->toString()));
        $I->assertStringContainsString($new_customer->account_activation_token, $I->amNormalizingEmailContent($email2->toString()));

        $I->expectTo('see email about success account activation');
        $I->assertArrayHasKey(2, $emails);

        /** @var yii\swiftmailer\Message $email3 */
        $email3 = $emails[2];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email3->getFrom());
        $I->assertEquals([$new_customer->email => $new_customer->getUserFullName()], $email3->getTo());
        $I->assertNull($email3->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email3->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email3->getBcc());
        $I->assertEquals('Your account has been activated.', $email3->getSubject());
        $I->assertStringContainsString(Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'User')]), $I->amNormalizingEmailContent($email3->toString()));
        $I->assertStringContainsString('Your account has been activated successfully. Please login and fill your profile.', $I->amNormalizingEmailContent($email3->toString()));

        $I->expectTo('see email about success submission of request to the provider after customer activation');
        $I->assertArrayHasKey(3, $emails);

        /** @var yii\swiftmailer\Message $email4 */
        $email4 = $emails[3];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email4->getFrom());
        $I->assertEquals([$provider->email => $provider->getUserFullName()], $email4->getTo());
        $I->assertNull($email4->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email4->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email4->getBcc());
        $I->assertEquals('You have a new service request.', $email4->getSubject());
        $I->assertStringContainsString(Yii::t('app', 'Dear {name},', ['name'=> $provider->getUserFullName()]), $I->amNormalizingEmailContent($email4->toString()));
        $I->assertStringContainsString(Yii::t('app', 'My name is {name}.', ['name'=> $new_customer->getUserFullName()]), $I->amNormalizingEmailContent($email4->toString()));
        $I->assertStringContainsString('I found your details from THERA Connect web site and I contact you for your services.', $I->amNormalizingEmailContent($email4->toString()));

        $I->expectTo('see email about success service request submission to the customer after customer activation');
        $I->assertArrayHasKey(4, $emails);

        /** @var yii\swiftmailer\Message $email5 */
        $email5 = $emails[4];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email5->getFrom());
        $I->assertEquals([$new_customer->email => $new_customer->getUserFullName()], $email5->getTo());
        $I->assertNull($email5->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email5->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email5->getBcc());
        $I->assertEquals('Your service request has been submitted.', $email5->getSubject());
        $I->assertStringContainsString(Yii::t('app', 'Dear {name}', ['name'=> $new_customer->getUserFullName()]), $I->amNormalizingEmailContent($email5->toString()));
        $I->assertStringContainsString(Yii::t('app', 'Your service request to specialist {provider} has been submitted.', ['provider' => $provider->getUserFullName()]), $I->amNormalizingEmailContent($email5->toString()));

    }

        /**
     * @param FunctionalTester $I
     */
    public function testSubmitOrderForLoggedCustomer($I)
    {
        $I->wantToTest('service request submission for logged user');
        $I->amLoggedInAs(3);
        $I->am(User::USER_CUSTOMER);

        $I->amGoingTo('submit service request from logged in customer');
        $I->amOnRoute('order/submit', ['id'=>10]);
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

        $I->expectTo('see new order record for service request saved in the database');
        $I->seeRecord(Order::class, ['customer_id' => 3, 'provider_id' => 10, 'service_id' => 1, 'order_address' => 'Test Address']);
        /** @var User $provider */
        $provider = $I->grabRecord(User::class, ['id' => 10]);
        /** @var User $customer */
        $customer = $I->grabRecord(User::class, ['id' => 3]);

        $I->expectTo('see 2 email messages has been sent');
        $I->seeEmailIsSent(2);

        $I->wantTo('see emails contains correct information');
        $emails = $I->grabSentEmails();
        $I->assertIsArray($emails);
        $I->assertEquals(2, count($emails));

        $I->assertArrayHasKey(0, $emails);

        /** @var yii\swiftmailer\Message $email0 */
        $email0 = $emails[0];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email0->getFrom());
        $I->assertEquals([$provider->email => $provider->getUserFullName()], $email0->getTo());
        $I->assertNull($email0->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email0->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email0->getBcc());
        $I->assertEquals('You have a new service request.', $email0->getSubject());
        $I->assertStringContainsString(Yii::t('app', 'Dear {name}', ['name' => $provider->getUserFullName()]), $I->amNormalizingEmailContent($email0->toString()));
        $I->assertStringContainsString(Yii::t('app', 'My name is {name}.', ['name' => $customer->getUserFullName()]), $I->amNormalizingEmailContent($email0->toString()));

        $I->assertArrayHasKey(1, $emails);

        /** @var yii\swiftmailer\Message $email1 */
        $email1 = $emails[1];
        $I->assertEquals([Yii::$app->params['fromEmailAddress'] => 'THERA Connect Support'], $email1->getFrom());
        $I->assertEquals([$customer->email => $customer->getUserFullName()], $email1->getTo());
        $I->assertNull($email1->getCc());
        $I->assertEquals([Yii::$app->params['supportEmail'] => 'THERA Connect Support'], $email1->getReplyTo());
        $I->assertArrayHasKey(Yii::$app->params['systemNotificationEmailAddress'], $email1->getBcc());
        $I->assertEquals('Your service request has been submitted.', $email1->getSubject());
        $I->assertStringContainsString(Yii::t('app', 'Dear {name},', ['name' => $customer->getUserFullName()]), $I->amNormalizingEmailContent($email1->toString()));
        $I->assertStringContainsString(Yii::t('app', 'Your service request to specialist {provider} has been submitted.', ['provider' => $provider->getUserFullName()]), $I->amNormalizingEmailContent($email1->toString()));
    }
}