<?php

namespace tests\codeception\common\unit\models\forms;

use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use tests\codeception\common\unit\DbTestCase;
use tests\codeception\common\fixtures\PatientFixture;
use tests\codeception\common\fixtures\RoleFixture;
use tests\codeception\common\fixtures\UserFixture;
use tests\codeception\common\fixtures\UserServiceFixture;
use common\models\User;
use common\models\Order;
use common\models\forms\CancelOrderForm;
use common\exceptions\OrderCancelException;

/**
 * Cancel Order form test
 * Class CancelOrderFormTest
 * @package tests\codeception\common\unit\models\forms
 * @group order
 * @group cancel_order_form
 */
class CancelOrderFormTest extends DbTestCase
{
    use Specify;

    protected $model;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    public function setUp(): void
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
            return 'testing_cancel_message.eml';
        };

        $this->model = new CancelOrderForm();
        $this->model->order_id = 1;
        $this->model->cancellation_reason = Order::ORDER_CANCELLATION_REASON_SERVICE_REMOVED;
    }

    /**
     * Clean up the objects against which you tested._getMessageFile
     */
    public function tearDown(): void
    {
        Yii::$app->user->logout();
        unset($this->model);
        if(is_file($this->_getMessageFile())){
            unlink($this->_getMessageFile());
        }
        parent::tearDown();
    }

    public function testValidateOrderId()
    {

        $this->specify('Order ID should not be string', function () {
            $this->model->order_id = 'string';
            expect('form validation not passed', $this->model->validate())->false();
        });

        $this->model->order_id = 2;
        $this->specify('Order ID should be integer', function () {
            expect('form validation passed', $this->model->validate())->true();
        });

        $this->model->cancellation_reason = null;
        $this->specify('Order cancellation reason cannot be empty', function () {
            expect('form validation passed', $this->model->validate())->false();
        });
    }

    public function testCancelAsOrderCustomer()
    {
        $this->specify('Ensure submitted order can be canceled by order customer', function () {
            // Login as customer of order
            Yii::$app->user->login($this->model->getOrder()->customer);

            expect('order status is submitted before cancellation (S)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_SUBMITTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->model->cancel();

            expect('order status is canceled after cancellation (C)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_CANCELED);
            expect('order canceled_at is not null after cancellation', $this->model->getOrder()->canceled_at)->notNull();
            expect('order canceled_by is equal to logged user id after cancellation', $this->model->getOrder()->canceled_by)->equals($this->model->getOrder()->customer->id);
            expect('order cancellation_reason is not null after cancellation', $this->model->getOrder()->cancellation_reason)->equals(Order::ORDER_CANCELLATION_REASON_SERVICE_REMOVED);
        });

        $this->specify('email about order cancel should be send', function () {
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('order cancel email message should contain correct data', function () {
            $emailMessage = str_replace(array("=\r\n", "\r\n"), '', file_get_contents($this->_getMessageFile()));

            expect('email should contain To: email', $emailMessage)->stringContainsString('To: ' . $this->model->getOrder()->customer->getUserFullName(). ' <'. $this->model->getOrder()->customer->email. '>');
            expect('email should contain From: email', $emailMessage)->stringContainsString('From: ' . 'THERA Connect Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain Subject:', $emailMessage)->stringContainsString('Subject: '. Yii::t('app', 'You have canceled your service request to specialist {provider}.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
            expect('email should contain Body', $emailMessage)->stringContainsString(Yii::t('app', 'You have canceled your service request to specialist {provider}.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
        });
    }

    public function testCancelAsAnyOtherCustomer()
    {
        $this->specify('Ensure submitted order cannot be canceled by any other customer', function () {
            // Login as any another customer
            Yii::$app->user->login(User::findOne([8]));

            expect('order status is submitted before cancellation (S)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_SUBMITTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->expectException(OrderCancelException::class);
            $this->expectExceptionMessage('You do not have access to cancel this order.');
            $this->model->cancel();

            expect('order status is submitted after cancellation (S)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_SUBMITTED);
            expect('order canceled_at is null after cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null after cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null after cancellation', $this->model->getOrder()->cancellation_reason)->null();
        });
    }

    public function testCancelAsAnyOtherProvider()
    {
        $this->specify('Ensure submitted order cannot be canceled by any other provider', function () {
            // Login as any another provider
            $logged_user = User::findOne([10]);
            Yii::$app->user->login($logged_user);

            expect('order status is submitted before cancellation (S)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_SUBMITTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->expectException(OrderCancelException::class);
            $this->expectExceptionMessage('You do not have access to cancel this order.');
            $this->model->cancel();

            expect('order status is submitted after cancellation (S)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_SUBMITTED);
            expect('order canceled_at is null after cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null after cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null after cancellation', $this->model->getOrder()->cancellation_reason)->null();
        });
    }

    public function testCancelAsOrderProvider()
    {
        $this->specify('Ensure submitted order can be canceled by order provider', function () {

            // Login as provider of order
            $logged_user = $this->model->getOrder()->provider;
            Yii::$app->user->login($logged_user);

            expect('order status is submitted before cancellation (S)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_SUBMITTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->model->cancel();

            expect('order status changed to canceled (C)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_CANCELED);
            expect('order canceled_at is not null after cancellation', $this->model->getOrder()->canceled_at)->notNull();
            expect('order canceled_by is equal to logged user id', $this->model->getOrder()->canceled_by)->equals($logged_user->id);
            expect('order cancellation_reason is not null after cancellation', $this->model->getOrder()->cancellation_reason)->equals(Order::ORDER_CANCELLATION_REASON_SERVICE_REMOVED);
        });

        $this->specify('email about order reject should be send', function () {
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('order reject email message should contain correct data', function () {
            $emailMessage = str_replace(array("=\r\n", "\r\n"), '', file_get_contents($this->_getMessageFile()));

            expect('email should contain To: email', $emailMessage)->stringContainsString('To: ' . $this->model->getOrder()->customer->getUserFullName(). ' <'. $this->model->getOrder()->customer->email. '>');
            expect('email should contain From: email', $emailMessage)->stringContainsString('From: ' . 'THERA Connect Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain Subject:', $emailMessage)->stringContainsString('Subject: '. Yii::t('app', 'Your service request to specialist {provider} has been canceled by specialist.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
            expect('email should contain Body', $emailMessage)->stringContainsString(Yii::t('app', 'Your service request to specialist {provider} has been canceled by specialist.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
        });
    }

    public function testCancelAcceptedOrderAsOrderCustomer()
    {
        // Used accepted already order
        $this->model->order_id = 3;
        $this->specify('Ensure accepted order cannot be canceled by order customer', function () {
            // Login as customer of order
            $logged_user = $this->model->getOrder()->customer;
            Yii::$app->user->login($logged_user);

            expect('order status is accepted before cancellation (A)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_ACCEPTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->model->cancel();

            expect('order status changed to canceled (C)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_CANCELED);
            expect('order canceled_at is not null after cancellation', $this->model->getOrder()->canceled_at)->notNull();
            expect('order canceled_by is equal to logged user id', $this->model->getOrder()->canceled_by)->equals($logged_user->id);
            expect('order cancellation_reason is not null after cancellation', $this->model->getOrder()->cancellation_reason)->equals(Order::ORDER_CANCELLATION_REASON_SERVICE_REMOVED);
        });

        $this->specify('email about order cancel should be send', function () {
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('order cancel email message should contain correct data', function () {
            $emailMessage = str_replace(array("=\r\n", "\r\n"), '', file_get_contents($this->_getMessageFile()));

            expect('email should contain To: email', $emailMessage)->stringContainsString('To: ' . $this->model->getOrder()->customer->getUserFullName(). ' <'. $this->model->getOrder()->customer->email. '>');
            expect('email should contain From: email', $emailMessage)->stringContainsString('From: ' . 'THERA Connect Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain Subject:', $emailMessage)->stringContainsString('Subject: '. Yii::t('app', 'You have canceled your service request to specialist {provider}.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
            expect('email should contain Body', $emailMessage)->stringContainsString(Yii::t('app', 'You have canceled your service request to specialist {provider}.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
        });
    }

    public function testCancelAcceptedOrderAsOrderProvider()
    {
        // Used accepted already order
        $this->model->order_id = 3;
        $this->specify('Ensure accepted order can be canceled by order provider', function () {

            // Login as provider of order
            $logged_user = $this->model->getOrder()->provider;
            Yii::$app->user->login($logged_user);

            expect('order status is accepted before cancellation (A)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_ACCEPTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->model->cancel();

            expect('order status changed to canceled (C)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_CANCELED);
            expect('order canceled_at is not null after cancellation', $this->model->getOrder()->canceled_at)->notNull();
            expect('order canceled_by is equal to logged user id', $this->model->getOrder()->canceled_by)->equals($logged_user->id);
            expect('order cancellation_reason is not null after cancellation', $this->model->getOrder()->cancellation_reason)->equals(Order::ORDER_CANCELLATION_REASON_SERVICE_REMOVED);
        });

        $this->specify('email about order reject should be send', function () {
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('order reject email message should contain correct data', function () {
            $emailMessage = str_replace(array("=\r\n", "\r\n"), '', file_get_contents($this->_getMessageFile()));

            expect('email should contain To: email', $emailMessage)->stringContainsString('To: ' . $this->model->getOrder()->customer->getUserFullName(). ' <'. $this->model->getOrder()->customer->email. '>');
            expect('email should contain From: email', $emailMessage)->stringContainsString('From: ' . 'THERA Connect Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain Subject:', $emailMessage)->stringContainsString('Subject: '. Yii::t('app', 'Your service request to specialist {provider} has been canceled by specialist.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
            expect('email should contain Body', $emailMessage)->stringContainsString(Yii::t('app', 'Your service request to specialist {provider} has been canceled by specialist.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
        });
    }

    public function testCancelAcceptedOrderAsSuperAdmin()
    {
        // Used accepted already order
        $this->model->order_id = 3;
        $this->specify('Ensure accepted order can be canceled by super admin', function () {
            // Login as super admin
            $logged_user = User::findOne([1]);
            Yii::$app->user->login($logged_user);

            expect('order status is accepted before cancellation (A)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_ACCEPTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->model->cancel();

            expect('order status changed to canceled (C)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_CANCELED);
            expect('order canceled_at is not null after cancellation', $this->model->getOrder()->canceled_at)->notNull();
            expect('order canceled_by is equal to logged user id', $this->model->getOrder()->canceled_by)->equals($logged_user->id);
            expect('order cancellation_reason is not null after cancellation', $this->model->getOrder()->cancellation_reason)->equals(Order::ORDER_CANCELLATION_REASON_SERVICE_REMOVED);
        });

        $this->specify('email about order cancel by administration should be send', function () {
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('order cancel email message should contain correct data', function () {
            $emailMessage = str_replace(array("=\r\n", "\r\n"), '', file_get_contents($this->_getMessageFile()));
            expect('email should contain To: email', $emailMessage)->stringContainsString('To: ' . $this->model->getOrder()->customer->getUserFullName(). ' <'. $this->model->getOrder()->customer->email. '>');
            expect('email should contain From: email', $emailMessage)->stringContainsString('From: ' . 'THERA Connect Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain Subject:', $emailMessage)->stringContainsString('Subject: '. Yii::t('app', 'Your service request to specialist {provider} has been canceled by administration.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
            expect('email should contain Body', $emailMessage)->stringContainsString(Yii::t('app', 'Your service request to specialist {provider} has been canceled by administration.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
        });
    }

    public function testCancelAsSuperAdmin()
    {
        $this->specify('Ensure submitted order can be canceled by super admin', function () {
            // Login as super admin
            $logged_user = User::findOne([1]);
            Yii::$app->user->login($logged_user);

            expect('order status is submitted before cancellation (S)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_SUBMITTED);
            expect('order canceled_at is null before cancellation', $this->model->getOrder()->canceled_at)->null();
            expect('order canceled_by is null before cancellation', $this->model->getOrder()->canceled_by)->null();
            expect('order cancellation_reason is null before cancellation', $this->model->getOrder()->cancellation_reason)->null();

            $this->model->cancel();

            expect('order status changed to canceled (C)', $this->model->getOrder()->status)->equals(Order::ORDER_STATUS_CANCELED);
            expect('order canceled_at is not null after cancellation', $this->model->getOrder()->canceled_at)->notNull();
            expect('order canceled_by is equal to logged user id', $this->model->getOrder()->canceled_by)->equals($logged_user->id);
            expect('order cancellation_reason is not null after cancellation', $this->model->getOrder()->cancellation_reason)->equals(Order::ORDER_CANCELLATION_REASON_SERVICE_REMOVED);
        });

        $this->specify('email about order cancel by administration should be send', function () {
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('order cancel email message should contain correct data', function () {
            $emailMessage = str_replace(array("=\r\n", "\r\n"), '', file_get_contents($this->_getMessageFile()));
            expect('email should contain To: email', $emailMessage)->stringContainsString('To: ' . $this->model->getOrder()->customer->getUserFullName(). ' <'. $this->model->getOrder()->customer->email. '>');
            expect('email should contain From: email', $emailMessage)->stringContainsString('From: ' . 'THERA Connect Support <' . Yii::$app->params['fromEmailAddress'] . '>');
            expect('email should contain Subject:', $emailMessage)->stringContainsString('Subject: '. Yii::t('app', 'Your service request to specialist {provider} has been canceled by administration.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
            expect('email should contain Body', $emailMessage)->stringContainsString(Yii::t('app', 'Your service request to specialist {provider} has been canceled by administration.', ['provider' => $this->model->getOrder()->provider->getUserFullName()]));
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
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath . '/testing_cancel_message.eml');
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
            'user_service' => [
                'class' => UserServiceFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_service.php',
            ],
            'order' => [
                'class' => PatientFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/order.php'
            ],
        ];
    }

}