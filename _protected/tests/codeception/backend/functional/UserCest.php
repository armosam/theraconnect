<?php

namespace tests\codeception\backend\functional;

use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\LoginAttempt;
use tests\codeception\backend\_pages\UserPage;
use tests\codeception\backend\FunctionalTester;
use yii\base\InvalidConfigException;

/**
 * Class UserCest
 * @group UserCest
 */
class UserCest
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
    public function testChangeStatus(FunctionalTester $I)
    {
        /*$I->wantTo('ensure that disabling of user works');
        $page = UserPage::openBy($I);

        $I->expectTo('see that current page is user index page with 3 active users');
        $I->amOnPage('user/index');
        $I->see('Users', 'h1');
        $I->seeElement('a[title="Disable User"]');
        $I->seeNumberOfElements('a[title="Disable User"]', 3);
        $I->seeNumberOfElements('td[class="boolean-true"]', 3);

        $I->amGoingTo('send ajax POST request to disable first user');
        $I->sendAjaxPostRequest($page->getUrl('user/disable/1'));

        $I->expectTo('see that first user disabled in the database and on the site');
        $I->seeRecord('common\models\User', ['id' => '1', 'status' => 'P']);
        $I->amOnRoute('user/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 2);
        $I->seeNumberOfElements('td[class="boolean-false"]', 1);*/
    }

    /**
     * @param FunctionalTester $I
     * @throws InvalidConfigException
     */
    public function testEnableUser(FunctionalTester $I)
    {
        /*$I->wantTo('ensure that enabling of user works');
        $page = UserPage::openBy($I);

        $I->expectTo('see that current page is user index page with 3 active users');
        $I->amOnPage('user/index');
        $I->see('Users', 'h1');
        $I->seeElement('a[title="Disable User"]');
        $I->seeNumberOfElements('a[title="Disable User"]', 3);
        $I->seeNumberOfElements('td[class="boolean-true"]', 3);

        $I->amGoingTo('send ajax POST request to disable first user then enable again');
        $I->sendAjaxPostRequest($page->getUrl('user/disable/1'));

        $I->expectTo('see that first user disabled in the database and on the site');
        $I->seeRecord('common\models\User', ['id' => '1', 'status' => 'P']);
        $I->amOnRoute('user/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 2);
        $I->seeNumberOfElements('td[class="boolean-false"]', 1);

        $I->amGoingTo('send ajax POST request to enable first user');
        $I->sendAjaxPostRequest($page->getUrl('user/enable/1'));

        $I->expectTo('see that first user enabled in the database and on the site');
        $I->seeRecord('common\models\User', ['id' => '1', 'status' => 'A']);
        $I->amOnRoute('user/index');
        $I->seeNumberOfElements('td[class="boolean-true"]', 3);
        $I->seeNumberOfElements('td[class="boolean-false"]', 0);*/
    }

}
