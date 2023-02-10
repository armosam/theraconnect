<?php

namespace tests\codeception\backend\acceptance;

use Yii;
use Exception;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\helpers\ConstHelper;
use common\models\LoginAttempt;
use tests\codeception\backend\AcceptanceTester;

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
     * @param AcceptanceTester $I
     */
    public function testServicesGridView(AcceptanceTester $I)
    {
        $I->wantTo('ensure that services grid view shows 3 active services');
        $I->amOpeningPage('service/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->expectTo('see 3 enabled services in the grid view');
        $I->seeNumberOfElements('td[class="boolean-true"]', '3');
        $I->seeNumberOfElements('a[title="Disable Service"]', '3');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testViewService(AcceptanceTester $I)
    {
        $I->wantTo('ensure that services view page works');
        $I->amOpeningPage('service/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->amGoingTo('click on View action button to open view for first service');
        $I->click('a[href$="service/1"][title="View Record"]');

        if (method_exists($I, 'wait')) {
            $I->wait(1); // only for selenium
        }

        $I->expectTo('see new page opened with service detail view');
        $I->see('Service - Birth Service', 'h1');

        $I->seeLink('Back');
        $I->seeLink('Edit');
        $I->seeLink('Delete');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testCreateService(AcceptanceTester $I)
    {
        $I->wantTo('ensure that create service page works');
        $I->amOpeningPage('service/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->amGoingTo('click on Create Service button to open create form');
        $I->seeLink('Create Service', 'service/create');
        $I->click('Create Service');

        if (method_exists($I, 'wait')) {
            $I->waitForElement('button[type="submit"]', 3); // only for selenium
        }

        $I->expectTo('see opened create form page');
        $I->see('Create Service', 'h1');

        $I->amGoingTo('fill form and submit to create a new service');
        $I->selectOption('Service[service_category_id]', ['value' => 1]);
        if (method_exists($I, 'wait')) {
            $I->click('English');
            $I->waitForElementVisible('#service-service_name_en', 3);
        }
        $I->fillField('Service[service_name_en]', 'Test Service En');
        if (method_exists($I, 'wait')) {
            $I->click('Հայերեն');
            $I->waitForElementVisible('#service-service_name_hy', 3);
        }
        $I->fillField('Service[service_name_hy]', 'Test Service Hy');
        if (method_exists($I, 'wait')) {
            $I->click('Русскиий');
            $I->waitForElementVisible('#service-service_name_ru', 3);
        }
        $I->fillField('Service[service_name_ru]', 'Test Service Ru');
        $I->fillField('Service[service_fee]', '1010');
        $I->fillField('Service[transportation_fee]', '101');
        $I->selectOption('Service[service_frequency]', ['value' => 18000]);
        $I->fillField('Service[service_radius]', '20');
        if (method_exists($I, 'wait')) {
            $I->fillField('#service-experience_at-disp', 'Oct 10, 2014');
        }else{
            $I->fillField('Service[experience_at]', '2014-10-10');
        }
        $I->selectOption('Service[ordering]', ['value' => 4]);
        $I->selectOption('Service[status]', ['value' => ConstHelper::STATUS_ACTIVE]);

        $I->see('Create', 'button[type="submit"]');
        $I->click('Create');

        if (method_exists($I, 'wait')) {
            $I->waitForElement('button[class="btn btn-warning"]', 3); // only for selenium
            $I->click('button[class="btn btn-warning"]');
            $I->waitForText('Service created successfully.', 3); // only for selenium
        }

        $I->expectTo('see that 1 service was created and showing on the view page');
        $I->see('Service created successfully.', '.alert-success');
        $I->see('Service - Test Service En',  'h1');
        $I->see('Services', 'td');
        $I->see('Test Service En', 'td');
        $I->see('Test Service Hy', 'td');
        $I->see('Test Service Ru', 'td');
        $I->see('1010.00', 'td');
        $I->see('101.00', 'td');
        $I->see(Yii::t('app', '{time,plural,=0.5{# hour} =1{# hour} other{# hours}}', ['time' => 5]), 'td');
        $I->see('20', 'td');
        $I->see('Oct 10, 2014', 'td');
        $I->see('Active', 'td');

        $I->seeLink('Back');
        $I->seeLink('Edit');
        $I->seeLink('Delete');

        $I->amGoingTo('go to index page to see the new created service');
        $I->click('Services');

        $I->expectTo('see that 1 service was created');
        $I->seeNumberOfElements('a[title="Disable Service"]', 4);
        $I->seeElement('a[href$="service/disable/4"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testUpdateService(AcceptanceTester $I)
    {
        $I->wantTo('ensure that update service page works');
        $I->amOpeningPage('service/index', ['username' => 'thecreator', 'password' => 'creator123']);

    }

}
