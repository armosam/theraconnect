<?php

namespace tests\codeception\frontend\_pages;

use Codeception\Actor;
use tests\codeception\frontend\AcceptanceTester;
use tests\codeception\frontend\FunctionalTester;
use tests\codeception\common\_pages\LoginPage;

/**
 * Represents UserService Page
 * Class UserServicePage
 * @package tests\codeception\frontend\_pages
 */
class UserServicePage extends LoginPage
{
    public $route = 'user-service/index';

    /**
     * @param Actor|AcceptanceTester|FunctionalTester $I
     * @param array $params
     * @return UserServicePage|LoginPage
     */
    public static function openBy($I, $params = [])
    {
        $page = parent::openBy($I, $params);
        $page->login('provider', 'provider123');

        $I->expectTo('see user logged in and on page user services.');
        $I->seeLink('Logout (provider)');
        $I->see('My Services', '.active');
        $I->seeInCurrentUrl('/user-services');

        return $page;
    }
}
