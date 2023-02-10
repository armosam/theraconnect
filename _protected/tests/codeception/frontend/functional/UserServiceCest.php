<?php

namespace tests\codeception\frontend\functional;

use Yii;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\LoginAttempt;
use tests\codeception\frontend\_pages\UserServicePage;
use tests\codeception\frontend\FunctionalTester;

/**
 * Class UserServiceCest
 * @group UserServiceFunctional
 */
class UserServiceCest
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
    public function testServicesGridView(FunctionalTester $I)
    {
        $I->wantTo('ensure that user services grid view shows 2 activated services');
        UserServicePage::openBy($I);

        $I->expectTo('see 2 services activated for current provider in the grid view');
        $I->see('My Services', '.active');
        $I->seeNumberOfElements('tr[data-key]', '2');
        $I->seeLink('Edit', 'user-service/update');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testUpdateServices(FunctionalTester $I)
    {
        $I->wantTo('ensure that service edit page works');
        UserServicePage::openBy($I);

        $I->amGoingTo('click on Edit button to open update page for services');
        $I->click('Edit');
        $I->expectTo('see all available service user can activate');
        $I->see('Update Services', '.active');
        $I->seeNumberOfElements('tr[data-key]', '3');
        $I->seeNumberOfElements('tr[class~="kv-tabform-row"]', '3');
        $I->seeNumberOfElements('input[class="kv-row-checkbox"]:checked', '2');

        $I->seeElement('button[type="submit"]');
        $I->seeElement('button[type="reset"]');
        $I->seeLink('Cancel');

        $I->amGoingTo('change first service parameters and save it');
        $I->selectOption('Service[1][service_radius]', ['value' => 200]);
        $I->selectOption('Service[1][service_frequency]', ['value' => 18000]);
        $I->fillField('Service[1][transportation_fee]', 500.00);
        $I->fillField('Service[1][service_fee]', 5000.00);
        $I->fillField('Service[1][experience_at]', '2020-10-10');

        $I->checkOption('#service_checkbox_3');

        $I->see('Save', 'button[type="submit"]');
        $I->click('Save');

        $I->expectTo('see that 3 service were activated for current user');
        $I->seeNumberOfElements('tr[data-key]', 3);

        $I->expectTo('see that data changed for first service');
        $I->see('5,000.00',  'tr[data-key="1"] td');
        $I->see('500.00',  'tr[data-key="1"] td');
        $I->see(Yii::t('app', '{time,plural,=0.5{# hour} =1{# hour} other{# hours}}', ['time' => 5]),  'tr[data-key="1"] td');
        $I->see('200',  'tr[data-key="1"] td');
        $I->see('Oct 10, 2020',  'tr[data-key="1"] td');

        $I->seeLink('Home');
        $I->seeLink('Edit');
    }

}
