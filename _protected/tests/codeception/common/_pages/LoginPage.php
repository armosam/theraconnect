<?php

namespace tests\codeception\common\_pages;

use Exception;
use tests\codeception\common\_support\yii\BasePage;

/**
 * Represents Login Page
 */
class LoginPage extends BasePage
{
    public $route = 'site/login';

    /**
     * Method representing user submitting login form.
     *
     * @param string $user
     * @param string $password
     */
    public function login($user, $password)
    {
        /*if ($this->actor->loadSessionSnapshot('login')) {
            return;
        }*/

        $this->actor->fillField('input[name="LoginForm[username]"]', $user);
        $this->actor->fillField('input[name="LoginForm[password]"]', $password);
        $this->actor->fillFieldIfCanSee('input[name="LoginForm[verifyCode]"]', 'testme');
        $this->actor->click('login-button');

        if (method_exists($this->actor, 'wait')) {
            try {
                $this->actor->waitForText("Logout ($user)", 3); // only for selenium
            }catch (Exception $e){
                return;
            }
        }

        /*$this->actor->see("Logout ($user)");
        $this->actor->saveSessionSnapshot('login');*/
    }

    /**
     * Logs out from account
     */
    public function logout()
    {
        $this->actor->seeLink('Logout');
        $this->actor->click('Logout');

        if (method_exists($this->actor, 'wait')) {
            $this->actor->waitForText('Sign in to continue', 3, '.panel-heading'); // only for selenium
        }
    }
}
