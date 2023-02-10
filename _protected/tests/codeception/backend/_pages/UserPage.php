<?php

namespace tests\codeception\backend\_pages;

use Codeception\Actor;
use tests\codeception\backend\AcceptanceTester;
use tests\codeception\backend\FunctionalTester;
use tests\codeception\common\_pages\LoginPage;

/**
 * Represents Service Page
 */
class UserPage extends LoginPage
{
    public $route = 'user/index';

    /**
     * @param Actor|AcceptanceTester|FunctionalTester $I
     * @param array $params
     * @return LoginPage
     */
    public static function openBy($I, $params = [])
    {
        $page = parent::openBy($I, $params);
        $page->login('thecreator', 'creator123');

        $I->expectTo('see user logged in and on page services.');
        $I->seeLink('Logout (thecreator)');
        $I->see('Users', 'h1');
        $I->seeInCurrentUrl('/users');

        return $page;
    }
}
