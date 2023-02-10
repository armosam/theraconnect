<?php

namespace tests\codeception\frontend\unit\models;

use Yii;
use Codeception\Specify;
use yii\base\InvalidConfigException;
use yii\base\InvalidArgumentException;
use common\models\ChangeHistory;
use common\models\User;
use frontend\models\AccountActivation;
use tests\codeception\frontend\unit\DbTestCase;
use tests\codeception\common\fixtures\RoleFixture;
use tests\codeception\common\fixtures\UserFixture;

/**
 * Class AccountActivationTest
 * @package tests\codeception\frontend\unit\models
 * @group account_activation
 */
class AccountActivationTest extends DbTestCase
{
    use Specify;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    protected function setUp() : void
    {
        parent::setUp();
        ChangeHistory::deleteAll();

        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_activation_message_account_activation.eml';
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
     * If token is wrong account activation should not be possible.
     */
    public function testActivationWrong()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Yii::t('app','Wrong account activation token.'));
        new AccountActivation('notexistingtoken_1391882543');
    }

    /**
     * If token is empty account activation should not be possible.
     */
    public function testActivationEmpty()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Yii::t('app', 'Account activation token cannot be blank.'));
        new AccountActivation('');
    }

    /**
     * Make sure that user can activate his account if token is correct.
     */
    public function testActivationCorrect()
    {
        $model = new AccountActivation($this->user[1]['account_activation_token']);
        $record = $model->activateAccount();

        expect('account should be activated', $record->status)->equals(User::USER_STATUS_ACTIVE);
        expect('account activation token removed', $record->account_activation_token)->null();
    }

    /**
     * Make sure that user can activate his account if token is correct.
     */
    public function testProviderActivationEmail()
    {
        $model = new AccountActivation($this->user[1]['account_activation_token']);
        $record = $model->activateAccount();

        expect('account activation returned user model', $record)->isInstanceOf(User::class);
        expect('account should be activated', $record->status)->equals(User::USER_STATUS_ACTIVE);
        expect('account activation token removed', $record->account_activation_token)->null();


        $this->specify('message has correct format and data', function () use ($record) {

            expect('message file exists', file_exists($this->_getMessageFile()))->true();

            $message = file_get_contents($this->_getMessageFile());
            expect('message "from" is correct', $message)->stringContainsStringIgnoringCase(Yii::$app->params['fromEmailAddress']);
            expect('message replyTo is correct', $message)->stringContainsStringIgnoringCase(Yii::$app->params['supportEmail']);
            expect('message "to" is correct', $message)->stringContainsStringIgnoringCase($record->email);
            expect('message "body" contains specific data', $message)->stringContainsStringIgnoringCase('Your account has been activated successfully');
            expect('message "body" contains specific data', $message)->stringContainsStringIgnoringCase('Specialist');

        });
    }

    /**
     * Make sure that user can activate his account if token is correct.
     */
    public function testCustomerActivationEmail()
    {
        $model = new AccountActivation($this->user[2]['account_activation_token']);
        $record = $model->activateAccount();

        expect('account activation returned user model', $record)->isInstanceOf(User::class);
        expect('account should be activated', $record->status)->equals(User::USER_STATUS_ACTIVE);
        expect('account activation token removed', $record->account_activation_token)->null();


        $this->specify('message has correct format and data', function () use ($record) {

            expect('message file exists', file_exists($this->_getMessageFile()))->true();

            $message = file_get_contents($this->_getMessageFile());
            expect('message "from" is correct', $message)->stringContainsStringIgnoringCase(Yii::$app->params['fromEmailAddress']);
            expect('message replyTo is correct', $message)->stringContainsStringIgnoringCase(Yii::$app->params['supportEmail']);
            expect('message "to" is correct', $message)->stringContainsStringIgnoringCase($record->email);
            expect('message "body" contains specific data', $message)->stringContainsStringIgnoringCase('Your account has been activated successfully');
            expect('message "body" should not contain specific data', $message)->stringNotContainsStringIgnoringCase('Specialist');

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
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_activation_message_account_activation.eml';
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
            'role' => [
                'class' => RoleFixture::class,
                'dataFile' => '@tests/codeception/frontend/unit/fixtures/data/models/auth_assignment.php'
            ],
        ];
    }
}
