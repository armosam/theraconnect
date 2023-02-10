<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class AccountActivationException
 * @package common\exceptions
 */
class AccountActivationException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Account Activation Exception';
    }
}