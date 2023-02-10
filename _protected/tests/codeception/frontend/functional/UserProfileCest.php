<?php

namespace tests\codeception\frontend\functional;

use Codeception\Event\FailEvent;
use Codeception\Event\TestEvent;
use common\models\User;
use common\models\ChangeHistory;
use tests\codeception\frontend\FunctionalTester;



/**
 * Class UserProfileCest
 * @package tests\codeception\frontend\functional
 * @group user_profile_functional
 */
class UserProfileCest
{
    /**
     * This method is called before each test method.
     *
     * @param TestEvent $event
     */
    public function _before($event)
    {
        ChangeHistory::deleteAll();
    }

    /**
     * This method is called after each test method, even if test failed.
     *
     * @param TestEvent $event
     */
    public function _after($event)
    {
        ChangeHistory::deleteAll();
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
    public function testChangeUserProfile($I)
    {
        $I->wantToTest('change user data like first last names in the profile (except changing email, phone1 or phone2)');

        $I->amGoingTo('login as customer and update profile page');
        $user = User::findOne([3]);
        $I->amLoggedInAs($user);
        $I->amOnPage('profile/index');
        $I->see($user->first_name);
        $I->see($user->last_name);

        // This code make freeze of tests

//        $I->click('Edit', '.btn-primary');


//        $I->fillFieldIfCanSee('User[first_name]', 'Xxxxx');
//        $I->fillFieldIfCanSee('User[last_name]', 'Yyyyyy');
//        $I->click('Save', 'button[type="submit"]');
//
//        $I->expectTo('see success message on the web site.');
//        $I->see('Your Changes Saved.', '.alert-success');

    }

    /**
     * @param FunctionalTester $I
     */
    public function testChangeUserEmail($I)
    {
        $I->wantToTest('change email address in the profile');

    }

    /**
     * @param FunctionalTester $I
     */
    public function testChangeUserPhone1($I)
    {
        $I->wantToTest('change of phone1 number in the profile');

    }

    /**
     * @param FunctionalTester $I
     */
    public function testChangeUserPhone2($I)
    {
        $I->wantToTest('change phone2 number in the profile');

    }

    /**
     * @param FunctionalTester $I
     */
    public function testChangeAgainNotVerifiedData($I)
    {
        $I->wantToTest('change of not verified data again');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testVerifyButtonForEmail($I)
    {
        $I->wantToTest('verify button to send verification email again');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testVerifyButtonForPhone($I)
    {
        $I->wantToTest('functionality of verify button to open dialog for verification');
    }

    /**
     * @param FunctionalTester $I
     */
    public function testDismissButton($I)
    {
        $I->wantToTest('dismiss button for email and phone numbers');
    }

}