<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use nenad\passwordStrength\StrengthValidator;
use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\queries\UserQuery;
use common\models\queries\UserCredentialQuery;
use common\models\Article;
use common\models\Service;
use common\models\ChangeHistory;
use common\models\UserCredential;
use common\models\UserAvatar;
use common\models\UserRating;
use common\models\UserService;
use common\models\UserOrder;
use common\rbac\models\Role;

/**
 * This is the user model class extending UserIdentity.
 * Here you can implement your custom user solutions.
 *
 * @property Article $articles
 * @property User $suspendedBy
 * @property User $terminatedBy
 * @property User $createdBy
 * @property User $updatedBy
 * @property UserRating $userRating
 * @property UserAvatar $avatar
 * @property UserAvatar[] $photos
 * @property UserService $userService
 * @property Service $service
 * @property Patient[] $customerPatients
 * @property UserCredential[] $userCredentials
 * @property UserCredential[] $userPendingCredentials
 * @property UserCredential[] $userExpiredCredentials
 * @property UserCredential[] $userCredentialsWithActiveCredentialType
 * @property UserOrder[] $userOrders
 * @property Order[] $customerOrders
 * @property Order[] $providerOrders
 * @property ChangeHistory $changeHistory
 * @property Role $role
 */
class User extends UserIdentity
{
    //Physical Therapy
    public const USER_TITLE_RPT = 'RPT'; //Registered Physical Therapists
    public const USER_TITLE_DPT = 'DPT'; //Doctor of Physical Therapy
    public const USER_TITLE_MPT = 'MPT'; //Master of Physical Therapy
    public const USER_TITLE_PTA = 'PTA'; //Physical Therapy Assistant

    //Occupational Therapy
    public const USER_TITLE_OTR = 'OTR'; //Registered Occupational Therapist
    public const USER_TITLE_OTD = 'OTD'; //Doctor of Occupational Therapist
    public const USER_TITLE_COTA = 'COTA'; //Certified Occupational Therapy Assistant

    //Speech Therapy
    public const USER_TITLE_SLP = 'SLP'; //Speech-language pathologist

    public const USER_GENDER_NONE = 'N';
    public const USER_GENDER_MALE = 'M';
    public const USER_GENDER_FEMALE = 'F';

    public const USER_STATUS_NOT_ACTIVATED = 'N';
    public const USER_STATUS_ACTIVE = 'A';
    public const USER_STATUS_INACTIVE = 'I';
    public const USER_STATUS_SUSPENDED = 'S';
    public const USER_STATUS_TERMINATED = 'T';

    public const USER_PROVIDER = 'provider';
    public const USER_CUSTOMER = 'customer';
    public const USER_EDITOR = 'editor';
    public const USER_ADMIN = 'admin';
    public const USER_SUPER_ADMIN = 'theCreator';

    public const USER_SYSTEM_USER_ID = 0;
    public const USER_SYSTEM_ADMIN_ID = 1;

    public const USER_SUSPENSION_REASON_NOT_USING_ANY_MORE = 'suspend_not_using_any_more';
    public const USER_SUSPENSION_REASON_OTHER_REASONS = 'suspend_other_reasons';
    public const USER_SUSPENSION_REASON_SUSPENDED_BY_ADMIN = 'suspend_suspended_by_admin';

    public const USER_TERMINATION_REASON_NOT_USING_ANY_MORE = 'terminate_not_using_any_more';
    public const USER_TERMINATION_REASON_OTHER_REASONS = 'terminate_other_reasons';
    public const USER_TERMINATION_REASON_TERMINATED_BY_ADMIN = 'terminate_terminated_by_admin';

    /** USER SCENARIOS */
    public const SCENARIO_DEFAULT = 'default';
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';
    public const SCENARIO_REGISTRATION_NEEDS_ACTIVATION = 'rna';
    public const SCENARIO_LOGIN_WITH_EMAIL = 'lwe';
    public const SCENARIO_LOGIN_WITH_USERNAME = 'lwu';
    public const SCENARIO_ACTIVATE_ACCOUNT = 'activate';
    public const SCENARIO_SUSPEND_ACCOUNT = 'suspend';
    public const SCENARIO_TERMINATE_ACCOUNT = 'terminate';
    public const SCENARIO_UPDATE_NOTIFICATIONS = 'update_notifications';
    public const SCENARIO_REQUEST_PASSWORD_RESET = 'request_password_reset';

    public $service_id;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'email', 'title', 'agency_name', 'rep_position', 'first_name', 'last_name', 'address', 'state', 'city', 'country', 'phone1', 'phone2', 'note', 'ip_address'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
            [['username', 'email'], 'filter', 'filter' => 'strtolower'],

            ['username', 'unique', 'message' => Yii::t('app', 'The username {value} has already been taken.')],
            [['username'], 'match', 'pattern' => '/^[a-zA-Z0-9]*[a-zA-Z]+[a-zA-Z0-9]*$/',
                'message'=> Yii::t('app', 'The username should contain only alphanumeric or alphabetic characters.')
            ],

            ['email', 'unique', 'message' => Yii::t('app', 'The email address {value} has already been taken.')],
            ['email', 'email', 'message' => Yii::t('app', 'The email address {value} is incorrect.')],

            [['agency_name', 'password_reset_token', 'account_activation_token', 'phone_number_validation_code', 'access_token'], 'unique'],

            [['title', 'agency_name', 'rep_position', 'first_name', 'last_name', 'address', 'state', 'city', 'country', 'zip_code', 'phone1', 'phone2', 'note'], 'filter', 'filter' => function($value){
                return empty($value) ? null : filter_var($value, FILTER_SANITIZE_STRING);
            }, 'skipOnArray' => true],

            [['title', 'phone1', 'phone2', 'zip_code', 'emergency_contact_number'], 'string', 'max' => 15],
            [['phone1', 'phone2'], PhoneInputValidator::class, 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],

            ['status', 'in', 'range' => [self::USER_STATUS_NOT_ACTIVATED, self::USER_STATUS_ACTIVE, self::USER_STATUS_INACTIVE, self::USER_STATUS_SUSPENDED, self::USER_STATUS_TERMINATED]],
            [['first_name', 'last_name', 'username', 'email', 'status'], 'required', 'on' => self::SCENARIO_CREATE],
            [['username', 'email', 'first_name', 'last_name', 'address', 'phone1', 'status'], 'required', 'on' => self::SCENARIO_UPDATE],
            ['title', 'required', 'when' => function($model) {
                /** @var User $model */
                return (($model->scenario === self::SCENARIO_UPDATE) && ($model->role->item_name === self::USER_PROVIDER));
            }, 'whenClient' => "function (attribute, value) {
                return $('#role-item_name').val() === 'provider';
            }"],
            ['agency_name', 'required', 'when' => function($model) {
                /** @var User $model */
                return (($model->scenario === self::SCENARIO_UPDATE) && ($model->role->item_name === self::USER_CUSTOMER));
            }, 'whenClient' => "function (attribute, value) {
                return ($('#role-item_name').val() === 'customer');
            }"],
            self::passwordStrengthRule(),
            [['status'], 'required', 'on'=>[self::SCENARIO_ACTIVATE_ACCOUNT], 'message' => Yii::t('app', 'Status can not be blank')],
            [['email', 'password_reset_token'], 'required', 'on'=>[self::SCENARIO_REQUEST_PASSWORD_RESET], 'message' => Yii::t('app', 'Email Address can not be blank')],
            [['suspension_reason', 'status'], 'required', 'on'=>[self::SCENARIO_SUSPEND_ACCOUNT], 'message' => Yii::t('app','Suspension reason can not be blank')],
            [['termination_reason', 'status'], 'required', 'on'=>[self::SCENARIO_TERMINATE_ACCOUNT], 'message' => Yii::t('app', 'Termination reason can not be blank')],
            [['note_email_news_and_promotions', 'note_email_account_updated', 'note_email_order_submitted', 'note_email_order_accepted', 'note_email_order_rejected', 'note_email_order_canceled', 'note_email_rate_service', 'note_email_order_reminder', 'note_sms_news_and_promotions', 'note_sms_account_updated', 'note_sms_order_submitted', 'note_sms_order_accepted', 'note_sms_order_rejected', 'note_sms_order_canceled', 'note_sms_rate_service', 'note_sms_order_reminder'], 'required', 'on' => self::SCENARIO_UPDATE_NOTIFICATIONS],
            [['service_rate', 'created_by', 'updated_by', 'suspended_by', 'terminated_by', 'phone_number_validation_code'], 'integer'],
            [['lat', 'lng'], 'number'],
            [['gender', 'note_email_news_and_promotions', 'note_email_account_updated', 'note_email_order_submitted', 'note_email_order_accepted', 'note_email_order_rejected', 'note_email_order_canceled', 'note_email_rate_service', 'note_email_order_reminder', 'note_sms_news_and_promotions', 'note_sms_account_updated', 'note_sms_order_submitted', 'note_sms_order_accepted', 'note_sms_order_rejected', 'note_sms_order_canceled', 'note_sms_rate_service', 'note_sms_order_reminder'], 'string', 'max' => 1],
            ['ip_address', 'ip'],
            [['username', 'agency_name', 'rep_position', 'first_name', 'last_name', 'address', 'city', 'country', 'timezone', 'note', 'state', 'suspension_reason', 'termination_reason', 'website_address', 'emergency_contact_name', 'emergency_contact_relationship', 'facebook_id', 'google_id'], 'string', 'min' => 2, 'max' => 255],
            [['language', 'covered_county', 'covered_city'], 'each', 'rule' => ['string', 'max' => 20]],
            [['created_at', 'updated_at', 'suspended_at', 'terminated_at'], 'date', 'format'=>'yyyy-MM-dd HH:mm:ssZ'],
        ];
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
                'skipUpdateOnClean' => false
            ],
            'integer' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
                'defaultValue' => self::USER_SYSTEM_ADMIN_ID,
                'skipUpdateOnClean' => false
            ],
            'emergency_contact_number' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'emergency_contact_number',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'emergency_contact_number',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'emergency_contact_number',
                ],
                'value' => function ($event) {
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        return $this->emergency_contact_number ? Yii::$app->formatter->asPhone($this->emergency_contact_number) : null;
                    }
                    return $this->emergency_contact_number ? '+1'.preg_replace('/\D/', '', $this->emergency_contact_number) : null;
                },
            ],
            /*'phone1' => [
                'class' => PhoneInputBehavior::class,
                'phoneAttribute' => 'phone1',
                //'displayFormat' => PhoneNumberFormat::NATIONAL
                //'countryCodeAttribute' => 'countryCode1',
            ],
            'phone2' => [
                'class' => PhoneInputBehavior::class,
                'phoneAttribute' => 'phone2',
                //'displayFormat' => PhoneNumberFormat::NATIONAL
                //'countryCodeAttribute' => 'countryCode2',
            ]*/
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
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'email' => Yii::t('app', 'Email Address'),
            'status' => Yii::t('app', 'Status'),
            'item_name' => Yii::t('app', 'Role'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'account_activation_token' => Yii::t('app', 'Account Activation Token'),
            'phone_number_validation_code' => Yii::t('app', 'Phone Number Validation Code'),
            'access_token' => Yii::t('app', 'Access Token'),
            'facebook_id' => Yii::t('app', 'Facebook'),
            'google_id' => Yii::t('app', 'Google'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),

            'title' => Yii::t('app', 'Title'),
            'agency_name' => Yii::t('app', 'Agency Name'),
            'rep_position' => Yii::t('app', 'Agent Position'),
            'service_id' => Yii::t('app', 'Therapist Service'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'gender' => Yii::t('app', 'Gender'),
            'phone1' => Yii::t('app', 'Primary Phone Number'),
            'phone2' => Yii::t('app', 'Secondary Phone Number'),
            'lat' => Yii::t('app', 'Latitude'),
            'lng' => Yii::t('app', 'Longitude'),
            'address' => Yii::t('app', 'Street Address'),
            'city' => Yii::t('app', 'City'),
            'state' => Yii::t('app', 'State'),
            'country' => Yii::t('app', 'Country'),
            'zip_code' => Yii::t('app', 'Zip Code'),
            'language' => Yii::t('app', 'Language'),
            'covered_county' => Yii::t('app', 'Covered County'),
            'covered_city' => Yii::t('app', 'Covered City'),
            'service_rate' => Yii::t('app', 'Service Rate Minimum'),
            'timezone' => Yii::t('app', 'Time Zone'),
            'note' => Yii::t('app', 'Note'),
            'website_address' => Yii::t('app', 'Web Site'),
            'emergency_contact_name' => Yii::t('app', 'Emergency Contact Name'),
            'emergency_contact_number' => Yii::t('app', 'Emergency Contact Number'),
            'emergency_contact_relationship' => Yii::t('app', 'Contact Relationship'),
            'ip_address' => Yii::t('app', 'Ip Address'),
            'suspended_at' => Yii::t('app', 'Suspended At'),
            'suspended_by' => Yii::t('app', 'Suspended By'),
            'suspension_reason' => Yii::t('app', 'Suspension Reason'),
            'terminated_at' => Yii::t('app', 'Terminated At'),
            'terminated_by' => Yii::t('app', 'Terminated By'),
            'termination_reason' => Yii::t('app', 'Termination Reason'),
            'note_email_news_and_promotions' => Yii::t('app','Email notifications about news and promotions'),
            'note_email_account_updated' => Yii::t('app', 'Email notifications about account updates'),
            'note_email_order_submitted' => Yii::t('app', 'Email notifications about submitted orders'),
            'note_email_order_accepted' => Yii::t('app', 'Email notifications about accepted orders'),
            'note_email_order_rejected' => Yii::t('app', 'Email notifications about rejected orders'),
            'note_email_order_canceled' => Yii::t('app', 'Email notifications about canceled orders'),
            'note_email_order_reminder' => Yii::t('app', 'Email notifications about order reminder'),
            'note_email_rate_service' => Yii::t('app', 'Email notifications about rating of services'),
            'note_sms_news_and_promotions' => Yii::t('app','SMS notifications about news and promotions'),
            'note_sms_account_updated' => Yii::t('app', 'SMS notifications about account updates'),
            'note_sms_order_submitted' => Yii::t('app', 'SMS notifications about submitted orders'),
            'note_sms_order_accepted' => Yii::t('app', 'SMS notifications about accepted orders'),
            'note_sms_order_rejected' => Yii::t('app', 'SMS notifications about rejected orders'),
            'note_sms_order_canceled' => Yii::t('app', 'SMS notifications about canceled orders'),
            'note_sms_order_reminder' => Yii::t('app', 'SMS notifications about order reminder'),
            'note_sms_rate_service' => Yii::t('app', 'SMS notifications about rating of services'),
        ];
    }

    /**
     * Set password rule based on our setting value (Force Strong Password).
     *
     * @return array Password strength rule.
     */
    public static function passwordStrengthRule()
    {
        // get setting value for 'Force Strong Password'
        $fsp = Yii::$app->params['fsp'];

        // password strength rule is determined by StrengthValidator
        // presets are located in: vendor/nenad/yii2-password-strength/presets.php
        $strong = [['password'], StrengthValidator::class, 'preset'=>'normal'];

        // normal yii rule
        $normal = ['password', 'string', 'min' => 6];

        // if 'Force Strong Password' is set to 'true' use $strong rule, else use $normal rule
        return ($fsp) ? $strong : $normal;
    }

    /**
     * Relation with Role model.
     *
     * @return ActiveQuery|Role
     */
    public function getRole()
    {
        // User has_one Role via Role.user_id -> id
        return $this->hasOne(Role::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with Article model.
     *
     * @return ActiveQuery|Article
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with userPhoto model for user photos.
     *
     * @return ActiveQuery|UserAvatar
     */
    public function getPhotos()
    {
        return $this->hasMany(UserAvatar::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with userAvatar for user avatar
     *
     * @return ActiveQuery|UserAvatar
     */
    public function getAvatar(){
        return $this->hasOne(UserAvatar::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with order as user customer
     *
     * @return ActiveQuery|UserRating
     */
    public function getUserRating()
    {
        return $this->hasOne(UserRating::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with patient as user customer
     *
     * @return ActiveQuery|Patient[]
     */
    public function getCustomerPatients()
    {
        return $this->hasMany(Patient::class, ['customer_id' => 'id'])->inverseOf('customer');
    }

    /**
     * Relation with UserService model
     *
     * @return ActiveQuery|UserService
     */
    public function getUserService() {
        return $this->hasOne(UserService::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with Service model via junction relation userService
     *
     * @return ActiveQuery|Service
     */
    public function getService() {
        return $this->hasOne(Service::class, ['id' => 'service_id'])->via('userService')->inverseOf('users');
    }

    /**
     * Relation with UserCredential model
     *
     * @return ActiveQuery|UserCredential[]
     */
    public function getUserCredentials() {
        return $this->hasMany(UserCredential::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with UserCredential model with pending status
     *
     * @return ActiveQuery|UserCredential[]
     */
    public function getUserPendingCredentials() {
        return $this->hasMany(UserCredential::class, ['user_id' => 'id'])->andOnCondition(['user_credential.status' => UserCredential::STATUS_PENDING]);
    }

    /**
     * Relation with UserCredential model with expired status
     *
     * @return ActiveQuery|UserCredential[]
     */
    public function getUserExpiredCredentials() {
        return $this->hasMany(UserCredential::class, ['user_id' => 'id'])->andOnCondition(['user_credential.status' => UserCredential::STATUS_EXPIRED]);
    }

    /**
     * Relation with UserCredential model with active credential type
     *
     * @return ActiveQuery|UserCredential[]
     */
    public function getUserCredentialsWithActiveCredentialType() {
        return $this->hasMany(UserCredential::class, ['user_id' => 'id'])->joinWith(['credentialType' => function(UserCredentialQuery $q){
            $q->withActiveCredentialType();
        }]);
    }

    /**
     * Relation with order model
     *
     * @return ActiveQuery|Order[]
     */
    public function getCustomerOrders() {
        return $this->hasMany(Order::class, ['patient_id' => 'id'])->via('customerPatients')->inverseOf('customer');
    }

    /**
     * Relation with UserService model
     *
     * @return ActiveQuery|UserOrder[]
     */
    public function getUserOrders() {
        return $this->hasMany(UserOrder::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with order model
     *
     * @return ActiveQuery|Order[]
     */
    public function getProviderOrders() {
        return $this->hasMany(Order::class, ['id' => 'order_id'])->via('userOrders')->inverseOf('providers');
    }

    /**
     * Relation with ChangeHistory model.
     *
     * @return ActiveQuery|ChangeHistory[]
     */
    public function getChangeHistory()
    {
        return $this->hasMany(ChangeHistory::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Relation with user for suspended_by
     *
     * @return ActiveQuery|User
     */
    public function getSuspendedBy(){
        return $this->hasOne(User::class, ['suspended_by' => 'id']);
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getTerminatedBy(){
        return $this->hasOne(User::class, ['terminated_by' => 'id']);
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getCreatedBy(){
        return $this->hasOne(User::class, ['created_by' => 'id']);
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getUpdatedBy(){
        return $this->hasOne(User::class, ['updated_by' => 'id']);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

}
