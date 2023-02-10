<?php
namespace common\exceptions;

/**
 * Class SMSNotificationException
 * @package common\exceptions
 */
class SMSNotificationException extends NotificationException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'SMS Notification Exception';
    }
}