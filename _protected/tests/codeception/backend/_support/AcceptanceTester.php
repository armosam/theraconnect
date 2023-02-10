<?php

namespace tests\codeception\backend;

use Yii;
use Exception;
use Codeception\Actor;
use Codeception\Lib\Friend;

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
        if($this->haveElement($field)){
            $this->fillField($field, $value);
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
        $this->wantTo("login as {$user} user");
        $page = Yii::$app->urlManager->createUrl('site/login');
        $this->amOnPage($page);
        $this->canSeeElement('input[name="LoginForm[username]"]');
        $this->fillField('input[name="LoginForm[username]"]', $user);
        $this->canSeeElement('input[name="LoginForm[password]"]');
        $this->fillField('input[name="LoginForm[password]"]', $password);
        $this->fillFieldIfCanSee('input[name="LoginForm[verifyCode]"]', 'testme');
        $this->click('#login-form button[type=submit]');

        if (method_exists($this, 'waitForElementVisible')) {
            try{
                $this->waitForText("Logout ({$user})", 3); // only for selenium
            }catch (Exception $e){
                return;
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
        if(!empty($loginData) && !empty($loginData['username']) && !empty($loginData['password'])){
            $this->loginAs($loginData['username'], $loginData['password']);
        }

        $page = Yii::$app->urlManager->createUrl($route);
        $this->amOnPage($page);
        if (method_exists($this, 'wait')) {
            $this->waitPageLoad(3); // only for selenium
        }
    }

    /**
     * Waits until ajax fully loads
     * @param $timeout
     */
    public function waitAjaxLoad($timeout = 10)
    {
        if (method_exists($this, 'waitForJS')) {
            $this->waitForJS('return !!window.jQuery && window.jQuery.active == 0;', $timeout);
        }
    }

    /**
     * Waits until page fully loads
     * @param $timeout
     */
    public function waitPageLoad($timeout = 10)
    {
        if (method_exists($this, 'waitForJS')) {
            $this->waitForJS('return document.readyState == "complete"', $timeout);
        }
        $this->waitAjaxLoad($timeout);
    }
}
