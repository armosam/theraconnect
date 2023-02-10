<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class AccountSuspensionException
 * @package common\exceptions
 */
class AccountSuspensionException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Account Suspension Exception';
    }
}