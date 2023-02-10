<?php

namespace common\models\notification;

/**
 * Class EmailNotification
 * @package common\models\notification
 */
class EmailNotification extends Notification
{
    /** User Notifications */
    public const NOTIFICATION_ACCOUNT_ACTIVATED = 'accountActivated';
    public const NOTIFICATION_ACCOUNT_FOR_PROVIDER_ACTIVATED = 'accountForProviderActivated';
    public const NOTIFICATION_ACCOUNT_PASSWORD_RESET = 'accountPasswordResetToken';
    public const NOTIFICATION_ACCOUNT_SUSPENDED_BY_ADMIN = 'accountSuspendedByAdmin';
    public const NOTIFICATION_ACCOUNT_UPDATED_BY_ADMIN = 'accountUpdatedByAdmin';
    public const NOTIFICATION_ACCOUNT_ACTIVATED_BY_ADMIN = 'accountActivatedByAdmin';
    public const NOTIFICATION_ACCOUNT_TERMINATED_BY_ADMIN = 'accountTerminatedByAdmin';
    public const NOTIFICATION_ACCOUNT_EMAIL_CHANGED = 'accountEmailChangedToken';
    public const NOTIFICATION_ACCOUNT_CREATED_BY_ADMIN = 'accountCreatedByAdmin';
    public const NOTIFICATION_ACCOUNT_ACTIVATION = 'accountActivationToken';
    public const NOTIFICATION_ACCOUNT_TERMINATED = 'accountTerminated';
    public const NOTIFICATION_ACCOUNT_CREATED = 'accountCreated';
    public const NOTIFICATION_ACCOUNT_SUSPENDED = 'accountSuspended';
    public const NOTIFICATION_ACCOUNT_UPDATED = 'accountUpdated';

    /** Order notifications */
    public const NOTIFICATION_ORDER_SUBMITTED_TO_PROVIDER = 'orderSubmittedToProvider';
    public const NOTIFICATION_ORDER_SUBMITTED_TO_CUSTOMER = 'orderSubmittedToCustomer';
    public const NOTIFICATION_ORDER_SUBMITTED_BY_ADMIN_TO_PROVIDER = 'orderSubmittedByAdminToProvider';
    public const NOTIFICATION_ORDER_SUBMITTED_BY_ADMIN_TO_CUSTOMER = 'orderSubmittedByAdminToCustomer';

    public const NOTIFICATION_ORDER_ACCEPTED_TO_PROVIDER = 'orderAcceptedToProvider';
    public const NOTIFICATION_ORDER_ACCEPTED_TO_CUSTOMER = 'orderAcceptedToCustomer';
    public const NOTIFICATION_ORDER_ACCEPTED_BY_ADMIN_TO_PROVIDER = 'orderAcceptedByAdminToProvider';
    public const NOTIFICATION_ORDER_ACCEPTED_BY_ADMIN_TO_CUSTOMER = 'orderAcceptedByAdminToCustomer';

    public const NOTIFICATION_ORDER_REJECTED_TO_PROVIDER = 'orderRejectedToProvider';
    public const NOTIFICATION_ORDER_REJECTED_TO_CUSTOMER = 'orderRejectedToCustomer';
    public const NOTIFICATION_ORDER_REJECTED_BY_ADMIN_TO_PROVIDER = 'orderRejectedByAdminToProvider';
    public const NOTIFICATION_ORDER_REJECTED_BY_ADMIN_TO_CUSTOMER = 'orderRejectedByAdminToCustomer';

    public const NOTIFICATION_ORDER_CANCELED_BY_PROVIDER_TO_PROVIDER = 'orderCanceledByProviderToProvider';
    public const NOTIFICATION_ORDER_CANCELED_BY_PROVIDER_TO_CUSTOMER = 'orderCanceledByProviderToCustomer';
    public const NOTIFICATION_ORDER_CANCELED_BY_CUSTOMER_TO_PROVIDER = 'orderCanceledByCustomerToProvider';
    public const NOTIFICATION_ORDER_CANCELED_BY_CUSTOMER_TO_CUSTOMER = 'orderCanceledByCustomerToCustomer';
    public const NOTIFICATION_ORDER_CANCELED_BY_ADMIN_TO_PROVIDER = 'orderCanceledByAdminToProvider';
    public const NOTIFICATION_ORDER_CANCELED_BY_ADMIN_TO_CUSTOMER = 'orderCanceledByAdminToCustomer';
    public const NOTIFICATION_ORDER_CANCELED_BY_SYSTEM_TO_PROVIDER = 'orderCanceledBySystemToProvider';
    public const NOTIFICATION_ORDER_CANCELED_BY_SYSTEM_TO_CUSTOMER = 'orderCanceledBySystemToCustomer';

    public const NOTIFICATION_ORDER_PROVIDER_REMINDER = 'orderProviderReminder';

    public const NOTIFICATION_ORDER_RATE_AS_PROVIDER = 'orderRateAsProvider';
    public const NOTIFICATION_ORDER_RATE_AS_CUSTOMER = 'orderRateAsCustomer';


    /** NOTE notifications */
    public const NOTIFICATION_NOTE_PROGRESS_NEEDS_RPT_SIGNATURE = 'noteProgressNeedsRptSignature';


    /**
     * @var string $view
     */
    public $view;

    /**
     * @var string $subject
     */
    public $subject;

    /**
     * @var string $recipient_email
     */
    public $recipient_email;

    public function __construct($user, $view, $subject = '', $data = [], $recipient_email = '')
    {
        $this->setUser($user);
        $this->setView($view);
        $this->setSubject($subject);
        $this->setData($data);
        $this->setRecipientEmail($recipient_email);
    }

    /**
     * @param string $view
     */
    public function setView(string $view): void
    {
        $this->view = $view;
    }

    /**
     * @return string
     */
    public function getView(): string
    {
        return $this->view;
    }

    /**
     * @param string $subject
     */
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $recipient_email
     */
    public function setRecipientEmail(string $recipient_email): void
    {
        $this->recipient_email = $recipient_email;
    }

    /**
     * @return string
     */
    public function getRecipientEmail(): string
    {
        return $this->recipient_email;
    }

    /**
     * Sends email notification
     * @return bool
     */
    public function send(): bool
    {
        return self::sendNotificationEmail($this);
    }
}