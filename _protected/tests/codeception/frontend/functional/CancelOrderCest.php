<?php

namespace tests\codeception\frontend\functional;

use Yii;
use common\models\Order;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use tests\codeception\frontend\FunctionalTester;

/**
 * Class CancelOrderCest
 * @package tests\codeception\frontend\functional
 * @group order
 * @group cancel_order_functional
 */
class CancelOrderCest
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
    public function testCancelOrderWindow($I)
    {
        $I->wantTo('open cancel request modal window for provider');
        $I->amGoingTo('login as Provider User #4 and open my requests page');
        $I->amLoggedInAs(4);
        $I->amOnRoute('order/index');

        $I->expectTo('see only accepted order with Cancel Order action button on the grid');
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-c', 1);
        $I->seeElement('table tr[data-key="3"] td a[title="Cancel Request"][href$="order/cancel/3"]');
        $I->dontSeeElement('table tr[data-key="1"] td a[title="Cancel Request"][href$="order/cancel/1"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Cancel Request"][href$="order/cancel/4"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Cancel Request"][href$="order/cancel/7"]');

        $I->amGoingTo('click on Cancel Request action button');
        $I->click('table tr[data-key="3"] td a[title="Cancel Request"][href$="order/cancel/3"]');

        $I->expectTo('see order cancel modal window with messages and button');
        $I->see('Information', 'h3');
        $I->see('Please be informed that by canceling this request customer and specialist might receive notification about cancellation. Select bellow select box that better describes reason of cancellation.', 'h4');
        $I->seeElement('#cancelorderform-cancellation_reason');
        $I->see('Cancel Request', 'button[type="submit"]');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testCancelOrder($I)
    {
        $I->wantToTest('cancel order functionality for provider');
        $I->amGoingTo('login as Provider User #4 and open my requests page');
        $I->amLoggedInAs(4);
        $I->amOnRoute('order/index');

        $I->expectTo('see order record status accepted with none filled cancellation data');
        $I->seeRecord(Order::class, ['id' => 3, 'status' => Order::ORDER_STATUS_ACCEPTED, 'canceled_by' => null, 'cancellation_reason' => null]);

        $I->amGoingTo('open Cancel Request modal window and cancel request');
        $I->click('table tr[data-key="3"] td a[title="Cancel Request"][href$="order/cancel/3"]');
        $I->see('Cancellation Reason');
        $I->seeElement('#cancelorderform-cancellation_reason');
        $I->selectOption('#cancelorderform-cancellation_reason', ['value' => 'too_far']);
        $I->see('Cancel Request', 'button[type="submit"]');
        $I->click('Cancel Request', 'button[type="submit"]');

        $I->expectTo('see order canceled successfully');
        $I->see('Request Canceled Successfully');

        $I->expectTo('see 2 email notifications has been sent');
        $I->seeEmailIsSent(2);

        $I->wantTo('see emails contains correct information');
        $emails = $I->grabSentEmails();
        $I->assertIsArray($emails);
        $I->assertArrayHasKey(0, $emails);

        /** @var yii\swiftmailer\Message $email0 */
        $email0 = $emails[0];
        $I->assertEquals(['support@Connect.com' => 'THERA Connect Support'], $email0->getFrom());
        $I->assertEquals(['provider@example.com' => 'Provider User'], $email0->getTo());
        $I->assertNull($email0->getCc());
        $I->assertEquals(['Connect@gmail.com' => 'THERA Connect Support'], $email0->getReplyTo());
        $I->assertArrayHasKey('Connect@gmail.com', $email0->getBcc());
        $I->assertEquals('You have canceled the service request from customer Member User.', $email0->getSubject());
        $I->assertStringContainsString('Dear Provider User,', $I->amNormalizingEmailContent($email0->toString()));
        $I->assertStringContainsString('You have canceled the service request from customer Member User.', $I->amNormalizingEmailContent($email0->toString()));

        $I->assertIsArray($emails);
        $I->assertArrayHasKey(1, $emails);

        /** @var yii\swiftmailer\Message $email1 */
        $email1 = $emails[1];
        $I->assertEquals(['support@Connect.com' => 'THERA Connect Support'], $email1->getFrom());
        $I->assertEquals(['member@example.com' => 'Member User'], $email1->getTo());
        $I->assertNull($email1->getCc());
        $I->assertEquals(['Connect@gmail.com' => 'THERA Connect Support'], $email1->getReplyTo());
        $I->assertArrayHasKey('Connect@gmail.com', $email1->getBcc());
        $I->assertEquals('Your service request to specialist Provider User has been canceled by specialist.', $email1->getSubject());
        $I->assertStringContainsString('Dear Member User,', $I->amNormalizingEmailContent($email1->toString()));
        $I->assertStringContainsString('Your service request to specialist Provider User has been canceled by specialist.', $I->amNormalizingEmailContent($email1->toString()));

        $I->expectTo('see order record status accepted');
        $I->seeRecord(Order::class, ['id' => '3', 'status' => Order::ORDER_STATUS_CANCELED, 'canceled_by' => '4', 'cancellation_reason' => 'too_far']);
    }

}