<?php

namespace frontend\models\forms;

use Yii;
use yii\base\Model;
use common\models\User;
use common\rbac\helpers\RbacHelper;
use common\exceptions\UserSignUpException;
use common\models\notification\EmailNotification;
use borales\extensions\phoneInput\PhoneInputValidator;

/**
 * Model representing  SignUp Form for Customer.
 */
class SignUpCustomerForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $first_name;
    public $last_name;
    public $agency_name;
    public $phone1;
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
            [['username', 'email', 'first_name', 'last_name', 'agency_name', 'phone1'], 'filter', 'filter' => 'trim'],
            [['username', 'email', 'first_name', 'last_name', 'agency_name', 'phone1'], 'required'],

            [['username', ], 'string', 'min' => 2, 'max' => 255],
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

            [['first_name', 'last_name', 'agency_name'], 'string', 'max' => 255],
            ['agency_name', 'unique', 'targetClass' => '\common\models\User',
                'message' => Yii::t('app', 'The agency {value} has already been registered.')],
            ['phone1', 'string', 'max' => 15],
            [['phone1'], PhoneInputValidator::class, 'on' => [self::SCENARIO_DEFAULT, User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION]],

            ['password', 'required'],
            // use passwordStrengthRule() method to determine password strength
            User::passwordStrengthRule(),

            // on default scenario, user status is set to active and for rna scenario it should be not activated
            ['status', 'default', 'value' => User::USER_STATUS_ACTIVE, 'on' => self::SCENARIO_DEFAULT],
            ['status', 'default', 'value' => User::USER_STATUS_NOT_ACTIVATED, 'on' => User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION],
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
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'agency_name' => Yii::t('app', 'Agency Name'),
            'phone1' => Yii::t('app', 'Phone Number'),
            'agreed' => Yii::t('app',  'I have read and agree with Terms of Service and Privacy Policy.'),
        ];
    }

    /**
     * Signs up the customer.
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
            $user->setAttributes([
                'username' => $this->username,
                'email' => $this->email,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'agency_name' => $this->agency_name,
                'phone1' => $this->phone1,
                'status' => $this->status
            ]);
            $user->setPassword($this->password);
            $user->generateAuthKey();

            // if scenario is "rna" we will generate account activation token before saving the model
            if ($this->scenario === User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION) {
                $user->generateAccountActivationToken();
            }

            if (!($user->save() && RbacHelper::assignRole($user->getId()))) {
                throw new UserSignUpException(Yii::t('app', 'User account not created or Role is not assigned correctly.'));
            }

            $transaction->commit();
            return $user;

        }catch(\Exception $e){
            $transaction->rollBack();
            Yii::error(sprintf('Agency SignUp Failed: Username: %s | Email: %s | Phone: %s | Password: %s | Agency: %s | ErrorMsg: %s', $user->username, $user->email, $this->phone1, $this->password, $this->agency_name, $e->getMessage()), 'Agency_SignUp');
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
