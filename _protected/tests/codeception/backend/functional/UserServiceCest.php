<?php

namespace tests\codeception\backend\functional;

use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\LoginAttempt;
use tests\codeception\backend\_pages\UserServicePage;
use tests\codeception\backend\FunctionalTester;

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
     */
    public function testServicesGridView(FunctionalTester $I)
    {
        $I->wantTo('ensure that user services for user by id equal 4 (provider) shows 2 services activated');
        UserServicePage::openBy($I, ['id' => 4]);

        $I->expectTo('see 2 services activated for provider in the grid view');
        $I->see('User Services', 'h1');
        $I->seeNumberOfElements('tr[data-key]', 2);
        $I->seeLink('Users');
        $I->seeLink('Edit');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testUpdateServices(FunctionalTester $I)
    {
        $I->wantTo('ensure that service edit page works');
        UserServicePage::openBy($I, ['id' => 4]);

        $I->amGoingTo('click on Edit button to open update page for services');
        $I->click('Edit');

        $I->expectTo('see all available services user can activate');
        $I->see('User Services');
        $I->seeNumberOfElements('tr[class~="kv-tabform-row"]', 3);
        $I->seeNumberOfElements('input[class="kv-row-checkbox"]:checked', 2);

        $I->see('Save', 'button[type="submit"]');
        $I->see('Reset', 'button[type="reset"]');
        $I->seeLink('Cancel');

        $I->amGoingTo('change first service parameters and save it');
        $I->selectOption('Service[1][service_radius]', ['value' => 200]);
        $I->selectOption('Service[1][service_frequency]', ['value' => 18000]);
        $I->fillField('Service[1][transportation_fee]', 500.00);
        $I->fillField('Service[1][service_fee]', 5000.00);
        $I->fillField('Service[1][experience_at]', '2020-10-10');

        $I->amGoingTo('activate 3-rd service and save it');
        $I->checkOption('#service_checkbox_3');

        $I->click('Save', 'button[type="submit"]');

        $I->expectTo('see message about success result');
        $I->see('User Services');
        $I->see('Your Changes Saved.', '.alert-success');

        $I->expectTo('see that 3 service were activated for current user');
        $I->seeNumberOfElements('tr[data-key]', 3);
        $I->seeElement('tr[data-key="1"] td');
        $I->seeElement('tr[data-key="2"] td');
        $I->seeElement('tr[data-key="3"] td');

        $I->expectTo('see that changes of first service were saved');
        $I->see('5,000.00',  'tr[data-key="1"] td');
        $I->see('500.00',  'tr[data-key="1"] td');
        $I->see('5 hours',  'tr[data-key="1"] td');
        $I->see('200',  'tr[data-key="1"] td');
        $I->see('Oct 10, 2020',  'tr[data-key="1"] td');

        $I->seeLink('Users');
        $I->seeLink('Edit');
    }

}
