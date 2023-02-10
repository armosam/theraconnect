<?php

namespace tests\codeception\backend\acceptance;

use Exception;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\helpers\ConstHelper;
use common\models\LoginAttempt;
use tests\codeception\backend\AcceptanceTester;

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
     * @param AcceptanceTester $I
     */
    public function testServiceCategoryGridView(AcceptanceTester $I)
    {
        $I->wantTo('ensure that service category grid view shows 1 active service category');
        $I->amOpeningPage('service-category/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->expectTo('see 1 enabled service category in the grid view');
        $I->seeNumberOfElements('td[class="boolean-true"]', '1');
        $I->seeNumberOfElements('a[title="Disable Service Category"]', 1);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testViewServiceCategory(AcceptanceTester $I)
    {
        $I->wantTo('ensure that service category view page works');
        $I->amOpeningPage('service-category/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->amGoingTo('click on View action button to open view for first service category');
        $I->click('a[href$="service-category/1"][title="View Record"]');

        if (method_exists($I, 'wait')) {
            $I->wait(1); // only for selenium
        }

        $I->expectTo('see new page opened with service category detail view');
        $I->see('Service Category - Service', 'h1');

        $I->seeLink('Back');
        $I->seeLink('Edit');
        $I->seeLink('Delete');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testCreateServiceCategory(AcceptanceTester $I)
    {
        $I->wantTo('ensure that create service category page works');
        $I->amOpeningPage('service-category/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->amGoingTo('click on Create Service Category button to open create form');
        $I->seeLink('Create Service Category', 'service-category/create');
        $I->click('Create Service Category');

        if (method_exists($I, 'wait')) {
            $I->waitForElement('button[type="submit"]', 3); // only for selenium
        }

        $I->expectTo('see opened create form page');
        $I->see('Create Service Category', 'h1');

        $I->amGoingTo('fill form and submit to create a new service category');
        if (method_exists($I, 'wait')) {
            $I->click('English');
            $I->waitForElementVisible('#servicecategory-category_name_en', 3);
        }
        $I->fillField('ServiceCategory[category_name_en]', 'Test Service Category En');
        if (method_exists($I, 'wait')) {
            $I->click('Հայերեն');
            $I->waitForElementVisible('#servicecategory-category_name_hy', 3);
        }
        $I->fillField('ServiceCategory[category_name_hy]', 'Test Service Category Hy');
        if (method_exists($I, 'wait')) {
            $I->click('Русскиий');
            $I->waitForElementVisible('#servicecategory-category_name_ru', 3);
        }
        $I->fillField('ServiceCategory[category_name_ru]', 'Test Service Category Ru');
        $I->selectOption('ServiceCategory[ordering]', ['value' => 2]);
        $I->selectOption('ServiceCategory[status]', ['value' => ConstHelper::STATUS_ACTIVE]);

        $I->see('Create', 'button[type="submit"]');
        $I->click('Create');

        if (method_exists($I, 'wait')) {
            $I->waitForElement('button[class="btn btn-warning"]', 3); // only for selenium
            $I->click('button[class="btn btn-warning"]');
            $I->waitForText('Service Category created successfully.', 3); // only for selenium
        }

        $I->expectTo('see that 1 service category was created and showing on view page');
        $I->see('Service Category created successfully.', '.alert-success');
        $I->canSee('Service Category - Test Service Category En',  'h1');
        $I->canSee('Test Service Category En', 'table[class*="detail-view"] td');
        $I->canSee('Test Service Category Hy', 'td');
        $I->canSee('Test Service Category Ru', 'td');
        $I->canSee('Active', 'td');

        $I->seeLink('Back');
        $I->seeLink('Edit');
        $I->seeLink('Delete');

        $I->amGoingTo('go to index page to see the new created service category');
        $I->click('Service Categories');

        $I->expectTo('see that 1 service category was created');
        $I->seeNumberOfElements('a[title="Disable Service Category"]', 2);
        $I->seeElement('a[href$="service-category/disable/2"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testUpdateServiceCategory(AcceptanceTester $I)
    {
        $I->wantTo('ensure that update service category page works');
        $I->amOpeningPage('service-category/index', ['username' => 'thecreator', 'password' => 'creator123']);

    }
}
