<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class PatientException
 * @package common\exceptions
 */
class PatientException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Patient Exception';
    }
}