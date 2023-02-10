<?php

namespace tests\codeception\backend\_pages;

use Codeception\Actor;
use tests\codeception\backend\AcceptanceTester;
use tests\codeception\backend\FunctionalTester;
use tests\codeception\common\_pages\LoginPage;

/**
 * Represents UserService Page
 * Class UserServicePage
 * @package tests\codeception\backend\_pages
 */
class UserServicePage extends LoginPage
{
    public $route = 'user-service/view';

    /**
     * @param Actor|AcceptanceTester|FunctionalTester $I
     * @param array $params
     * @return UserServicePage|LoginPage
     */
    public static function openBy($I, $params = [])
    {
        $page = parent::openBy($I, $params);
        $page->login('thecreator', 'creator123');

        $I->expectTo('see user logged in and on page user services.');
        $I->seeLink('Logout (thecreator)');
        $I->see('User Services', 'h1');
        $I->seeInCurrentUrl('/user-service/'.$params['id']);

        return $page;
    }
}
