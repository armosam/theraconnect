<?php

namespace tests\codeception\backend\functional;

use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\LoginAttempt;
use tests\codeception\backend\_pages\ServiceCategoryPage;
use tests\codeception\backend\FunctionalTester;
use yii\base\InvalidConfigException;

/**
 * Class ServiceCategoryCest
 * @group ServiceCategoryCest
 */
class ServiceCategoryCest
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
    public function testDisableServiceCategory(FunctionalTester $I)
    {
        $I->wantTo('ensure that disabling of service category works');
        $page = ServiceCategoryPage::openBy($I);

        $I->expectTo('see that current page is service category index page with 1 active category');
        $I->amOnPage('service-category/index');
        $I->see('Service Categories', 'h1');
        $I->seeElement('a[title="Disable Service Category"]');
        $I->seeNumberOfElements('a[title="Disable Service Category"]', 1);
        $I->seeNumberOfElements('td[class="boolean-true"]', 1);

        $I->amGoingTo('send ajax POST request to disable first service category');
        $I->sendAjaxPostRequest($page->getUrl('service-category/disable/1'));

        $I->expectTo('see that first service category disabled in the database and on the site');
        $I->seeRecord('common\models\ServiceCategory', ['id' => '1', 'status' => 'P']);
        $I->amOnRoute('service-category/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 0);
        $I->seeNumberOfElements('td[class="boolean-false"]', 1);
    }

    /**
     * @param FunctionalTester $I
     * @throws InvalidConfigException
     */
    public function testEnableServiceCategory(FunctionalTester $I)
    {
        $I->wantTo('ensure that enabling of service category works');
        $page = ServiceCategoryPage::openBy($I);

        $I->expectTo('see that current page is service category index page with 1 active category');
        $I->amOnPage('service-category/index');
        $I->see('Service Categories', 'h1');
        $I->seeElement('a[title="Disable Service Category"]');
        $I->seeNumberOfElements('a[title="Disable Service Category"]', 1);
        $I->seeNumberOfElements('td[class="boolean-true"]', 1);

        $I->amGoingTo('firs disable category then send ajax POST request to enable again');
        $I->sendAjaxPostRequest($page->getUrl('service-category/disable/1'));

        $I->expectTo('see that first service category disabled in the database and on the site');
        $I->seeRecord('common\models\ServiceCategory', ['id' => '1', 'status' => 'P']);
        $I->amOnRoute('service-category/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 0);
        $I->seeNumberOfElements('td[class="boolean-false"]', 1);

        $I->amGoingTo('send ajax POST request to enable first service category');
        $I->sendAjaxPostRequest($page->getUrl('service-category/enable/1'));

        $I->expectTo('see that first service category enabled in the database and on the site');
        $I->seeRecord('common\models\ServiceCategory', ['id' => '1', 'status' => 'A']);
        $I->amOnRoute('service-category/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 1);
        $I->seeNumberOfElements('td[class="boolean-false"]', 0);
    }

}
