<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class AccountTerminationException
 * @package common\exceptions
 */
class AccountTerminationException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Account Termination Exception';
    }
}