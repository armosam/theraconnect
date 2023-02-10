<?php

namespace tests\codeception\frontend\unit\models\forms;

use Yii;
use Codeception\Specify;
use common\models\forms\LoginForm;
use tests\codeception\frontend\unit\DbTestCase;
use tests\codeception\common\fixtures\UserFixture;
use yii\base\InvalidConfigException;

/**
 * Class LoginFormTest
 * @package tests\codeception\frontend\unit\models\forms
 * @group login_form
 */
class LoginFormTest extends DbTestCase
{
    use Specify;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    protected function setUp() : void
    {
        parent::setUp();

        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_activation_message_login_form.eml';
        };
    }

    /**
     * Clean up the objects against which you tested.
     */
    protected function tearDown() : void
    {
        if(is_file($this->_getMessageFile())){
            unlink($this->_getMessageFile());
        }

        parent::tearDown();
    }

    /**
     * If user has not activated his account he should not be able to log in.
     * Also user gets activation email again
     */
    public function testLoginNotActivatedUser()
    {
        $model = new LoginForm();
        $model->username = 'tester';
        $model->password = 'test123';

        $this->specify('not activated user should not be able to login', function () use ($model) {
            expect('model should not login user', $model->login(false))->false();
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
        });

        $this->specify('by login to not activated account activation email should send again', function () use ($model) {
            expect('model should not login user', $model->isNotActivated(true))->true();
            expect('activation email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('account activation email message should contain:', function () use ($model) {
            $emailMessage = file_get_contents($this->_getMessageFile());

            expect('email should contain from', $emailMessage)->stringContainsString('From: ' . Yii::$app->name . ' Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain replyTo', $emailMessage)->stringContainsString('Reply-To: ' . Yii::$app->name . ' Support <' . Yii::$app->params['supportEmail'] . '>');
            expect('email should contain to', $emailMessage)->stringContainsString('To: "' . $model->getUser()->getUserFullName() . '" <' . $model->getUser()->email . '>');
            expect('email should contain subject', $emailMessage)->stringContainsString('Subject: ' . Yii::t('app', 'Account activation for {user}', ['user' => Yii::$app->name]));
            expect('email should contain correct activation token', $emailMessage)->stringContainsString('token=' . $this->user[0]['account_activation_token']);

            if(!empty(Yii::$app->params['systemNotificationEmailAddress'])){
                $systemEmails = explode(',', Yii::$app->params['systemNotificationEmailAddress']);
                if(!empty($systemEmails)){
                    foreach ($systemEmails as $systemEmail)
                        expect('email should contain bcc', $emailMessage)->stringContainsString('Bcc: ' . $systemEmail);
                }
            }
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
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_activation_message_login_form.eml';
    }

    /**
     * Declares the fixtures that are needed by the current test case.
     *
     * @return array
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => '@tests/codeception/frontend/unit/fixtures/data/models/user.php'
            ],
        ];
    }
}
