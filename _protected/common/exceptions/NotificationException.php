<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class NotificationException
 * @package common\exceptions
 */
class NotificationException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Notification Exception';
    }
}