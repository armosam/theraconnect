<?php

namespace tests\codeception\backend\acceptance;

use Exception;
use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\LoginAttempt;
use common\models\User;
use tests\codeception\backend\AcceptanceTester;

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
     * @param AcceptanceTester $I
     */
    public function testUsersGridView(AcceptanceTester $I)
    {
        $I->wantTo('ensure that users grid view shows correct user roles and statuses');
        $I->amOpeningPage('user/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->expectTo('see 9 users in the grid view');
        $I->seeNumberOfElements('td[class~="role"]', 11);
        $I->seeNumberOfElements('td[class~="status"]', 11);

        $I->expectTo('see 1 super user in the grid view');
        $I->seeNumberOfElements('td[class$="role-thecreator"]', 1);

        $I->expectTo('see 1 admin user in the grid view');
        $I->seeNumberOfElements('td[class$="role-admin"]', 1);

        $I->expectTo('see 1 editor user in the grid view');
        $I->seeNumberOfElements('td[class$="role-editor"]', 1);

        $I->expectTo('see provider user in the grid view');
        $I->seeNumberOfElements('td[class$="role-provider"]', 3);

        $I->expectTo('see provider user has action button for services');
        $I->seeElement('a[href$="user-service/4"]');

        $I->expectTo('see 5 customer users in the grid view');
        $I->seeNumberOfElements('td[class$="role-customer"]', 5);


        $I->expectTo('see 6 active users in the grid view');
        $I->seeNumberOfElements('td[class$="status-a"]', 7);

        $I->expectTo('see a not activated user in the grid view');
        $I->seeNumberOfElements('td[class$="status-n"]', 1);

        $I->expectTo('see an inactive user in the grid view');
        $I->seeNumberOfElements('td[class$="status-i"]', 1);

        $I->expectTo('see a suspended user in the grid view');
        $I->seeNumberOfElements('td[class$="status-s"]', 1);

        $I->expectTo('see a terminated user in the grid view');
        $I->seeNumberOfElements('td[class$="status-t"]', 1);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testViewUser(AcceptanceTester $I)
    {
        $I->wantTo('ensure that users view page works');
        $I->amOpeningPage('user/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->amGoingTo('click on View action button to open view for first user');
        $I->seeElement('a[href$="user/1"][title="View Record"]');
        $I->click('a[href$="user/1"][title="View Record"]');

        if (method_exists($I, 'wait')) {
            $I->wait(3); // only for selenium
        }

        $I->expectTo('see new page opened with user detail view');
        $I->see('User thecreator');

        $I->seeLink('Back');
        $I->seeLink('Edit');
        $I->seeLink('Delete');
    }

    /**
     * @param AcceptanceTester $I
     * @throws Exception
     */
    public function testCreateUser(AcceptanceTester $I)
    {
        $I->wantTo('ensure that create user page works');
        $I->amOpeningPage('user/index', ['username' => 'thecreator', 'password' => 'creator123']);

        $I->amGoingTo('click on Create User button to open create form');
        $I->seeLink('Create User', 'user/create');
        $I->click('Create User');

        if (method_exists($I, 'wait')) {
            $I->waitForElement('button[type="submit"]', 3); // only for selenium
        }

        $I->expectTo('see opened create form page');
        $I->see('Create User', 'h1');

        $I->amGoingTo('fill form and submit to create a new user');
        $I->fillField('User[username]', 'test');
        $I->fillField('User[email]', 'test@test.com');
        $I->fillField('User[password]', 'test123');
        $I->fillField('User[first_name]', 'Test');
        $I->fillField('User[last_name]', 'User');
        $I->fillField('User[address]', 'Test Address 11');
        $I->fillField('User[phone1]', '+18188887777');
        $I->selectOption('User[gender]', ['value' => User::USER_GENDER_MALE]);
        $I->selectOption('Role[item_name]', ['value' => User::USER_PROVIDER]);
        $I->selectOption('User[status]', ['value' => User::USER_STATUS_ACTIVE]);

        $I->see('Create', 'button[type="submit"]');
        $I->click('Create');

        if (method_exists($I, 'wait')) {
            $I->waitForElement('button[class="btn btn-warning"]', 3); // only for selenium
            $I->click('button[class="btn btn-warning"]');
            $I->waitForText('User created successfully.', 3); // only for selenium
        }

        $I->expectTo('see that 1 user was created and showing on the view page. Verification needed attributes should not be there');
        $I->see('User created successfully.', '.alert-success');
        $I->see('User test',  'h1');
        $I->see('Test', 'td');
        $I->see('User', 'td');
        $I->see('test@test.com', 'td');
        $I->see('Test Address 11', 'td');
        $I->dontSee('(818) 888-7777', 'td');
        $I->dontSee('test123', 'td');
        $I->see('Active Provider', '.small');
        $I->see('Male', 'td');

        $I->seeLink('Back');
        $I->seeLink('Edit');
        $I->seeLink('Delete');

        $I->amGoingTo('go to index page to see the new created user');
        $I->click('Users');

        $I->expectTo('see that 1 user was created');
        $I->seeNumberOfElements('td[class~="role-provider"]', 4);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function testUpdateUser(AcceptanceTester $I)
    {
        $I->wantTo('ensure that update user page works');
        $I->amOpeningPage('user/index', ['username' => 'thecreator', 'password' => 'creator123']);

    }

}
