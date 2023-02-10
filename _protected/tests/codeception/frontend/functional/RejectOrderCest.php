<?php

namespace tests\codeception\frontend\functional;

use Yii;
use common\models\Order;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use tests\codeception\frontend\FunctionalTester;

/**
 * Class RejectOrderCest
 * @package tests\codeception\frontend\functional
 * @group order
 * @group reject_order_functional
 */
class RejectOrderCest
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
    public function testRejectOrderWindow($I)
    {
        $I->wantTo('open reject request modal window for provider');
        $I->amGoingTo('login as Provider User #4 and open my requests page');
        $I->amLoggedInAs(4);
        $I->amOnRoute('order/index');

        $I->expectTo('see only submitted order with Reject Order action button on the grid');
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-r', 3);
        $I->seeElement('table tr[data-key="1"] td a[title="Reject Request"][href$="order/reject/1"]');
        $I->dontSeeElement('table tr[data-key="3"] td a[title="Reject Request"][href$="order/reject/3"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Reject Request"][href$="order/reject/4"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Reject Request"][href$="order/reject/7"]');

        $I->amGoingTo('click on Reject Request action button');
        $I->click('table tr[data-key="1"] td a[title="Reject Request"][href$="order/reject/1"]');

        $I->expectTo('see order reject modal window with messages and button');
        $I->see('Information', 'h3');
        $I->see('Please be informed that by rejecting this request customer might receive notification about rejection. Select bellow select box that better describes reason of rejection.', 'h4');
        $I->seeElement('#rejectorderform-rejection_reason');
        $I->see('Reject Request', 'button[type="submit"]');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testRejectOrder($I)
    {
        $I->wantToTest('reject order functionality for provider');
        $I->amGoingTo('login as Provider User #4 and open my requests page');
        $I->amLoggedInAs(4);
        $I->amOnRoute('order/index');

        $I->expectTo('see order record status submitted without rejection data');
        $I->seeRecord(Order::class, ['id' => 1, 'status' => Order::ORDER_STATUS_SUBMITTED, 'rejected_by' => null, 'rejection_reason' => null]);

        $I->amGoingTo('open Reject Request modal window and reject request');
        $I->click('table tr[data-key="1"] td a[title="Reject Request"][href$="order/reject/1"]');
        $I->see('Rejection Reason');
        $I->seeElement('#rejectorderform-rejection_reason');
        $I->selectOption('#rejectorderform-rejection_reason', ['value' => 'too_far']);
        $I->see('Reject Request', 'button[type="submit"]');
        $I->click('Reject Request', 'button[type="submit"]');

        $I->expectTo('see order rejected successfully');
        $I->see('Request Rejected Successfully');

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
        $I->assertEquals('You rejected service request from Member User.', $email0->getSubject());
        $I->assertStringContainsString('Dear Provider User,', $I->amNormalizingEmailContent($email0->toString()));
        $I->assertStringContainsString('You rejected the service request from Member User.', $I->amNormalizingEmailContent($email0->toString()));

        $I->assertIsArray($emails);
        $I->assertArrayHasKey(1, $emails);

        /** @var yii\swiftmailer\Message $email1 */
        $email1 = $emails[1];
        $I->assertEquals(['support@Connect.com' => 'THERA Connect Support'], $email1->getFrom());
        $I->assertEquals(['member@example.com' => 'Member User'], $email1->getTo());
        $I->assertNull($email1->getCc());
        $I->assertEquals(['Connect@gmail.com' => 'THERA Connect Support'], $email1->getReplyTo());
        $I->assertArrayHasKey('Connect@gmail.com', $email1->getBcc());
        $I->assertEquals('Your service request to Provider User has been rejected.', $email1->getSubject());
        $I->assertStringContainsString('Dear Member User,', $I->amNormalizingEmailContent($email1->toString()));
        $I->assertStringContainsString('Your service request to specialist Provider User has been rejected.', $I->amNormalizingEmailContent($email1->toString()));

        $I->expectTo('see order record status accepted');
        $I->seeRecord(Order::class, ['id' => '1', 'status' => Order::ORDER_STATUS_REJECTED, 'rejected_by' => '4', 'rejection_reason' => 'too_far']);
    }

}