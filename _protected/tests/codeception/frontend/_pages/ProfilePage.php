<?php

namespace tests\codeception\frontend\_pages;

use Codeception\Actor;
use tests\codeception\frontend\AcceptanceTester;
use tests\codeception\frontend\FunctionalTester;
use tests\codeception\common\_pages\LoginPage;

/**
 * Represents Profile Page
 * Class ProfilePage
 * @package tests\codeception\frontend\_pages
 */
class ProfilePage extends LoginPage
{
    public $route = 'profile/index';

    /**
     * @param Actor|AcceptanceTester|FunctionalTester $I
     * @param array $params
     * @return ProfilePage|LoginPage
     */
    public static function openBy($I, $params = [])
    {
        $page = parent::openBy($I, $params);
        $page->login('provider', 'provider123');

        $I->expectTo('see user logged in and on page my profile.');
        $I->seeLink('Logout (provider)');
        $I->see('My Profile', '.active');
        $I->seeInCurrentUrl('/profile');

        return $page;
    }
}
