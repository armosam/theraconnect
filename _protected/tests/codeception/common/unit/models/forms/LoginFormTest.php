<?php

namespace tests\codeception\common\unit\models\forms;

use common\models\User;
use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use common\models\forms\LoginForm;
use tests\codeception\common\unit\DbTestCase;
use tests\codeception\common\fixtures\RoleFixture;
use tests\codeception\common\fixtures\UserFixture;

/**
 * Login form test
 */
class LoginFormTest extends DbTestCase
{
    use Specify;

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

        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_activation_message_login_form.eml';
        };
    }

    /**
     * Clean up the objects against which you tested._getMessageFile
     */
    public function tearDown() : void
    {
        Yii::$app->user->logout();
        if(is_file($this->_getMessageFile())){
            unlink($this->_getMessageFile());
        }
        parent::tearDown();
    }

    public function testValidateUsername()
    {
        $model = new LoginForm();

        $model->username = 'testing';
        $this->specify('Username could be alphanumeric and cannot contain only numbers', function () use ($model) {
            expect('model should be validated', $model->validate(['username']))->true();
        });

        $model->username = 'testing123';
        $this->specify('Username could be alphanumeric and cannot contain only numbers', function () use ($model) {
            expect('model should be validated', $model->validate(['username']))->true();
        });

        $model->username = 'testing@test.com';
        $this->specify('Username could be email address', function () use ($model) {
            expect('model should be validated', $model->validate(['username']))->true();
        });

        $model->username = '';
        $this->specify('Username is required', function () use ($model) {
            expect('model should not be validated', $model->validate(['username']))->false();
        });

        $model->username = '123456';
        $this->specify('Username cannot contain only numbers', function () use ($model) {
            expect('model should not be validated', $model->validate(['username']))->false();
        });

        $model->username = '!@#$%^%^sdf234';
        $this->specify('Username cannot contain special symbols', function () use ($model) {
            expect('model should not be validated', $model->validate(['username']))->false();
        });

        $model->username = 'tes_ting';
        $this->specify('Username cannot contain special symbols', function () use ($model) {
            expect('model should not be validated', $model->validate(['username']))->false();
        });
    }

    /**
     * If username is wrong user should not be able to log in.
     */
    public function testLoginWrongUsername()
    {
        $model = new LoginForm();
        $model->username = 'wrong';
        $model->password = 'member123';

        $this->specify('user should not be able to login, when username is wrong', function () use ($model) {
            expect('model should not login user', $model->login(false))->false();
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
        });
    }

    /**
     * If email is wrong user should not be able to log in.
     */
    public function testLoginWrongEmail()
    {
        $model = new LoginForm();
        $model->username = 'member@wrong.com';
        $model->password = 'member123';

        $this->specify('user should not be able to login, when email is wrong', function () use ($model) {
            expect('model should not login user', $model->login(false))->false();
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
        }); 
    }

    /**
     * If password is wrong user should not be able to log in.
     */
    public function testLoginWrongPassword()
    {
        $model = new LoginForm();
        $model->username = 'member';
        $model->password = 'test';

        $this->specify('user should not be able to login with wrong password', function () use ($model) {
            expect('model should not login user', $model->login(false))->false();
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
        });
    }

    /**
     * If user has not activated his account and try to login then he should not be able to log in
     * Also user will get activation email again.
     */
    public function testSendingActivationTokenAgainWhenLoginNotActivatedUser()
    {
        $model = new LoginForm();
        $model->username = 'tester';
        $model->password = 'test123';

        $this->specify('not activated user should not be able to login', function () use ($model) {
            expect('model should not login user', $model->login(false))->false();
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
        });

        $this->specify('account activation email should be send again if user not activated account', function () use($model) {
            expect('checks if user is not activated then going to get activation email',$model->isNotActivated(true))->true();
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('account activation email message should contain:', function () use ($model) {
            $emailMessage = file_get_contents($this->_getMessageFile());

            expect('email should contain from', $emailMessage)->stringContainsString('From: ' . Yii::$app->name . ' Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain replyTo', $emailMessage)->stringContainsString('Reply-To: ' . Yii::$app->name . ' Support <' . Yii::$app->params['supportEmail'] . '>');
            expect('email should contain to', $emailMessage)->stringContainsString('To: ' . $model->getUser()->getUserFullName() . ' <' . $model->getUser()->email . '>');
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
     * Active user should be able to log in if he enter correct credentials.
     */
    public function testLoginActiveUser()
    {
        $model = new LoginForm();
        $model->username = 'member';
        $model->password = 'member123';

        $this->specify('user should be able to login with correct credentials', function () use ($model) {
            expect('model should login user', $model->login(false))->true();
            expect('user should be logged in', Yii::$app->user->isGuest)->false();
        });
    }

    /**
     * If user has set inactive his account he should be able to log in.
     */
    public function testLoginInactiveUser()
    {
        $model = new LoginForm();
        $model->username = 'inactive';
        $model->password = 'inactive123';

        $this->specify('inactive user should be able to login', function () use ($model) {
            expect('model should login user', $model->login(false))->true();
            expect('user should be logged in', Yii::$app->user->isGuest)->false();
        });
    }

    /**
     * If user has suspended account he should not be able to log in.
     */
    public function testLoginSuspendedUser()
    {
        $model = new LoginForm();
        $model->username = 'suspended';
        $model->password = 'suspended123';

        $this->specify('suspended user should not be able to login', function () use ($model) {
            expect('model should login user', $model->login(false))->false();
            expect('user should be logged in', Yii::$app->user->isGuest)->true();
        });
    }

    /**
     * If user has terminated account he should not be able to log in.
     */
    public function testLoginTerminatedUser()
    {
        $model = new LoginForm();
        $model->username = 'terminated';
        $model->password = 'terminated123';

        $this->specify('terminated user should not be able to login', function () use ($model) {
            expect('model should not login user', $model->login(false))->false();
            expect('user should not be logged in', Yii::$app->user->isGuest)->true();
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
                'dataFile' => '@tests/codeception/common/fixtures/data/user.php'
            ],
            'role' => [
                'class' => RoleFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/auth_assignment.php'
            ],
        ];
    }
}
