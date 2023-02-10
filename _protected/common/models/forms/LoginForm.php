<?php

namespace common\models\forms;

use Yii;
use Throwable;
use yii\base\Model;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\validators\EmailValidator;
use common\models\User;
use common\models\LoginAttempt;
use common\models\notification\EmailNotification;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;
    public $verifyCode;

    /**
     * @var User
     */
    private $_user = null;

    /**
     * @var User
     */
    private $_not_activated_user = null;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'filter', 'filter' => 'strtolower'],
            [['username', 'password'], 'required'],

            ['username', 'match', 'pattern' => '/^[a-zA-Z0-9]*[a-zA-Z]+[a-zA-Z0-9]*$/',
                'message'=> Yii::t('app', 'Username could be email address or alphanumeric characters.'),
                'on' => User::SCENARIO_LOGIN_WITH_USERNAME
            ],
            ['username', 'email', 'on' => User::SCENARIO_LOGIN_WITH_EMAIL],

            ['password', 'validatePassword'],
            ['rememberMe', 'boolean'],
            ['verifyCode', 'captcha', 'when' => function($model){
                return LoginAttempt::doesExitAcceptableLimit();
            }]
        ];
    }

    /**
     * Runs before validation and sets necessary settings for validation
     * @return bool
     */
    public function beforeValidate()
    {
        $username = strtolower(trim($this->username));

        $validator = new EmailValidator();
        if ($validator->validate($username, $error)) {
            $this->setScenario(User::SCENARIO_LOGIN_WITH_EMAIL);
        }else{
            $this->setScenario(User::SCENARIO_LOGIN_WITH_USERNAME);
        }
        return parent::beforeValidate();
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute The attribute currently being validated.
     */
    public function validatePassword($attribute)
    {
        if (!$this->hasErrors()) 
        {
            $user = $this->getUser();

            if (empty($user) || !in_array($user->status, [User::USER_STATUS_INACTIVE, User::USER_STATUS_ACTIVE]) || !$user->validatePassword($this->password))
            {
                switch($this->getScenario()){
                    case User::SCENARIO_LOGIN_WITH_EMAIL:
                        $field = Yii::t('app', 'Email');
                        break;
                    case User::SCENARIO_LOGIN_WITH_USERNAME:
                        $field = Yii::t('app', 'Username');
                        break;
                    default:
                        $field = Yii::t('app', 'Username');
                }
                $this->addError($attribute, Yii::t('app', 'Incorrect {field} or Password.', ['field' => $field]));
            }
        }
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
            'rememberMe' => Yii::t('app', 'Remember me'),
            'verifyCode' => Yii::t('app', 'Verification Code'),
        ];
    }

    /**
     * Logs in a user using the provided username|email and password.
     *
     * @param bool $controlAttempts
     * @return bool Whether the user is logged in successfully.
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function login($controlAttempts = true)
    {
        if ($this->validate()) 
        {
            LoginAttempt::clearAttempts();
            return Yii::$app->getUser()->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            if ($controlAttempts){
                LoginAttempt::doAttemptAndCheckAgain();
            }
            Yii::error(sprintf('Wrong attempt to login: LoginName: %s, Password: %s', $this->username, $this->password));
            return false;
        }  
    }

    /**
     * Finds user by username or email based on login scenario
     *
     * @return User|null
     */
    public function getUser()
    {
        if (empty($this->_user))
        {
            switch ($this->getScenario()){
                case User::SCENARIO_LOGIN_WITH_USERNAME:
                    $this->_user = User::findByUsername($this->username);
                    break;
                case User::SCENARIO_LOGIN_WITH_EMAIL:
                    $this->_user = User::findByEmail($this->username);
                    break;
                default:
                    $this->_user = null;
            }
        }
        return $this->_user;
    }

    /**
     * Finds not activated user account by username or email based on login scenario
     *
     * @return User|null
     */
    public function getNotActivatedUser()
    {
        if (empty($this->_not_activated_user)) {
            $user = null;
            switch ($this->getScenario()){
                case User::SCENARIO_LOGIN_WITH_USERNAME:
                    $user = User::findOne(['username' => $this->username, 'status' => User::USER_STATUS_NOT_ACTIVATED]);
                    break;
                case User::SCENARIO_LOGIN_WITH_EMAIL:
                    $user = User::findOne(['email' => $this->username, 'status' => User::USER_STATUS_NOT_ACTIVATED]);
                    break;
            }
            $this->_not_activated_user = (!empty($user) && $user->validatePassword($this->password)) ? $user : null;
        }
        return $this->_not_activated_user;
    }

    /**
     * Checks to see if the given user has NOT activated his account yet.
     * We first check if user exists in our system,
     * and then did he activated his account.
     * If there is the user not activated we send activation link again.
     *
     * @param bool $sendActivationNotification
     * @return bool True if not activated.
     * @throws \Exception
     */
    public function isNotActivated($sendActivationNotification = false)
    {
        $notActivatedUser = $this->getNotActivatedUser();
        if (empty($notActivatedUser)) {
            return false;
        }

        if ($sendActivationNotification) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                // We need to send activation email again in case it was expired
                $notActivatedUser->generateAccountActivationToken();
                $notActivatedUser->save(true, ['account_activation_token']);
                $emailNotification = new EmailNotification(
                    $notActivatedUser,
                    EmailNotification::NOTIFICATION_ACCOUNT_ACTIVATION,
                    Yii::t('app', 'Account activation for {user}', ['user' => Yii::$app->name]),
                    ['user' => $notActivatedUser],
                    $notActivatedUser->email
                );
                if (!$emailNotification->send()) {
                    throw new Exception(Yii::t('app', 'Account Activation link was not resent.'));
                }

                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollBack();
                return false;
            }
        }
        return true;
    }

    /**
     * Checks to see if the account of user has been set inactive.
     * We first check if user exists in our system,
     * and then did he set inactive his account.
     * @return bool True if inactive.
     */
    public function isInactive()
    {
        return (!empty($this->getUser()) && $this->getUser()->status === User::USER_STATUS_INACTIVE);
    }

    /**
     * Checks to see if the account of user has been set suspended.
     * We first check if user exists in our system,
     * and then if the status of account is suspend.
     * @return bool True if suspended.
     */
    public function isSuspended()
    {
        return (!empty($this->getUser()) && $this->getUser()->status === User::USER_STATUS_SUSPENDED);
    }
}
