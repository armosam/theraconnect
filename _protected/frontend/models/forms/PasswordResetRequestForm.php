<?php
namespace frontend\models\forms;

use Yii;
use Exception;
use yii\base\Model;
use common\models\User;
use common\models\notification\EmailNotification;
use common\exceptions\EmailNotificationException;

/**
 * Password reset request form.
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'filter', 'filter' => 'strtolower'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                'filter' => ['status' => User::USER_STATUS_ACTIVE],
                'message' => Yii::t('app', 'There is no account with this email address.')
            ],
        ];
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => Yii::t('app', 'Email Address'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool Whether the email was send.
     * @throws Exception
     */
    public function sendPasswordResetRequestEmail()
    {
        try {
            /* @var $user User */
            $user = User::findOne([
                'status' => User::USER_STATUS_ACTIVE,
                'email' => $this->email,
            ]);

            if (!$user){
                throw new EmailNotificationException('User not found for password reset');
            }

            $user->setScenario(User::SCENARIO_REQUEST_PASSWORD_RESET);
            $user->generatePasswordResetToken();

            if (!$user->save()) {
                throw new EmailNotificationException('User data not saved for password reset.');
            }

            $emailNotification = new EmailNotification(
                $user,
                EmailNotification::NOTIFICATION_ACCOUNT_PASSWORD_RESET,
                Yii::t('app', 'Password reset for {user}', ['user' => Yii::$app->name]),
                ['user' => $user],
                $this->email
            );

            return $emailNotification->send();

        }catch(Exception $e){
            Yii::error('Password reset email failed to send. '.$e->getMessage());
        }

        return false;
    }
}
