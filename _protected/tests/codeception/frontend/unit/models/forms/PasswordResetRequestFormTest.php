<?php

namespace tests\codeception\frontend\unit\models\forms;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use common\models\User;
use frontend\models\forms\PasswordResetRequestForm;
use tests\codeception\frontend\unit\DbTestCase;
use tests\codeception\common\fixtures\UserFixture;

/**
 * Class PasswordResetRequestFormTest
 * @package tests\codeception\frontend\unit\models\forms
 * @group password_reset_request_form
 */
class PasswordResetRequestFormTest extends DbTestCase
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
            return 'testing_password_reset_message.eml';
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
     * Make sure that sending email to wrong user will fail.
     *
     */
    public function testSendEmailWrongUser()
    {
        $this->specify('no user with such email, message should not be sent', function () {

            $model = new PasswordResetRequestForm();
            $model->email = 'not-existing-email@example.com';

            expect('email not send', $model->sendPasswordResetRequestEmail())->false();

        });

        $this->specify('user is not active, message should not be sent', function () {

            $model = new PasswordResetRequestForm();
            $model->email = $this->user[1]['email'];

            expect('email not send', $model->sendPasswordResetRequestEmail())->false();

        });
    }

    /**
     * Make sure that sending email to correct user is working.
     *
     * @throws Exception
     */
    public function testSendEmailCorrectUser()
    {
        $model = new PasswordResetRequestForm();
        $model->email = $this->user[0]['email'];
        $user = User::findOne(['password_reset_token' => $this->user[0]['password_reset_token']]);

        expect('email sent', $model->sendPasswordResetRequestEmail())->true();
        expect('user has valid token', $user->password_reset_token)->notNull();

        $this->specify('message has correct format', function () use ($model) {

            expect('message file exists', file_exists($this->_getMessageFile()))->true();

            $message = file_get_contents($this->_getMessageFile());
            expect('message "from" is correct', $message)->stringContainsStringIgnoringCase(Yii::$app->params['fromEmailAddress']);
            expect('message replyTo is correct', $message)->stringContainsStringIgnoringCase(Yii::$app->params['supportEmail']);
            expect('message "to" is correct', $message)->stringContainsStringIgnoringCase($model->email);

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
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_password_reset_message.eml';
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
