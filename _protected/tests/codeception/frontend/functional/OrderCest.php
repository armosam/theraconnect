<?php

namespace tests\codeception\frontend\functional;

use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\Order;
use tests\codeception\frontend\FunctionalTester;

/**
 * Class OrderCest
 * @package tests\codeception\frontend\functional
 * @group order
 * @group order_functional
 */
class OrderCest
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
    public function testOrderGridOfProvider($I)
    {
        $I->wantToTest('order grid logged as provider account');
        $I->amGoingTo('login as provider and open orders page');
        $I->amLoggedInAs(4);
        $I->amOnRoute('/order/index');

        $I->expectTo('see my requests grid');
        $I->see('Received Service Requests', '.breadcrumb');
        $I->seeNumberOfElements('table.kv-grid-table>tbody>tr', 6);
        $I->seeNumberOfElements('table tr td a[title="View"]', 6);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-a', 1);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-s', 1);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-r', 3);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-c', 1);
        $I->dontSeeElement('table tr', ['data-key' => '2']);

        $I->expectTo('see correct  action buttons for submitted request');
        $I->see('Submitted', 'table tr[data-key="1"] td[class*="order-status-s"]');
        $I->seeElement('table tr[data-key="1"] td a[title="View"][href$="order/1"]');
        $I->seeElement('table tr[data-key="1"] td a[title="Accept Request"][href$="order/accept/1"]');
        $I->seeElement('table tr[data-key="1"] td a[title="Reject Request"][href$="order/reject/1"]');
        $I->dontSeeElement('table tr[data-key="1"] td a[title="Cancel Request"][href$="order/cancel/1"]');

        $I->expectTo('see correct  action buttons for accepted request');
        $I->see('Accepted', 'table tr[data-key="3"] td[class*="order-status-a"]');
        $I->seeElement('table tr[data-key="3"] td a[title="View"][href$="order/3"]');
        $I->dontSeeElement('table tr[data-key="3"] td a[title="Accept Request"][href$="order/accept/3"]');
        $I->dontSeeElement('table tr[data-key="3"] td a[title="Reject Request"][href$="order/reject/3"]');
        $I->seeElement('table tr[data-key="3"] td a[title="Cancel Request"][href$="order/cancel/3"]');

        $I->expectTo('see correct  action buttons for rejected request');
        $I->see('Rejected', 'table tr[data-key="4"] td[class*="order-status-r"]');
        $I->seeElement('table tr[data-key="4"] td a[title="View"][href$="order/4"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Accept Request"][href$="order/accept/4"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Reject Request"][href$="order/reject/4"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Cancel Request"][href$="order/cancel/4"]');

        $I->expectTo('see correct  action buttons for canceled request');
        $I->see('Canceled', 'table tr[data-key="7"] td[class*="order-status-c"]');
        $I->seeElement('table tr[data-key="7"] td a[title="View"][href$="order/7"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Accept Request"][href$="order/accept/7"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Reject Request"][href$="order/reject/7"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Cancel Request"][href$="order/cancel/7"]');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testOrderGridOfCustomer($I)
    {
        $I->wantToTest('order grid logged as customer account');
        $I->amGoingTo('login as customer and open orders page');
        $I->amLoggedInAs(3);
        $I->amOnRoute('/order/index');

        $I->expectTo('see my requests grid');
        $I->see('Service Requests', '.breadcrumb');
        $I->seeNumberOfElements('table.kv-grid-table>tbody>tr', 6);
        $I->seeNumberOfElements('table tr td a[title="View"]', 6);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-a', 1);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-s', 1);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-r', 3);
        $I->seeNumberOfElements('table.kv-grid-table tr td.order-status-c', 1);
        $I->dontSeeElement('table tr[data-key="2"]');

        $I->expectTo('see correct  action buttons for submitted request');
        $I->see('Submitted', 'table tr[data-key="1"] td[class*="order-status-s"]');
        $I->seeElement('table tr[data-key="1"] td a[title="View"][href$="order/1"]');
        $I->dontSeeElement('table tr[data-key="1"] td a[title="Accept Request"][href$="order/accept/1"]');
        $I->dontSeeElement('table tr[data-key="1"] td a[title="Reject Request"][href$="order/reject/1"]');
        $I->seeElement('table tr[data-key="1"] td a[title="Cancel Request"][href$="order/cancel/1"]');

        $I->expectTo('see correct  action buttons for accepted request');
        $I->see('Accepted', 'table tr[data-key="3"] td[class*="order-status-a"]');
        $I->seeElement('table tr[data-key="3"] td a[title="View"][href$="order/3"]');
        $I->dontSeeElement('table tr[data-key="3"] td a[title="Accept Request"][href$="order/accept/3"]');
        $I->dontSeeElement('table tr[data-key="3"] td a[title="Reject Request"][href$="order/reject/3"]');
        $I->seeElement('table tr[data-key="3"] td a[title="Cancel Request"][href$="order/cancel/3"]');

        $I->expectTo('see correct  action buttons for rejected request');
        $I->see('Rejected', 'table tr[data-key="4"] td[class*="order-status-r"]');
        $I->seeElement('table tr[data-key="4"] td a[title="View"][href$="order/4"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Accept Request"][href$="order/accept/4"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Reject Request"][href$="order/reject/4"]');
        $I->dontSeeElement('table tr[data-key="4"] td a[title="Cancel Request"][href$="order/cancel/4"]');

        $I->expectTo('see correct  action buttons for canceled request');
        $I->see('Canceled', 'table tr[data-key="7"] td[class*="order-status-c"]');
        $I->seeElement('table tr[data-key="7"] td a[title="View"][href$="order/7"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Accept Request"][href$="order/accept/7"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Reject Request"][href$="order/reject/7"]');
        $I->dontSeeElement('table tr[data-key="7"] td a[title="Cancel Request"][href$="order/cancel/7"]');
    }

}