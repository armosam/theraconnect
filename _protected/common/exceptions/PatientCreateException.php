<?php
namespace common\exceptions;

/**
 * Class PatientCreateException
 * @package common\exceptions
 */
class PatientCreateException extends PatientException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Patient Create Exception';
    }
}