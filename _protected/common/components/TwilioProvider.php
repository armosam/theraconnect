<?php

namespace common\components;

use Yii;
use Exception;
use Twilio\Rest\Client;
use yii\base\Component;
use yii\base\ErrorException;
use yii\helpers\VarDumper;
use common\exceptions\SMSNotificationException;

/**
 * This is a Twilio provider class to allow us use Twilio's API calls
 *
 * Class TwilioProvider
 * @package common\components
 */
class TwilioProvider extends Component
{
    /**
     * The internal Twilio client.
     * @var Client $client
     */
    private $client;

    /** @var string $account_sid -> Twilio Account ID */
    public $account_sid;

    /** @var string $auth_key -> Twilio Authorization Key */
    public $auth_key;

    /** @var string $from_number */
    public $from_number;

    /** @var string $senderID */
    public $senderID;

    /** @var string $_prefix */
    protected $_prefix = '+';

    /** @var bool $_hasSenderIDSupport */
    protected $_hasSenderIDSupport = false;

    /**
     * init() called by yii.
     * @throws Exception
     */
    public function init()
    {
        parent::init();
        if(empty($this->twilioClass)){
            try {
                $this->client = new Client($this->account_sid, $this->auth_key);
            } catch (Exception $e) {
                throw $e;
            }
        }
    }

    /**
     * @param string $recipient
     * @param string $message
     * @return bool
     * @throws ErrorException
     * @throws SMSNotificationException
     */
    public function sendSMS($recipient, $message)
    {
        if(empty($this->from_number)){
            throw new ErrorException('From Number is not configured in the main configuration.');
        }

        if(empty($recipient)){
            throw new ErrorException('Recipient number is empty.');
        }

        $recipient_number = $this->_prepareRecipientNumber($recipient);

        try {
            $from_number = $this->prepareFromNumber();
            $result = $this->client->account->messages->create(
                $recipient_number,
                [
                    "from" => $from_number,
                    "body" => $message,
                ]
            );

            if(!empty($result->errorMessage)){
                throw new SMSNotificationException(sprintf('There is an error: (%s) to send SMS text message: %s, from: %s to: %s', $result->errorMessage, $result->body, $result->from, $result->to));
            }

            Yii::info(sprintf('SMS text message: (%s) sent from: %s to: %s', $result->body, $result->from, $result->to), 'TwilioProvider_'.__FUNCTION__);
            return true;

        } catch (SMSNotificationException $e) {
            Yii::error($e->getMessage(), 'TwilioProvider_'.__FUNCTION__);
        } catch (Exception $e) {
            Yii::error(sprintf('Error in text message sending to the number: %s %s.%s: error_code: %s, error_message: %s', $recipient_number,__CLASS__, __FUNCTION__, $e->getCode(), VarDumper::dumpAsString($e->getMessage())), 'TwilioProvider_'.__FUNCTION__);
        }
        return false;
    }

    /**
     * Returns normalized from number
     * @return string
     */
    protected function prepareFromNumber()
    {
        $from_number = preg_replace('/[^0-9]/', '', $this->from_number);
        return ($this->checkSenderIDSupport($this->from_number)) ? $this->senderID : ($this->_prefix . $from_number);
    }

    /**
     * Checks senderID support by given recipient number
     * @param string $recipient_number
     * @return bool
     */
    protected function checkSenderIDSupport($recipient_number)
    {
        $this->_hasSenderIDSupport = false;
        if(strpos($recipient_number, 374) === 0 || $recipient_number[0] === 7){
            $this->_hasSenderIDSupport = true;
        }
        return $this->_hasSenderIDSupport;
    }

    /**
     * Prepare recipient phone number
     * @param string $recipient
     * @return string GSM number like +1XXXXXXXXXX
     * @throws SMSNotificationException
     */
    protected function _prepareRecipientNumber($recipient)
    {
        $gsm_number = $this->_prefix;
        if(!empty($recipient)){
            $gsm_number .= preg_replace('/[^0-9]/', '', $recipient);
        }else{
            throw new SMSNotificationException('Unknown recipient phone number.');
        }
        return $gsm_number;
    }

    /**
     * Use magic PHP function __call to route function calls to the Twilio class.
     * Look into the Twilio class for possible functions.
     *
     * @param string $methodName Method name from Twilio class
     * @param array $methodParams Parameters pass to method
     * @return mixed
     */
    public function __call($methodName, $methodParams)
    {
        if (method_exists($this->client, $methodName)) {
            return call_user_func_array(array($this->client, $methodName), $methodParams);
        }
        return parent::__call($methodName, $methodParams);
    }
}