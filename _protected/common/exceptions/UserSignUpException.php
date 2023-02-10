<?php
namespace common\exceptions;

use yii\base\Exception;

/**
 * Class UserSignUpException
 * @package common\exceptions
 */
class UserSignUpException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'User Sign Up Exception';
    }
}