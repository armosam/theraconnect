<?php

namespace common\models\events;

use Yii;
use yii\base\Event;
use Exception;
use common\models\User;
use common\helpers\ConstHelper;
use common\models\notification\EmailNotification;
use common\models\notification\SMSNotification;
use common\exceptions\EmailNotificationException;
use common\exceptions\SMSNotificationException;

class UserEvents
{
    public const EVENT_ACCOUNT_CREATED = 'account_created';
    public const EVENT_ACCOUNT_CREATED_BY_ADMIN = 'account_created_by_admin';
    public const EVENT_ACCOUNT_ACTIVATED = 'account_activated';
    public const EVENT_ACCOUNT_ACTIVATED_BY_ADMIN = 'account_activated_by_admin';
    public const EVENT_ACCOUNT_UPDATED = 'account_updated';
    public const EVENT_ACCOUNT_UPDATED_BY_ADMIN = 'account_updated_by_admin';
    public const EVENT_ACCOUNT_SUSPENDED = 'account_suspended';
    public const EVENT_ACCOUNT_SUSPENDED_BY_ADMIN = 'account_suspended_by_admin';
    public const EVENT_ACCOUNT_TERMINATED = 'account_terminated';
    public const EVENT_ACCOUNT_TERMINATED_BY_ADMIN = 'account_terminated_by_admin';
    public const EVENT_ACCOUNT_ACTIVATION = 'account_activation';
    public const EVENT_ACCOUNT_PHONE_CHANGED = 'account_phone_changed';
    public const EVENT_ACCOUNT_EMAIL_CHANGED = 'account_email_changed';
    public const EVENT_ACCOUNT_PASSWORD_RESET = 'account_password_reset';

    /**
     * Handler method for accountCreatedEvent
     * @param Event $event
     */
    public function accountCreatedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try{
            if (Yii::$app->params['accountCreatedEmailNotification'] === ConstHelper::FLAG_YES) {
                $subject = Yii::t('app', 'Your account has been created.');
                $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_CREATED, $subject, $event->data, $user->email);
                if(!$emailNotification->send()){
                    throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account creation by provided email address.'));
                }
            }
            if (Yii::$app->params['accountCreatedSmsNotification'] === ConstHelper::FLAG_YES) {
                $message = Yii::t('sms', 'Your account has been created.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if(!$smsNotification->send()){
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account creation by provided phone number․'));
                }
            }
        }catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Handler method for accountCreatedByAdminEvent
     * @param Event $event
     * @throws Exception
     */
    public function accountCreatedByAdminEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try{
            if (Yii::$app->params['accountCreatedEmailNotification'] === ConstHelper::FLAG_YES) {
                $subject = Yii::t('app', 'Your account has been created by administration.');
                $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_CREATED_BY_ADMIN, $subject, $event->data, $user->email);
                if(!$emailNotification->send()){
                    throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account creation by provided email address.'));
                }
            }
            if (Yii::$app->params['accountCreatedSmsNotification'] === ConstHelper::FLAG_YES) {
                $message = Yii::t('app', 'Your account created by administration. Please use this link to set your password {password-reset-link}', [
                    'password-reset-link' => Yii::$app->urlManagerToFront->createAbsoluteUrl(['site/password-reset', 'token'=> $user->password_reset_token])]);
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if(!$smsNotification->send()){
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account creation by provided phone number․'));
                }
            }
        }catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Handler method of accountUpdatedEvent
     * @param Event $event
     */
    public function accountUpdatedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            if ($user->note_email_account_updated === ConstHelper::FLAG_YES) {
                $subject = Yii::t('app', 'Your account has been updated.');
                $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_UPDATED, $subject, $event->data, $user->email);
                if(!$emailNotification->send()){
                    throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account update by provided email address.'));
                }
            }
            if ($user->note_sms_account_updated === ConstHelper::FLAG_YES) {
                $message = Yii::t('app', 'Your account has been updated.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if(!$smsNotification->send()){
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account update by provided phone number․'));
                }
            }
        }catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Handler method for accountUpdateByAdmin event
     * @param Event $event
     */
    public function accountUpdatedByAdminEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            if ($user->note_email_account_updated === ConstHelper::FLAG_YES) {
                $subject = Yii::t('app', 'Your account has been updated by administration.');
                $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_UPDATED_BY_ADMIN, $subject, $event->data, $user->email);
                if(!$emailNotification->send()){
                    throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account update by provided email address.'));
                }
            }
            if ($user->note_sms_account_updated === ConstHelper::FLAG_YES) {
                $message = Yii::t('app', 'Your account has been updated by administration.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if(!$smsNotification->send()){
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account update by provided phone number․'));
                }
            }
        }catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Currently Account activation email is sending from AccountActivation form class and ServiceRequest form class
     * Account activation event handler
     * @param Event $event
     */
    public function accountActivationEventHandler(Event $event)
    {
    }

    /**
     * Account activated event handler
     * @param Event $event
     */
    public function accountActivatedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            $subject = Yii::t('app', 'Your account has been activated.');
            // Check if account is for provider then set correct mail template
            if($user->role->item_name === User::USER_PROVIDER){
                $emailTemplate = EmailNotification::NOTIFICATION_ACCOUNT_FOR_PROVIDER_ACTIVATED;
            }else{
                $emailTemplate = EmailNotification::NOTIFICATION_ACCOUNT_ACTIVATED;
            }
            $emailNotification = new EmailNotification($user, $emailTemplate, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account activation by provided email address.'));
            }
            if (!(empty($user->phone1) && empty($user->phone2))) {
                $message = Yii::t('app', 'Your account has been activated.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if (!$smsNotification->send()) {
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account activation by provided phone number․'));
                }
            }
        } catch (Exception $e) {
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Account activated by administration event handler
     * @param Event $event
     */
    public function accountActivatedByAdminEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            $subject = Yii::t('app', 'Your account has been activated by administration.');
            $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_ACTIVATED_BY_ADMIN, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account activation by provided email address.'));
            }
            if (!(empty($user->phone1) && empty($user->phone2))) {
                $message = Yii::t('app', 'Your account has been activated by administration.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if (!$smsNotification->send()) {
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account activation by provided phone number․'));
                }
            }
        } catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * @param Event $event
     */
    public function accountSuspendedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            $subject = Yii::t('app', 'Your account has been suspended.');
            $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_SUSPENDED, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account suspension by provided email address.'));
            }

            if (!(empty($user->phone1) && empty($user->phone2))){
                $message = Yii::t('app', 'Your account has been suspended.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if (!$smsNotification->send()) {
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account suspension by provided phone number․'));
                }
            }
        } catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * @param Event $event
     */
    public function accountSuspendedByAdminEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            $subject = Yii::t('app', 'Your account has been suspended by administration.');
            $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_SUSPENDED_BY_ADMIN, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account suspension by provided email address.'));
            }
            if (!(empty($user->phone1) && empty($user->phone2))) {
                $message = Yii::t('app', 'Your account has been suspended by administration.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if (!$smsNotification->send()) {
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account suspension by provided phone number․'));
                }
            }
        } catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * @param Event $event
     */
    public function accountTerminatedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            $subject = Yii::t('app', 'Your account has been terminated.');
            $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_TERMINATED, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account termination by provided email address.'));
            }
            if (!(empty($user->phone1) && empty($user->phone2))) {
                $message = Yii::t('app', 'Your account has been terminated.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if (!$smsNotification->send()) {
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account termination by provided phone number.'));
                }
            }
        } catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Account terminated by administration event handler
     * @param Event $event
     */
    public function accountTerminatedByAdminEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            $subject = Yii::t('app', 'Your account has been terminated by administration.');
            $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_TERMINATED_BY_ADMIN, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about account termination by provided email address.'));
            }
            if (!(empty($user->phone1) && empty($user->phone2))) {
                $message = Yii::t('app', 'Your account has been terminated by administration.');
                $smsNotification = new SMSNotification($user, $message, $event->data);
                if (!$smsNotification->send()) {
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about account termination by provided phone number.'));
                }
            }
        } catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Account email address changed event handler
     * @param Event $event
     */
    public function accountEmailChangedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            if(empty($event->data->verification_code)){
                throw new EmailNotificationException(Yii::t('app', 'Email verification token is not generated.'));
            }
            $subject = Yii::t('app', 'Your account has been updated and email address changed.');
            $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_EMAIL_CHANGED, $subject, ['ChangeHistory' => $event->data], $event->data->new_value);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email message with email verification link.'));
            }
            Yii::$app->getSession()->addFlash('success', Yii::t('app', 'Verification email has been sent to your new email address. Please check your email and verify email address.'));
        } catch (Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Account phone number changed event handler
     * @param Event $event
     */
    public function accountPhoneChangedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        try {
            if(empty($event->data->verification_code)){
                throw new EmailNotificationException(Yii::t('app', 'Phone number verification code is not generated.'));
            }
            $message = Yii::t('app', 'Please go to your profile and use the code {phone_validation_code} to verify your new phone number.', [
                'phone_validation_code' => $event->data->verification_code]);
            $smsNotification = new SMSNotification($user, $message, [$event->data->field_name => $event->data->new_value]);
            if (!$smsNotification->send()) {
                throw new SMSNotificationException(Yii::t('app', 'We cannot send you a message with a verification code to the phone number provided.'));
            }
            Yii::$app->getSession()->addFlash('success', Yii::t('app', 'Verification code has been sent to your new phone number. Please check your phone and verify phone number.'));
        }catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }

    /**
     * Currently password reset notification is sending from password reset form
     * Account password reset event handler
     * @param Event $event
     */
    public function accountPasswordResetEventHandler(Event $event)
    {

        /** @var User $user */
        /*$user = $event->sender;
        try {

            $subject = Yii::t('app', 'Reset your account password.');
            $emailNotification = new EmailNotification($user, EmailNotification::NOTIFICATION_ACCOUNT_PASSWORD_RESET, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, user unable to receive email about account password reset by email provided.'));
            }

            $message = Yii::t('sms', 'Your account password changed. Please use this link to set your password {password-reset-link}', [
                'password-reset-link' => Yii::$app->urlManagerToFront->createAbsoluteUrl(['site/password-reset', 'token'=> $user->password_reset_token])]);
            $smsNotification = new SMSNotification($user, $message, $event->data);
            if (!$smsNotification->send()) {
                throw new SMSNotificationException(Yii::t('app', 'Sorry, user unable to receive SMS message about account password reset by phone number provided.'));
            }

        } catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'UserEvent-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }*/
    }
}