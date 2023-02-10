<?php

namespace common\exceptions;

use yii\base\Exception;

/**
 * Class UserNotFoundException
 * @package common\exceptions
 */
class UserNotFoundException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'User Not Found Exception';
    }
}