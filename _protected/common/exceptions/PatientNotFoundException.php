<?php
namespace common\exceptions;


/**
 * Class PatientNotFoundException
 * @package common\exceptions
 */
class PatientNotFoundException extends PatientException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Patient Not Found Exception';
    }
}