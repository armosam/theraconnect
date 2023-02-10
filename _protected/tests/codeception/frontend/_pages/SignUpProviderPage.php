<?php

namespace tests\codeception\frontend\_pages;

use tests\codeception\common\_support\yii\BasePage;

/**
 * Represents SignUpProvider Page
 */
class SignUpProviderPage extends BasePage
{
    public $route = 'site/sign-up-provider';

    /**
     * Method representing user submitting sign-up-provider form.
     *
     * @param array $signUpProviderData
     */
    public function submit(array $signUpProviderData)
    {
        foreach ($signUpProviderData as $field => $value)
        {
            if($field === 'agreed'){
                if(!empty($value)){
                    if($this->actor->haveElement('.bootstrap-switch-id-signupproviderform-agreed')){
                        $this->actor->click('.bootstrap-switch-id-signupproviderform-agreed');
                    }else{
                        $this->actor->checkOption('input[name="SignUpProviderForm[agreed]"][id="signupproviderform-agreed"]');
                    }
                }
            }else{
                $this->actor->fillField('input[name="SignUpProviderForm[' . $field . ']"]', $value);
            }
        }

        $this->actor->click('sign-up-button');

        if (method_exists($this->actor, 'wait')) {
            $this->actor->wait(3); // only for selenium
        }
    }
}
