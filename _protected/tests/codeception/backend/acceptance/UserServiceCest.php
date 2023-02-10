<?php

namespace tests\codeception\backend\acceptance;

use Yii;
use Exception;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\LoginAttempt;
use tests\codeception\backend\AcceptanceTester;

/**
 * Class UserServiceCest
 * @group UserServiceAcceptance
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
     * @param AcceptanceTester $I
     */
    public function testUserServiceGridView(AcceptanceTester $I)
    {
        $I->wantTo('ensure that user services for user by id equal 4 (provider) shows 2 services activated');
        $I->amOpeningPage(['user-service/view', 'id' => 4], ['username' => 'thecreator', 'password' => 'creator123']);

        $I->expectTo('see 2 services activated for provider in the grid view');
        $I->see('User Services', 'h1');
        $I->seeNumberOfElements('tr[data-key]', 2);
        $I->seeLink('Users');
        $I->seeLink('Edit');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testUpdateUserService(AcceptanceTester $I)
    {
        $I->wantTo('ensure that service edit page works');
        $I->amOpeningPage(['user-service/view', 'id' => 4], ['username' => 'thecreator', 'password' => 'creator123']);

        $I->amGoingTo('click on Edit button to open update page for services');
        $I->click('Edit');

        $I->expectTo('see all available services user can activate');
        if (method_exists($I, 'wait')) {
            $I->waitForElement('input[class="kv-row-checkbox"]', 3); // only for selenium
            $I->canSeeNumberOfElementsInDOM('input[class="kv-row-checkbox"]:checked', 2);
        }else{
            $I->canSeeNumberOfElements('input[class="kv-row-checkbox"]:checked', 2);
        }

        $I->see('User Services', 'h1');
        $I->see('Save', 'button[type="submit"]');
        $I->see('Reset', 'button[type="reset"]');
        $I->seeLink('Cancel');

        $I->amGoingTo('change first service parameters and save it');
        $I->selectOption('Service[1][service_radius]', ['value' => 200]);
        $I->selectOption('Service[1][service_frequency]', ['value' => 18000]);
        $I->fillField('Service[1][transportation_fee]', 500.00);
        $I->fillField('Service[1][service_fee]', 5000.00);
        if (method_exists($I, 'wait')) {
            $I->fillField('#service-1-experience_at-disp', 'Oct 10, 2020');
        }else{
            $I->fillField('Service[1][experience_at]', '2020-10-10');
        }

        $I->amGoingTo('activate 3-rd service and save it');
        if (method_exists($I, 'wait')) {
            $I->waitForElement('.bootstrap-switch-id-service_checkbox_3', 3); // only for selenium
            $I->click('.bootstrap-switch-id-service_checkbox_3');
        }else{
            $I->checkOption('#service_checkbox_3');
        }

        $I->click('Save');

        if (method_exists($I, 'wait')) {
            $I->waitForElement('button[class="btn btn-warning"]', 3); // only for selenium
            $I->click('button[class="btn btn-warning"]');
            $I->waitForText('Your Changes Saved.', 3); // only for selenium
        }

        $I->expectTo('see message about success result');
        $I->see('User Services');
        $I->see('Your Changes Saved.', '.alert-success');

        $I->expectTo('see that 3 service were activated for current user');
        $I->canSeeNumberOfElements('tr[data-key]', 3);
        $I->seeElement('tr[data-key="1"]');
        $I->seeElement('tr[data-key="2"]');
        $I->seeElement('tr[data-key="3"]');

        $I->expectTo('see that changes of first service were saved');
        $I->see('5,000.00',  'tr[data-key="1"] td');
        $I->see('500.00',  'tr[data-key="1"] td');
        $I->see(Yii::t('app', '{time,plural,=0.5{# hour} =1{# hour} other{# hours}}', ['time' => 5]),  'tr[data-key="1"] td');
        $I->see('200',  'tr[data-key="1"] td');
        $I->see('Oct 10, 2020',  'tr[data-key="1"] td');

        $I->seeLink('Users');
        $I->seeLink('Edit');
    }

}
