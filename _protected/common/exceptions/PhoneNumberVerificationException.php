<?php
namespace common\exceptions;

/**
 * Class PhoneNumberVerificationException
 * @package common\exceptions
 */
class PhoneNumberVerificationException extends DataVerificationException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Phone Number Verification Exception';
    }
}