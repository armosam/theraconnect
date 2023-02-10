<?php

namespace common\models\notification;

/**
 * Class SMSNotification
 * @package common\models\base
 */
class SMSNotification extends Notification
{
    /**
     * @var string $message
     */
    public $message;

    public function __construct($user, $message = '', $data = [])
    {
        $this->user = $user;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Sends SMS message notification
     * @return bool
     */
    public function send(): bool
    {
        return self::sendNotificationSms($this);
    }
}