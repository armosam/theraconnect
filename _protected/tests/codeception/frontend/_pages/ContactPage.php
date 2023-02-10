<?php

namespace tests\codeception\frontend\_pages;

use tests\codeception\common\_support\yii\BasePage;

/**
 * Represents Contact Page
 */
class ContactPage extends BasePage
{
    public $route = 'site/contact';

    /**
     * Method representing user submitting contact form.
     *
     * @param array $contactData
     */
    public function submit(array $contactData)
    {
        foreach ($contactData as $field => $value) 
        {
            $inputType = $field === 'body' ? 'textarea' : 'input';

            $this->actor->fillField($inputType . '[name="ContactForm[' . $field . ']"]', $value);
        }
        
        $this->actor->click('contact-button');

        if (method_exists($this->actor, 'wait')) {
            $this->actor->wait(1); // only for selenium
        }
    }
}
