<?php

namespace console\controllers;

use Yii;
use Exception;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\LogArchive;

/**
 * Class LogController
 */
class LogController extends Controller
{
    /**
     * Deletes all records in table log_archive.
     */
    public function actionClearArchive()
    {
        $deleted = LogArchive::deleteAll();

        $message = "Deleted $deleted logs from archive";

        Yii::info($message, self::class);
        $this->stdout($message . PHP_EOL, Console::FG_GREEN);
    }

    /**
     * Archives and deletes all logs (Log) older than given in configuration days
     */
    public function actionArchive()
    {
        try {
            $this->stdout(Yii::$app->logArchive->archiveAll());
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), self::class);
            $this->stdout('Command failed. '.$e->getMessage());
        }
    }

    /**
     * Sends email message with attached log file given as argument
     * @param string $subject Email subject
     * @param string $log_file_path Log file full path to attach to email
     */
    public function actionMail($subject, $log_file_path)
    {
        try {
            $hasLog = (!empty($log_file_path) && is_file($log_file_path) && is_readable($log_file_path));

            $mail = Yii::$app->mailer->compose('errorLogForAdmin', ['hasLog' => $hasLog])
                ->setFrom([Yii::$app->params['fromEmailAddress'] => Yii::$app->name . ' Support'])
                ->setReplyTo([Yii::$app->params['supportEmail'] => Yii::$app->name . ' Support'])
                ->setTo([Yii::$app->params['systemNotificationEmailAddress'] => 'System Administration'])
                ->setSubject(empty($subject) ? 'Subject is empty.' : $subject);

            if ($hasLog) {
                $mail->attach($log_file_path);
            }

            if (!$mail->send()) {
                throw new Exception('Email message has been failed to sent due to issue.');
            }

            Yii::info('Email message with attached log file has been sent successfully.');

        } catch (Exception $e) {
            Yii::error($e->getMessage());
        }
    }
}
