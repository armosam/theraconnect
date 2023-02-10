<?php

namespace common\traits;

use Yii;
use yii\validators\EmailValidator;
use common\models\notification\EmailNotification;
use common\models\notification\SMSNotification;
use common\exceptions\EmailNotificationException;
use common\exceptions\SMSNotificationException;
use Exception;

/**
 * Trait NotificationsTrait
 */
trait NotificationTrait
{
    /**
     * Sends an email notification about event
     *
     * @param EmailNotification $notification
     * @return bool Whether the email was send.
     */
    protected static function sendNotificationEmail(EmailNotification $notification)
    {
        try{
            if(isset(Yii::$app->params['disableEmailNotifications']) && Yii::$app->params['disableEmailNotifications'] === true){
                Yii::warning('Currently Email notification sending is disabled. Please refer to configuration "disableEmailNotifications"');
                return true;
            }

            $validator = new EmailValidator();
            $email_address = !empty($notification->recipient_email) ? $notification->recipient_email : $notification->user->email;
            if (empty($email_address) || !$validator->validate($email_address)){
                throw new EmailNotificationException('Email Address is empty.');
            }

            $mail = Yii::$app->mailer->compose($notification->view, $notification->data)
                ->setFrom([Yii::$app->params['fromEmailAddress'] => Yii::$app->name . ' Support'])
                ->setReplyTo([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Support'])
                ->setTo([$email_address => $notification->user->getUserFullName()])
                ->setSubject($notification->subject);

            // Sets system notification email address as BCC recipient
            if(!empty(Yii::$app->params['systemNotificationEmailAddress'])){
                $systemEmails = explode(',', Yii::$app->params['systemNotificationEmailAddress']);
                if(!empty($systemEmails)){
                    $mail->setBcc($systemEmails);
                }
            }

            if(!$mail->send()){
                throw new EmailNotificationException('Email notification message has not been sent due to issue.');
            }

            return true;

        }catch(EmailNotificationException $e){
            Yii::error($e->getMessage(), 'NotificationTrait-'.__FUNCTION__);
        }catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'NotificationTrait-'.__FUNCTION__);
        }

        return false;
    }

    /**
     * Send SMS notification about event
     * @param SMSNotification $notification
     * @return bool
     */
    protected static function sendNotificationSms(SMSNotification $notification)
    {
        try{
            if(isset(Yii::$app->params['disableSMSNotifications']) && Yii::$app->params['disableSMSNotifications'] === true){
                    Yii::warning('Currently SMS notification sending is disabled. Please refer to configuration "disableSMSNotifications"');
                return true;
            }

            $recipient = null;

            if(isset($notification->data['phone1']) && !empty($notification->data['phone1'])){
                $recipient = $notification->data['phone1'];
            }

            if(empty($recipient) && isset($notification->data['phone2']) && !empty($notification->data['phone2'])){
                $recipient = $notification->data['phone2'];
            }

            if(empty($recipient) && isset($notification->user->phone1) && !empty($notification->user->phone1)){
                $recipient = $notification->user->phone1;
            }

            if(empty($recipient) && isset($notification->user->phone2) && !empty($notification->user->phone2)){
                $recipient = $notification->user->phone2;
            }

            if (empty($recipient)){
                throw new SMSNotificationException('Phone Number is empty.');
            }

            if(!Yii::$app->twilio->sendSMS($recipient, $notification->message)){
                throw new SMSNotificationException('SMS notification message has not been sent due to issue.');
            }

            return true;

        }catch(SMSNotificationException $e){
            Yii::error($e->getMessage(), 'NotificationTrait-'.__FUNCTION__);
        }catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'NotificationTrait-'.__FUNCTION__);
        }

        return false;
    }
}

