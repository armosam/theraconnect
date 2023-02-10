<?php

namespace common\models\notification;

use common\models\User;
use common\traits\NotificationTrait;

/**
 * Class Notification
 * @package common\models\notification
 */
abstract class Notification
{
    use NotificationTrait;

    /**
     * @var User $user
     */
    public $user;

    /**
     * @var array $data
     */
    public $data;

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}