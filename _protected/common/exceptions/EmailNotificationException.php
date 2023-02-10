<?php
namespace common\exceptions;

/**
 * Class EmailNotificationException
 * @package common\exceptions
 */
class EmailNotificationException extends NotificationException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Email Notification Exception';
    }
}