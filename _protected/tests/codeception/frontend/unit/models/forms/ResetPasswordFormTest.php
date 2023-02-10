<?php

namespace tests\codeception\frontend\unit\models\forms;

use Yii;
use yii\base\Exception;
use frontend\models\forms\ResetPasswordForm;
use tests\codeception\frontend\unit\DbTestCase;
use tests\codeception\common\fixtures\UserFixture;
use yii\base\InvalidArgumentException;

/**
 * Class ResetPasswordFormTest
 * @package tests\codeception\frontend\unit\models\forms
 * @group reset_password_form
 */
class ResetPasswordFormTest extends DbTestCase
{
    /**
     * Resetting password if token is wrong should not be possible.
     */
    public function testResetWrongToken()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Yii::t('app', 'Wrong password reset token.'));
        new ResetPasswordForm('notexistingtoken_1391882543');
    }

    /**
     * Resetting password if token is empty should not be possible.
     */
    public function testResetEmptyToken()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(Yii::t('app', 'Password reset token cannot be blank.'));
        new ResetPasswordForm('');
    }

    /**
     * Make sure that we can reset password if token is correct.
     *
     * @throws Exception
     */
    public function testResetCorrectToken()
    {
        $form = new ResetPasswordForm($this->user[0]['password_reset_token']);

        expect('password should be reseted', $form->resetPassword())->true();
        expect('password reset token should be removed', $form->userModel()->password_reset_token)->null();
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
