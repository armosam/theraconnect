<?php

namespace tests\codeception\backend\functional;

use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\LoginAttempt;
use tests\codeception\backend\_pages\ServicePage;
use tests\codeception\backend\FunctionalTester;
use yii\base\InvalidConfigException;

/**
 * Class ServiceCest
 * @group ServiceCest
 */
class ServiceCest
{
    /**
     * This method is called before each test method.
     *
     * @param TestEvent $event
     */
    public function _before($event)
    {
        LoginAttempt::deleteAll();
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
     * @throws InvalidConfigException
     */
    public function testDisableService(FunctionalTester $I)
    {
        $I->wantTo('ensure that disabling of service works');
        $page = ServicePage::openBy($I);

        $I->expectTo('see that current page is service index page with 3 active services');
        $I->amOnPage('service/index');
        $I->see('Services', 'h1');
        $I->seeElement('a[title="Disable Service"]');
        $I->seeNumberOfElements('a[title="Disable Service"]', 3);
        $I->seeNumberOfElements('td[class="boolean-true"]', 3);

        $I->amGoingTo('send ajax POST request to disable first service');
        $I->sendAjaxPostRequest($page->getUrl('service/disable/1'));

        $I->expectTo('see that first service disabled in the database and on the site');
        $I->seeRecord('common\models\Service', ['id' => '1', 'status' => 'P']);
        $I->amOnRoute('service/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 2);
        $I->seeNumberOfElements('td[class="boolean-false"]', 1);
    }

    /**
     * @param FunctionalTester $I
     * @throws InvalidConfigException
     */
    public function testEnableService(FunctionalTester $I)
    {
        $I->wantTo('ensure that enabling of service works');
        $page = ServicePage::openBy($I);

        $I->expectTo('see that current page is service index page with 3 active services');
        $I->amOnPage('service/index');
        $I->see('Services', 'h1');
        $I->seeElement('a[title="Disable Service"]');
        $I->seeNumberOfElements('a[title="Disable Service"]', 3);
        $I->seeNumberOfElements('td[class="boolean-true"]', 3);

        $I->amGoingTo('send ajax POST request to disable first service then enable again');
        $I->sendAjaxPostRequest($page->getUrl('service/disable/1'));

        $I->expectTo('see that first service disabled in the database and on the site');
        $I->seeRecord('common\models\Service', ['id' => '1', 'status' => 'P']);
        $I->amOnRoute('service/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 2);
        $I->seeNumberOfElements('td[class="boolean-false"]', 1);

        $I->amGoingTo('send ajax POST request to enable first service');
        $I->sendAjaxPostRequest($page->getUrl('service/enable/1'));

        $I->expectTo('see that first service enabled in the database and on the site');
        $I->seeRecord('common\models\Service', ['id' => '1', 'status' => 'A']);
        $I->amOnRoute('service/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 3);
        $I->seeNumberOfElements('td[class="boolean-false"]', 0);
    }

}
