<?php

namespace frontend\models\forms;

use Yii;
use yii\base\Model;
use common\models\User;
use common\rbac\helpers\RbacHelper;
use common\exceptions\UserSignUpException;
use common\models\notification\EmailNotification;

/**
 * Model representing  SignUp Form for Provider.
 */
class SignUpProviderForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $status;
    public $agreed;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'filter', 'filter' => 'trim'],
            [['username', 'email'], 'required'],

            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => '\common\models\User',
                'message' => Yii::t('app', 'The username {value} has already been taken.')
            ],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z0-9]*[a-zA-Z]+[a-zA-Z0-9]*$/',
                'message'=> Yii::t('app', 'The username should contain only alphanumeric or alphabetic characters.')
            ],

            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User',
                'message' => Yii::t('app', 'The email address {value} has already been taken.')],

            ['password', 'required'],
            // use passwordStrengthRule() method to determine password strength
            User::passwordStrengthRule(),

            // on default scenario, user status is set to active
            ['status', 'default', 'value' => User::USER_STATUS_ACTIVE, 'on' => User::SCENARIO_DEFAULT],
            // status is set to not active on rna (registration needs activation) scenario
            ['status', 'default', 'value' => User::USER_STATUS_NOT_ACTIVATED, 'on' => User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION],
            // status has to be integer value in the given range. Check User model.
            ['status', 'in', 'range' => [User::USER_STATUS_NOT_ACTIVATED, User::USER_STATUS_ACTIVE]],
            ['agreed', 'required', 'requiredValue' => 1, 'message' => Yii::t('app', 'You are requested to agree with Terms of Service and Privacy Policy')],
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
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email'),
            'agreed' => Yii::t('app',  'I have read and agree with Terms of Service and Privacy Policy.'),
        ];
    }

    /**
     * Signs up the user.
     * If scenario is set to "rna" (registration needs activation), this means
     * that user need to activate his account using email confirmation method.
     *
     * @return User|null The saved model or null if saving fails.
     */
    public function signUp()
    {
        $transaction = Yii::$app->db->beginTransaction();
        $user = new User();

        try {
            if (!isset($this->agreed) || empty($this->agreed)) {
                throw new UserSignUpException(Yii::t('app','You are requested to agree with Terms of Service and Privacy Policy'));
            }

            $user->setScenario(User::SCENARIO_CREATE);
            $user->setAttribute('username', $this->username);
            $user->setAttribute('email', $this->email);
            $user->setAttribute('status', $this->status);
            $user->setPassword($this->password);
            $user->generateAuthKey();

            // if scenario is "rna" we will generate account activation token before saving the model
            if ($this->scenario === User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION) {
                $user->generateAccountActivationToken();
            }

            if (!($user->save() && RbacHelper::assignRole($user->getId(), User::USER_PROVIDER))) {
                throw new UserSignUpException(Yii::t('app', 'Provider account not created or Role is not assigned correctly.'));
            }

            $transaction->commit();
            return $user;
        }catch(\Exception $e){
            $transaction->rollBack();
            Yii::error(sprintf('Provider SignUp Failed: Username: %s | Email: %s | Password: %s | ErrorMsg: %s', $user->username, $user->email, $this->password, $e->getMessage()));
        }
        return null;
    }

    /**
     * Sends email to registered user with account activation link.
     *
     * @param  object $user Registered user.
     * @return bool Whether the message has been sent successfully.
     */
    public function sendAccountActivationEmail($user)
    {
        $emailNotification = new EmailNotification(
            $user,
            EmailNotification::NOTIFICATION_ACCOUNT_ACTIVATION,
            Yii::t('app', 'Account activation for {user}', ['user' => Yii::$app->name]),
            ['user' => $user],
            $this->email
        );
        return $emailNotification->send();
    }
}
