<?php

namespace common\components;

use Psr\Log\LoggerInterface;
use SendGrid;
use SendGrid\Mail;
use Swift_Events_EventListener;
use Swift_Mime_Attachment;
use Swift_Mime_SimpleMessage;
use Swift_Transport;
use Yii;

/**
 * SendGrid transport for Swift Mailer.
 * It implements transport for Swift Mailer
 *
 * Only add configuration in the mailer section.
 * Usage:
 *
 *  'mailer' => [
 *      'class' => 'yii\swiftmailer\Mailer',
 *      'viewPath' => '@common/mail',
 *      //'htmlLayout' => '@common/mail/layouts/html',
 *      //'textLayout' => '@common/mail/layouts/text',
 *      'useFileTransport' => true,
 *      'enableSwiftMailerLogging' => true,
 * THIS IS STANDARD TRANSPORT
 *      'transport' => [
 *          'class' => 'Swift_SmtpTransport',
 *          'host' => 'smtp.gmail.com',
 *          'username' => 'xxx@gmail.com',
 *          'password' => 'pass here',
 *          'port' => '465',
 *          'encryption' => 'ssl'
 *      ]
 * OR USE SENDGRID TRANSPORT
 *      'transport' => [
 *          'class' => 'common\components\SendGridTransport',
 *          'apiKey' => 'API KEY HERE' //Email Sending Only Key
 *      ]
 *  ]
 *
 * @author Andrej Rypák (dakujem) <xrypak@gmail.com>
 * @author Armen Bablanyan <thera@gmail.com>
 */
class SendGridTransport implements Swift_Transport
{
    /**
     * Sendgrid api key.
     *
     * @var string
     */
    private $apiKey;

    /**
     * On-ready event handlers.
     * The event fires when the Mail has been populated and is ready to be posted to SendGrid API.
     *
     * Note: this is where you may want to alter the Mail object using your own routines.
     *
     * handler signature: function(SendGrid\Mail, Swift_Mime_SimpleMessage, SendGridTransport): void
     *
     * @var callable[] on-ready event handlers
     */
    public $onReady = [];

    /**
     * On-error event handlers.
     * The event fires when an error occurs during the SendGrid API post call.
     *
     * handler signature: function(SendGrid\Mail, Swift_Mime_SimpleMessage, SendGrid\Response, SendGridTransport): void
     *
     * @var callable[] on-error event handlers
     */
    public $onError = [];

    /**
     * On-send event handlers.
     * The event fires when the Mail has been posted to the SendGrid API.
     *
     * handler signature: function(SendGrid\Mail, Swift_Mime_SimpleMessage, SendGrid\Response, SendGridTransport): void
     *
     * @var callable[] on-send event handlers
     */
    public $onSend = [];

    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @param string $value API Key
     */
    public function setApiKey($value){
        $this->apiKey = $value;
    }

    /**
     * @param LoggerInterface|null $logger
     */
    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param Swift_Mime_SimpleMessage $message
     * @param array $failedRecipients
     *
     * @return int
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        // create the Mail object
        $mail = $this->prepareMail($message);

        // fire on-ready event
        $this->fire($this->onReady, $mail, $message);

        // send the mail
        $response = $this->sendMail($mail);

        // fire on-send event
        $this->fire($this->onSend, $mail, $message, $response);
        if ($this->responseIsOk($response)) {
            // return the count of recipients
            // Note: it may happen that not all will actually be delivered...
            $count = count($message->getTo() ?? [] ) + count($message->getCc() ?? [] ) + count($message->getBcc() ?? [] );
            return $count;
        }

        // the API call is not valid, handle the error...
        // fire on-error event
        $this->fire($this->onError, $mail, $message, $response);

        // log
        if ($this->logger !== null) {
            $this->logger->error($response->statusCode() . ': ' . $response->body());
        }

        // failed recipients (all)
        foreach (array_keys(array_merge(($message->getTo() ?? []), ($message->getCc() ?? []), ($message->getBcc() ?? []))) as $recipient) {
            $failedRecipients[] = $recipient;
        }

        return 0; // Note: it may happen that some will actually be delivered...
    }

    /**
     * Convert a Swiftmailer message object into SendGrid Mail object.
     *
     * @param Swift_Mime_SimpleMessage $message
     * @return Mail
     */
    public function prepareMail(Swift_Mime_SimpleMessage $message): Mail
    {
        // Get the first from email
        $fromArr = $this->mapRecipients($message->getFrom() ?? []);
        $from = reset($fromArr);

        // Get first to address and remove it from array
        $toArr = $this->mapRecipients($message->getTo() ?? []);
        $to = array_shift($toArr);

        $ccArr = $this->mapRecipients($message->getCc() ?? []);
        $bccArr = $this->mapRecipients($message->getBcc() ?? []);
        $replyTo = $this->mapRecipients($message->getReplyTo() ?? []);

        // process attachments and multipart content
        $contents = [];
        $attachments = [];
        $parts = $message->getChildren() ?? [];
        if(!empty($parts)) {
            foreach ($parts as $part) {
                if ($part instanceof Swift_Mime_Attachment) {
                    $attachment = new SendGrid\Attachment();
                    $attachment->setContent(base64_encode($part->getBody()));
                    $attachment->setType($part->getContentType());
                    $attachment->setFilename($part->getFilename());
                    $attachment->setDisposition($part->getDisposition());
                    $attachment->setContentId($part->getId());
                    $attachments[] = $attachment;
                } elseif (in_array($part->getContentType(), ['text/plain', 'text/html'])) {
                    $content = new SendGrid\Content($part->getContentType(), $part->getBody());
                    $contents[$part->getContentType()] = $content;
                }
            }
        }

        $body = $message->getBody();
        if(empty($body)){
            if(empty($contents)){
                $content = new SendGrid\Content('text/plain', ' ');
            }else{
                $content = $contents['text/plain'];
                unset($contents['text/plain']);
            }
        }else{
            $bodyContentType = $message->getBodyContentType();
            $content = new SendGrid\Content($bodyContentType, $body);
        }

        $mail = new Mail($from, $message->getSubject(), $to, $content);

        if(!(empty($toArr) && empty($ccArr) && empty($bccArr)))
        {
            foreach($mail->getPersonalizations() as $personalization) {
                /** @var SendGrid\Personalization $personalization */
                array_map([$personalization, 'addTo'], $toArr);
                array_map([$personalization, 'addCc'], $ccArr);
                array_map([$personalization, 'addBcc'], $bccArr);
            }
        }

        array_map([$mail, 'addContent'], $contents);
        array_map([$mail, 'addAttachment'], $attachments);
        array_map([$mail, 'setReplyTo'], $replyTo);

        $mail->setMailSettings(['sandbox_mode' => ['enable' => YII_ENV_DEV]]);

        return $mail;
    }

    /**
     * @param Mail $mail
     * @return SendGrid\Response
     */
    public function sendMail(Mail $mail): SendGrid\Response
    {
        return (new SendGrid($this->apiKey))->client->mail()->send()->post($mail);
    }

    /**
     * Is the API call response OK?
     * 2xx responses indicate a successful request.
     *
     * @link https://sendgrid.com/docs/API_Reference/Web_API_v3/Mail/errors.html API call status documentation
     * @param SendGrid\Response $response
     * @return bool
     */
    protected function responseIsOk(SendGrid\Response $response): bool
    {
        return $response->statusCode() >= 200 && $response->statusCode() < 300;
    }

    /**
     * @param $eventHandlers
     * @param mixed ...$args
     */
    protected function fire($eventHandlers, ...$args): void
    {
        foreach ($eventHandlers as $handler) {
            call_user_func_array($handler, array_merge($args, [$this]));
        }
    }

    /**
     * Map swift recipients to SG emails.
     *
     * @param array $recipients
     * @return array
     */
    private function mapRecipients(array $recipients): array
    {
        return array_map(function($name, $email) {
            return new SendGrid\Email($name, $email);
        }, $recipients, array_keys($recipients));
    }

    public function ping()
    {
        // Not used
        return true;
    }
    public function isStarted()
    {
        // Not used
        return true;
    }
    public function start()
    {
        // Not used
    }
    public function stop()
    {
        // Not used
    }
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        // unused
    }
}