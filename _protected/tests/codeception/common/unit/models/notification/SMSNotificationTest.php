<?php

namespace tests\codeception\common\unit\models\notification;

use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use common\models\User;
use common\models\notification\SMSNotification;
use tests\codeception\common\unit\TestCase;
use tests\codeception\common\fixtures\UserFixture;

/**
 * Class SMSNotificationTest
 * @group SMSNotification
 */
class SMSNotificationTest extends TestCase
{
    use Specify;

    protected $data;
    protected $smsNotification;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    public function setUp() : void
    {
        parent::setUp();

        Yii::configure(Yii::$app, [
            'components' => [
                'user' => [
                    'class' => 'yii\web\User',
                    'identityClass' => 'common\models\base\UserIdentity',
                ],
            ],
        ]);
        $model = User::findOne([3]);
        $this->data = [
            'user' => $model,
            'message' => 'This is a test SMS message',
            'data' => ['model' => $model],
        ];
        $this->smsNotification = new SMSNotification($this->data['user'], $this->data['message'], $this->data['data']);

    }

    /**
     * Clean up the objects against which you tested._getMessageFile
     */
    public function tearDown() : void
    {
        unset($this->data);
        unset($this->smsNotification);
        parent::tearDown();
    }

    /**
     * Tests SMS notification sending process
     */
    public function testSMSNotificationClass()
    {
        $this->specify('SMS Notification class components', function () {

            expect('model has property user', $this->smsNotification)->hasAttribute('user');
            expect('model has property user initialized', $this->smsNotification->getUser())->equals($this->data['user']);
            expect('model has property message', $this->smsNotification)->hasAttribute('message');
            expect('model has property message initialized', $this->smsNotification->getMessage())->equals($this->data['message']);
            expect('model has property data', $this->smsNotification)->hasAttribute('data');
            expect('model has property data initialized', $this->smsNotification->getData())->equals($this->data['data']);
        });
    }

    /**
     * Tests sending of SMS notifications with empty phone numbers
     */
    public function testSMSNotificationSendingWithEmptyPhone()
    {
        $this->data['user']->phone1 = null;
        $this->data['user']->phone2 = null;
        $this->smsNotification->setUser($this->data['user']);

        $this->specify('SMS Notification fails sending SMS message when phone numbers empty', function () {
            if(isset(Yii::$app->params['disableSMSNotifications']) && Yii::$app->params['disableSMSNotifications'] === true){
                expect('sms notification failed to sent', $this->smsNotification->send())->true();
            }else{
                expect('sms notification failed to sent', $this->smsNotification->send())->false();
            }
        });
    }

    /**
     * Declares the fixtures that are needed by the current test case.
     * @return array
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user.php'
            ],
        ];
    }
}
