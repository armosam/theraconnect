<?php
namespace common\exceptions;

/**
 * Class EmailVerificationException
 * @package common\exceptions
 */
class EmailVerificationException extends DataVerificationException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Email Verification Exception';
    }
}