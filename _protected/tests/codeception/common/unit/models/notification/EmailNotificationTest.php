<?php

namespace tests\codeception\common\unit\models\notification;

use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use common\models\User;
use common\models\notification\EmailNotification;
use tests\codeception\common\unit\TestCase;
use tests\codeception\common\fixtures\UserFixture;

/**
 * Class EmailNotificationTest
 * @group EmailNotification
 */
class EmailNotificationTest extends TestCase
{
    use Specify;

    protected $data;
    protected $emailNotification;

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
            'view' => EmailNotification::NOTIFICATION_ACCOUNT_CREATED,
            'subject' => 'This is a test message',
            'data' => ['model' => $model],
            'recipient_email' => 'test@user.com'
        ];
        $this->emailNotification = new EmailNotification($this->data['user'], $this->data['view'], $this->data['subject'], $this->data['data']);

        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_activation_message_notification.eml';
        };
    }

    /**
     * Clean up the objects against which you tested._getMessageFile
     */
    public function tearDown() : void
    {
        if(is_file($this->_getMessageFile())){
            unlink($this->_getMessageFile());
        }
        unset($this->data);
        unset($this->emailNotification);
        parent::tearDown();
    }

    /**
     * Tests email notification sending process
     */
    public function testEmailNotificationClass()
    {
        $this->specify('Email Notification class components', function () {

            expect('model has property subject', $this->emailNotification)->hasAttribute('subject');
            expect('model has property subject initialized', $this->emailNotification->getSubject())->equals($this->data['subject']);
            expect('model has property view', $this->emailNotification)->hasAttribute('view');
            expect('model has property view initialized', $this->emailNotification->getView())->equals($this->data['view']);
            expect('model has property data', $this->emailNotification)->hasAttribute('data');
            expect('model has property data initialized', $this->emailNotification->getData())->equals($this->data['data']);
            expect('model has property user', $this->emailNotification)->hasAttribute('user');
            expect('model has property user initialized', $this->emailNotification->getUser())->equals($this->data['user']);

            expect('model has property recipient_email', $this->emailNotification)->hasAttribute('recipient_email');
            expect('model has no property recipient_email initialized', $this->emailNotification->getRecipientEmail())->isEmpty();
            $this->emailNotification->setRecipientEmail($this->data['recipient_email']);
            expect('model has property recipient_email initialized as specified email address', $this->emailNotification->getRecipientEmail())->equals($this->data['recipient_email']);
        });
    }

    /**
     * Testing email sending when direct recipient is not selected
     * It will take user's email address
     */
    public function testEmailNotificationSendingToUserEmail()
    {
        $this->specify('send email notification', function(){
            expect('email notification sent successfully', $this->emailNotification->send())->true();
        });

        $this->specify('email message should contain:', function(){
            $emailMessage = file_get_contents($this->_getMessageFile());

            expect('email should contain From element', $emailMessage)->stringContainsString('From: ' . Yii::$app->name . ' Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain ReplyTo element', $emailMessage)->stringContainsString('Reply-To: ' . Yii::$app->name . ' Support <' . Yii::$app->params['supportEmail'] . '>');
            expect('email should contain To element', $emailMessage)->stringContainsString('To: ' . $this->data['user']->getUserFullName() . ' <' . $this->data['user']->email . '>');
            expect('email should contain Subject', $emailMessage)->stringContainsString('Subject: ' . $this->data['subject']);
            expect('email should contain text in body', $emailMessage)->stringContainsString('Your account has been created');

            if(!empty(Yii::$app->params['systemNotificationEmailAddress'])){
                $systemEmails = explode(',', Yii::$app->params['systemNotificationEmailAddress']);
                if(!empty($systemEmails)){
                    foreach ($systemEmails as $systemEmail)
                        expect('email should contain Bcc element', $emailMessage)->stringContainsString('Bcc: ' . $systemEmail);
                }
            }
        });
    }

    public function testEmailNotificationSendingToSpecifiedAddress()
    {
        // We are specifying recipient address
        $this->emailNotification->setRecipientEmail($this->data['recipient_email']);

        $this->specify('send email notification', function(){
            expect('email notification sent successfully', $this->emailNotification->send())->true();
        });

        $this->specify('email message should contain:', function(){
            $emailMessage = file_get_contents($this->_getMessageFile());

            expect('email should contain From element', $emailMessage)->stringContainsString('From: ' . Yii::$app->name . ' Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain ReplyTo element', $emailMessage)->stringContainsString('Reply-To: ' . Yii::$app->name . ' Support <' . Yii::$app->params['supportEmail'] . '>');
            expect('email should contain To element', $emailMessage)->stringContainsString('To: ' . $this->data['user']->getUserFullName() . ' <' . $this->data['recipient_email'] . '>');
            expect('email should contain Subject', $emailMessage)->stringContainsString('Subject: ' . $this->data['subject']);
            expect('email should contain text in body', $emailMessage)->stringContainsString('Your account has been created');
        });
    }

    /**
     * Get message file that our test will create to put contact data in
     * (we are simulating email sending in our test by writing data to file).
     *
     * @return string
     */
    private function _getMessageFile()
    {
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_activation_message_notification.eml';
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
