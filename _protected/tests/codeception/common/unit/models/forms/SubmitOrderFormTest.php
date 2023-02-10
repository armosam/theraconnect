<?php

namespace tests\codeception\common\unit\models\forms;

use Yii;
use Codeception\Specify;
use yii\base\InvalidConfigException;
use tests\codeception\frontend\unit\TestCase;
use tests\codeception\common\fixtures\RoleFixture;
use tests\codeception\common\fixtures\UserServiceFixture;
use tests\codeception\common\fixtures\UserFixture;
use common\models\forms\SubmitOrderForm;
use common\models\User;

/**
 * Class SubmitOrderFormTest
 * @package tests\codeception\frontend\unit\models\forms
 * @group order
 * @group service_request_form
 */
class SubmitOrderFormTest extends TestCase
{
    use Specify;

    protected $model;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    protected function setUp(): void
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
            return 'testing_submit_request_message.eml';
        };

        $this->model = new SubmitOrderForm();
        $this->model->setScenario(SubmitOrderForm::SCENARIO_NECESSARY_DATA);
        $this->model->service_id = 1;
        $this->model->provider_id = 10;
        $this->model->customer_id = 3;
        $this->model->order_start = '2020-01-01 01:00:00';
        $this->model->order_end = '2020-01-01 02:00:00';
        $this->model->order_address = 'Test Address';
    }

    /**
     * Clean up the objects against which you tested.
     */
    protected function tearDown(): void
    {
        unset($this->model);
        if(is_file($this->_getMessageFile())){
            unlink($this->_getMessageFile());
        }
        parent::tearDown();
    }

    public function testValidateFormForGuest()
    {
        Yii::$app->user->logout();
        $this->model->customer_id = null;

        $this->specify('Validate form for guest users', function () {
            expect('form validation not passed', $this->model->validate())->false();
        });

        $this->specify('First Name is required', function () {
            expect('first_name validation should not pass', $this->model->validate(['first_name']))->false();
            $this->model->first_name = 'New';
            expect('first_name validation should pass', $this->model->validate(['first_name']))->true();
        });

        $this->specify('Last Name is required', function () {
            expect('last_name validation should not pass', $this->model->validate(['last_name']))->false();
            $this->model->last_name = 'Customer';
            expect('last_name validation should pass', $this->model->validate(['last_name']))->true();
        });

        $this->specify('Email is required and should be email address', function () {
            expect('email validation should not pass with empty', $this->model->validate(['email']))->false();
            $this->model->email = 'simpleString';
            expect('email validation should not pass with simple string', $this->model->validate(['email']))->false();
            $this->model->email = 'newcustomer*&^(*@user.com';
            expect('email validation should not pass with special chars', $this->model->validate(['email']))->false();
            $this->model->email = 'newcustomer@user';
            expect('email validation should not pass with non email address', $this->model->validate(['email']))->false();
            $this->model->email = 'newcustomer@user.com';
            expect('email validation should pass with correct email address', $this->model->validate(['email']))->true();
        });

        $this->specify('Primary Phone is required and should be phone number', function () {
            expect('phone1 validation should not pass', $this->model->validate(['phone1']))->false();
            $this->model->phone1 = '+18183331234';
            expect('phone1 validation should pass', $this->model->validate(['phone1']))->true();
        });

        $this->specify('Secondary Phone is not required and should be phone number', function () {
            expect('phone2 validation should pass', $this->model->validate(['phone2']))->true();
            $this->model->phone1 = '+18183334321';
            expect('phone2 validation should pass', $this->model->validate(['phone2']))->true();
        });

        $this->specify('Captcha is required', function () {
            expect('verifyCode validation should not pass', $this->model->validate(['verifyCode']))->false();
            $this->model->verifyCode = 'someCode';
            expect('verifyCode validation should not pass', $this->model->validate(['verifyCode']))->false();
            $this->model->verifyCode = 'testme';
            expect('verifyCode validation should pass', $this->model->validate(['verifyCode']))->true();
        });

        $this->specify('Validate form fields for guest users', function () {
            $this->model->customer_id = 3;
            expect('form validation passed', $this->model->validate())->true();
        });
    }

    public function testValidateFormForLoggedUser()
    {
        $customer = User::findOne([$this->model->customer_id]);
        Yii::$app->user->login($customer);

        if(empty($customer->first_name)){
            $this->specify('First Name is required when customer first_name is empty', function () {
                expect('first_name validation should not pass', $this->model->validate(['first_name']))->false();
                $this->model->first_name = 'New';
                expect('first_name validation should pass', $this->model->validate(['first_name']))->true();
            });
        }
        if(empty($customer->last_name)){
            $this->specify('Last Name is required when customer last_name is empty', function () {
                expect('last_name validation should not pass', $this->model->validate(['last_name']))->false();
                $this->model->last_name = 'Customer';
                expect('last_name validation should pass', $this->model->validate(['last_name']))->true();
            });
        }
        if(empty($customer->email)){
            $this->specify('Email is required when customer email is empty', function () {
                expect('email validation should not pass', $this->model->validate(['email']))->false();
                $this->model->email = 'simpleString';
                expect('email validation should not pass', $this->model->validate(['email']))->false();
                $this->model->email = 'newcustomer*&^(*@user.com';
                expect('email validation should not pass', $this->model->validate(['email']))->false();
                $this->model->email = 'newcustomer@user';
                expect('email validation should not pass', $this->model->validate(['email']))->false();
                $this->model->email = 'newcustomer@user.com';
                expect('email validation should pass', $this->model->validate(['email']))->true();
            });
        }
        if(empty($customer->phone1)){
            $this->specify('Primary Phone is required when customer phone1 is empty', function () {
                expect('phone1 validation should not pass', $this->model->validate(['phone1']))->false();
                $this->model->phone1 = '+18183331234';
                expect('phone1 validation should pass', $this->model->validate(['phone1']))->true();
            });
        }
        if(empty($customer->phone2)){
            $this->specify('Secondary Phone is not required when customer phone2 is empty', function () {
                expect('phone2 validation should pass', $this->model->validate(['phone2']))->true();
                $this->model->phone1 = '+18183334321';
                expect('phone2 validation should pass', $this->model->validate(['phone2']))->true();
            });
        }

        $this->specify('Captcha is required always', function () {
            expect('verifyCode validation should not pass', $this->model->validate(['verifyCode']))->false();
            $this->model->verifyCode = 'someCode';
            expect('verifyCode validation should not pass', $this->model->validate(['verifyCode']))->false();
            $this->model->verifyCode = 'testme';
            expect('verifyCode validation should pass', $this->model->validate(['verifyCode']))->true();
        });

        $this->specify('Validate form for correct data', function () {
            expect('form validation passed', $this->model->validate())->true();
        });
    }

    public function testCalculateNecessaryAttributesForGuest()
    {
        $this->specify('Method should return expected result for logged customer', function () {
            Yii::$app->user->logout();
            $this->model->customer_id = null;
            $result = $this->model->calculateNecessaryAttributes();
            expect('returned array contains provider_id', $result)->contains('provider_id');
            expect('returned array contains service_id', $result)->contains('service_id');
            expect('returned array contains first_name', $result)->contains('first_name');
            expect('returned array contains last_name', $result)->contains('last_name');
            expect('returned array contains order_start', $result)->contains('order_start');
            expect('returned array contains order_end', $result)->contains('order_end');
            expect('returned array contains order_address', $result)->contains('order_address');
            expect('returned array contains email', $result)->contains('email');
            expect('returned array contains phone1', $result)->contains('phone1');
            expect('returned array contains phone2', $result)->contains('phone2');
            expect('returned array contains password', $result)->contains('password');
            expect('returned array contains verifyCode', $result)->contains('verifyCode');
        });
    }

    public function testCalculateNecessaryAttributesForLoggedCustomer()
    {
        $this->specify('Method should return expected result for guest customer', function () {
            $customer = User::findOne([$this->model->customer_id]);
            Yii::$app->user->login($customer);
            $result = $this->model->calculateNecessaryAttributes();
            expect('returned array contains provider_id', $result)->contains('provider_id');
            expect('returned array contains service_id', $result)->contains('service_id');
            expect('returned array contains customer_id', $result)->contains('customer_id');
            expect('returned array contains order_start', $result)->contains('order_start');
            expect('returned array contains order_end', $result)->contains('order_end');
            expect('returned array contains order_address', $result)->contains('order_address');
            expect('returned array contains phone2', $result)->contains('phone2');
            expect('returned array contains verifyCode', $result)->contains('verifyCode');
        });
    }

    public function testSubmitRequestAsLoggedCustomer()
    {
        // Login as a customer
        $customer = User::findOne([$this->model->customer_id]);
        Yii::$app->user->login($customer);

        $this->specify('Submit Service Request as a logged in customer', function () use ($customer) {
            if(empty($customer->first_name)){
                $this->model->first_name = 'New';
            }
            if(empty($customer->last_name)){
                $this->model->last_name = 'Customer';
            }
            if(empty($customer->email)){
                $this->model->email = 'newcustomer@user.com';
            }
            if(empty($customer->phone1)){
                $this->model->phone1 = '+18183331234';
            }
            if(empty($customer->phone2)){
                $this->model->phone1 = '+18183334321';
            }
            $this->model->verifyCode = 'testme';

            expect('form validation passed', $this->model->validate())->true();
            /** @TODO Check if order created,  email sent */
        });
    }

    public function testSubmitRequestAsGuest()
    {
        Yii::$app->user->logout();
        $this->model->customer_id = null;
        $this->specify('Submit Service Request as a guest', function () {
            $this->model->first_name = 'New';
            $this->model->last_name = 'Customer';
            $this->model->email = 'newcustomer@user.com';
            $this->model->phone1 = '+18183331234';
            $this->model->phone2 = '+18183334321';
            $this->model->password = 'user123';
            $this->model->verifyCode = 'testme';
            expect('form validation passed', $this->model->validate())->true();
            expect('sign up new customer', $this->model->signUpNewCustomer())->true();
        });

        $this->specify('email should be send about new customer', function () {
            expect('email file should exist', file_exists($this->_getMessageFile()))->true();
        });

        $this->specify('email message should contain correct data', function () {
            $emailMessage = file_get_contents($this->_getMessageFile());
            expect('email should contain user first_name', $emailMessage)->stringContainsString($this->model->first_name);
            expect('email should contain user last_name', $emailMessage)->stringContainsString($this->model->last_name);
            expect('email should contain sender email', $emailMessage)->stringContainsString($this->model->email);
            expect('email should contain sender To', $emailMessage)->stringContainsString('From: THERA Connect Support <'.Yii::$app->params['fromEmailAddress'].'>');
            expect('email should contain sender To', $emailMessage)->stringContainsString('To: '.$this->model->first_name .' '. $this->model->last_name .' <'.$this->model->email.'>');
            expect('email should contain subject', $emailMessage)->stringContainsString('Account activation for THERA Connect');
            expect('email should contain body', $emailMessage)->stringContainsString('Follow this link to activate your account');
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
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_submit_request_message.eml';
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
            'userService' => [
                'class' => UserServiceFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_service.php'
            ]
        ];
    }

}