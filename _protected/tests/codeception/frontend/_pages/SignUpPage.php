<?php

namespace tests\codeception\frontend\_pages;

use tests\codeception\common\_support\yii\BasePage;

/**
 * Represents SignUp Page
 */
class SignUpPage extends BasePage
{
    public $route = 'site/sign-up';

    /**
     * Method representing user submitting sign-up form.
     *
     * @param array $signUpData
     */
    public function submit(array $signUpData)
    {
        foreach ($signUpData as $field => $value)
        {
            if($field === 'agreed'){
                if(!empty($value)){
                    if($this->actor->haveElement('.bootstrap-switch-id-signupform-agreed')){
                        $this->actor->click('.bootstrap-switch-id-signupform-agreed');
                    }else{
                        $this->actor->checkOption('input[name="SignUpForm[agreed]"][id="signupform-agreed"]');
                    }
                }
            }else{
                $this->actor->fillField('input[name="SignUpForm[' . $field . ']"]', $value);
            }
        }
        
        $this->actor->click('sign-up-button');

        if (method_exists($this->actor, 'wait')) {
            $this->actor->wait(3); // only for selenium
        }
    }
}
