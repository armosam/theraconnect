<?php

namespace common\models\events;

use Yii;
use yii\base\Event;
use Exception;
use common\models\NoteProgress;
use common\models\notification\EmailNotification;
use common\models\notification\SMSNotification;
use common\exceptions\EmailNotificationException;
use common\exceptions\SMSNotificationException;

class NoteProgressEvent
{
    public const EVENT_NOTE_PROGRESS_SUBMITTED_BY_PTA = 'event_note_progress_submitted_by_pta';

    /**
     * Handler method for noteProgressSubmittedByPtaEvent
     * @param Event $event
     */
    public function noteProgressSubmittedByPtaEventHandler(Event $event)
    {
        /** @var NoteProgress $note */
        $note = $event->sender;
        try {
            $subject = Yii::t('app', 'Please sign progress note submitted by PTA.');
            $emailNotification = new EmailNotification($note->order->orderRPT, EmailNotification::NOTIFICATION_NOTE_PROGRESS_NEEDS_RPT_SIGNATURE, $subject, $event->data);
            if (!$emailNotification->send()) {
                throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send an email notification to RPT to sign this progress note.'));
            }
            if (!(empty($note->order->orderRPT->phone1) && empty($note->order->orderRPT->phone2))) {
                $message = Yii::t('app', 'Please sign progress note submitted by PTA');
                $smsNotification = new SMSNotification($note->order->orderRPT, $message, $event->data);
                if (!$smsNotification->send()) {
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send a SMS notification to RPT to sign this progress note.'));
                }
            }
        } catch(Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'NoteProgressEvent::'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }
}