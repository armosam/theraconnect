<?php

namespace tests\codeception\frontend;

use Yii;
use Exception;
use Codeception\Actor;
use Codeception\Lib\Friend;
use PHPUnit\Framework\AssertionFailedError;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends Actor
{
    use _generated\AcceptanceTesterActions;

   /**
    * Define custom actions here
    */

    /**
     * Method checks if field exists then try to fill value
     * @param string $field
     * @param string $value
     */
    public function fillFieldIfCanSee($field, $value)
    {
        $I = $this;
        if($I->haveElement($field)){
            $I->fillField($field, $value);
        }
    }

    /**
     * Checks if element exists
     * @param string $field Element's selector
     * @return bool
     */
    public function haveElement($field)
    {
        $element = $this->grabMultiple($field);
        return empty($element) ? false : true;
    }

    /**
     * Login user with username and password
     * @param string $user
     * @param string $password
     */
    public function loginAs($user, $password)
    {
        $I = $this;
        /*if ($I->loadSessionSnapshot("login_{$user}_{$password}") ){
            return;
        }*/
        $page = Yii::$app->urlManager->createUrl('site/login');
        $I->amOnPage($page);
        $I->fillFieldIfCanSee('input[name="LoginForm[username]"]', $user);
        $I->fillFieldIfCanSee('input[name="LoginForm[password]"]', $password);
        $I->fillFieldIfCanSee('input[name="LoginForm[verifyCode]"]', 'testme');
        $I->click('#login-form button[type=submit]');
        if (method_exists($I, 'wait')) {
            $I->wait(1);
        }
        //$I->saveSessionSnapshot("login_{$user}_{$password}");
    }

    /**
     * @param string $user
     */
    public function seeLoggedInAs($user)
    {
        $I = $this;
        if (method_exists($I, 'wait')) {
            try{
                $I->waitForElement('#setting_dropdown > a', 3);
                $I->seeInPageSource("Logout ({$user})");
                $I->click('#setting_dropdown > a');
                $I->waitForText("Logout ({$user})", 3);
            }catch (Exception $e){
                return;
            }
        }
        $I->see("Logout ({$user})");
    }

    /**
     * Logout logged user
     * @version  This method should work only for WebDrivers.
     * It needs javascript support to do post request
     */
    public function logout()
    {
        $I = $this;
        $page = Yii::$app->urlManager->createUrl('site/index');
        $I->amOnPage($page);
        if($I->haveElement('#logout_desktop')) {
            if (method_exists($I, 'wait')) {
                if($I->haveElement('#setting_dropdown > a')) {
                    $I->click('#setting_dropdown > a');
                    $I->seeElement('#logout_desktop');
                    $I->click('#logout_desktop');
                    $I->waitPageLoad(3);
                }
            }
        }
    }

    /**
     * Open page with or without login before
     * @param string|array $route
     * @param null|array $loginData
     */
    public function amOpeningPage($route, $loginData = [])
    {
        $I = $this;
        if(!empty($loginData) && !empty($loginData['username']) && !empty($loginData['password'])){
            $I->loginAs($loginData['username'], $loginData['password']);
        }

        $page = Yii::$app->urlManager->createUrl($route);
        $I->amOnPage($page);
        if (method_exists($I, 'wait')) {
            $I->waitPageLoad(3);
        }
    }

    /**
     * Waits until ajax fully loads
     * @param $timeout
     */
    public function waitAjaxLoad($timeout = 10)
    {
        $I = $this;
        if (method_exists($I, 'waitForJS')) {
            $I->waitForJS('return !!window.jQuery && window.jQuery.active == 0;', $timeout);
        }
    }

    /**
     * Waits until page fully loads
     * @param $timeout
     */
    public function waitPageLoad($timeout = 10)
    {
        $I = $this;
        if (method_exists($I, 'waitForJS')) {
            $I->waitForJS('return document.readyState == "complete"', $timeout);
        }
        $I->waitAjaxLoad($timeout);
    }

}
