<?php
namespace common\exceptions;

use yii\base\Exception;

/**
 * Class DataVerificationException
 * @package common\exceptions
 */
class DataVerificationException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Data Verification Exception';
    }
}