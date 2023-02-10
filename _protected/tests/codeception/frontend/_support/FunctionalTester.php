<?php

namespace tests\codeception\frontend;

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
class FunctionalTester extends Actor
{
    use _generated\FunctionalTesterActions;

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
     * Removes unnecessary parts from email body
     * @param string $content
     * @return string|string[]
     */
    public function amNormalizingEmailContent($content)
    {
        return str_replace("=\r\n", '', $content);
    }
}
