<?php

namespace tests\codeception\frontend\unit\models\forms;

use common\models\User;
use Codeception\Specify;
use frontend\models\forms\SignUpProviderForm;
use tests\codeception\frontend\unit\DbTestCase;
use tests\codeception\common\fixtures\UserFixture;

/**
 * Class SignUpProviderFormTest
 * @package tests\codeception\frontend\unit\models\forms
 * @group sign_up_provider_form
 */
class SignUpProviderFormTest extends DbTestCase
{
    use Specify;

    /**
     * Make sure that signup is working if registration with activation is
     * requested by administrator.
     *
     */
    public function testSignUpProviderWithActivation()
    {
        $model = new SignUpProviderForm([
            'username' => 'someusername',
            'email' => 'someemail@example.com',
            'password' => 'somepassword',
            'agreed' => 1,
            'status' => 'N'
        ]);
        $model->scenario = User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION;

        $user = $model->signUp();

        $this->assertInstanceOf('common\models\User', $user, 'user should be valid');

        expect('username should be correct', $user->username)->equals('someusername');
        expect('email should be correct', $user->email)->equals('someemail@example.com');
        expect('password should be correct', $user->validatePassword('somepassword'))->true();
        expect('User has provider role', $user->role->item_name)->equals(User::USER_PROVIDER);

        expect('user has valid account activation token', $user->account_activation_token)->notNull();

        expect('account activation email should be sent', $model->sendAccountActivationEmail($user))->true();
    }

    /**
     * Make sure that sign up without activation is working.
     *
     */
    public function testSignUpProviderWithoutActivation()
    {
        $model = new SignUpProviderForm([
            'username' => 'someusername',
            'email' => 'someemail@example.com',
            'password' => 'somepassword',
            'agreed' => 1,
            'status' => 'A'
        ]);

        $user = $model->signUp();

        $this->assertInstanceOf('common\models\User', $user, 'user should be valid');

        expect('username should be correct', $user->username)->equals('someusername');
        expect('email should be correct', $user->email)->equals('someemail@example.com');
        expect('password should be correct', $user->validatePassword('somepassword'))->true();
        expect('User has provider role', $user->role->item_name)->equals(User::USER_PROVIDER);

        expect('account activation token is not set', $user->account_activation_token)->null();
    }

    /**
     * Make sure that user can not take username|email that already exists.
     */
    public function testSignUpProviderWithExistingData()
    {
        $model = new SignUpProviderForm([
            'username' => 'member',
            'email' => 'member@example.com',
            'password' => 'member123',
            'agreed' => 1,
            'status' => 'N'
        ]);

        expect('username and email are in use, user should not be created', $model->signUp())->null();
    }

    /**
     * Make sure that user can not sign up without agreeing with terms of service and privacy policy by checking checkbox.
     */
    public function testSignUpProviderWithoutAgreeingWithPolicy()
    {
        $model = new SignUpProviderForm([
            'username' => 'someusername',
            'email' => 'someemail@example.com',
            'password' => 'somepassword',
            'agreed' => 0,
            'status' => 'A'
        ]);

        expect('agreeing with policy is required, without agreeing user should not be created', $model->signUp())->null();
    }

    /**
     * Make sure that user can not sign up with username containing restricted symbols
     */
    public function testSignUpProviderWithIncorrectUsername()
    {
        $model = new SignUpProviderForm([
            'username' => 'some_user name',
            'email' => 'someemail@example.com',
            'password' => 'somepassword',
            'agreed' => 1,
            'status' => 'A'
        ]);

        expect('username must contain alphanumeric or alphabetic symbols, user should not be created', $model->signUp())->null();
    }

    /**
     * Make sure that user can not sign up with only numeric username.
     */
    public function testSignUpProviderWithOnlyNumericUsername()
    {
        $model = new SignUpProviderForm([
            'username' => '123456789',
            'email' => 'someemail@example.com',
            'password' => 'somepassword',
            'agreed' => 1,
            'status' => 'A'
        ]);

        expect('username must contain alphanumeric or alphabetic symbols, user should not be created', $model->signUp())->null();
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
                'dataFile' => '@tests/codeception/frontend/unit/fixtures/data/models/user.php',
            ],
        ];
    }
}
