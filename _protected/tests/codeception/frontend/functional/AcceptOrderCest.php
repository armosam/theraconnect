<?php

namespace tests\codeception\frontend\functional;

use tests\codeception\common\_support\FixtureHelper;
use Yii;
use common\models\Order;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use tests\codeception\frontend\FunctionalTester;

/**
 * Class AcceptOrderCest
 * @package tests\codeception\frontend\functional
 * @group order
 * @group accept_order_functional
 */
class AcceptOrderCest
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
    public function testAcceptOrderWindow($I)
    {
        $I->wantTo('open accept request modal window for provider');
        $I->amGoingTo('login as Provider User #4 and open my requests page');
        $I->amLoggedInAs(4);
        $I->amOnRoute('order/index');

        $I->expectTo('see only submitted order with Accept Order action button on the grid');
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-a', 1);
        $I->seeElement('table tr[data-key="1"] td a[title="Accept Request"][href$="order/accept/1"]');
        $I->dontSeeElement('table tr[data-key="3"] td a[title="Accept Request"][href$="order/accept/3"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Accept Request"][href$="order/accept/4"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Accept Request"][href$="order/accept/7"]');

        $I->amGoingTo('click on Accept Request action button');
        $I->click('table tr[data-key="1"] td a[title="Accept Request"][href$="order/accept/1"]');

        $I->expectTo('see order accept modal window with messages and button');
        $I->see('Disclosure', 'h4');
        $I->see('Please be informed that by accepting this request you give your consent for your data to be shared with customer and your contact information might be shared to help customer contact you for a service.', 'h5');
        $I->see('Accept Request', 'button[type="submit"]');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testAcceptOrder($I)
    {
        $I->wantToTest('accept order functionality for provider');
        $I->amGoingTo('login as Provider User #4 and open my requests page');
        $I->amLoggedInAs(4);
        $I->amOnRoute('order/index');

        $I->expectTo('see order record status submitted');
        $I->seeRecord(Order::class, ['id' => 1, 'status' => Order::ORDER_STATUS_SUBMITTED]);

        $I->amGoingTo('open Accept Request modal window and accept request');
        $I->click('table tr[data-key="1"] td a[title="Accept Request"][href$="order/accept/1"]');
        $I->see('Accept Request', 'button[type="submit"]');
        $I->click('Accept Request', 'button[type="submit"]');

        $I->expectTo('see order accepted successfully');
        $I->see('Request Accepted Successfully');

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
        $I->assertEquals('You have accepted service request from Member User', $email0->getSubject());
        $I->assertStringContainsString('Dear Provider User,', $I->amNormalizingEmailContent($email0->toString()));
        $I->assertStringContainsString('You accepted the service request from Member User.', $I->amNormalizingEmailContent($email0->toString()));

        $I->assertIsArray($emails);
        $I->assertArrayHasKey(1, $emails);

        /** @var yii\swiftmailer\Message $email1 */
        $email1 = $emails[1];
        $I->assertEquals(['support@Connect.com' => 'THERA Connect Support'], $email1->getFrom());
        $I->assertEquals(['member@example.com' => 'Member User'], $email1->getTo());
        $I->assertNull($email1->getCc());
        $I->assertEquals(['Connect@gmail.com' => 'THERA Connect Support'], $email1->getReplyTo());
        $I->assertArrayHasKey('Connect@gmail.com', $email1->getBcc());
        $I->assertEquals('Your service request to Provider User has been accepted.', $email1->getSubject());
        $I->assertStringContainsString('Dear Member User,', $I->amNormalizingEmailContent($email1->toString()));
        $I->assertStringContainsString('Your service request to specialist Provider User has been accepted.', $I->amNormalizingEmailContent($email1->toString()));

        $I->expectTo('see order record status accepted');
        $I->seeRecord(Order::class, ['id' => '1', 'status' => Order::ORDER_STATUS_ACCEPTED]);
    }

}