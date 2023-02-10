<?php

namespace common\models;

use Yii;
use Exception;
use DateTimeZone;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\BaseInflector;
use common\rbac\models\Role;
use common\models\events\UserEvents;
use common\helpers\ArrayHelper;
use common\helpers\ConstHelper;
use common\helpers\GeoDataHelper;
use common\widgets\ISO639\Language;
use common\exceptions\ServiceException;
use common\exceptions\UserNotFoundException;
use common\exceptions\AccountActivationException;
use common\exceptions\AccountSuspensionException;
use common\exceptions\AccountTerminationException;

/**
 * Class User
 * @package common\models
 *
 * @property int $countOfReviews
 * @property string $userAddress
 */
class User extends base\User
{
    /**
     * Cached attributes that should be verified
     * @var array $_verification_needed_attributes
     */
    private $_verification_needed_attributes = [];

    /**
     * @var string $password
     */
    public $password;

    /**
     * @var Role $item_name
     */
    public $item_name;

    /**
     * Adding events
     */
    public function init()
    {
        $this->on(UserEvents::EVENT_ACCOUNT_CREATED, [UserEvents::class, 'accountCreatedEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_CREATED_BY_ADMIN, [UserEvents::class, 'accountCreatedByAdminEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_UPDATED, [UserEvents::class, 'accountUpdatedEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_UPDATED_BY_ADMIN, [UserEvents::class, 'accountUpdatedByAdminEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_ACTIVATION, [UserEvents::class, 'accountActivationEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_ACTIVATED, [UserEvents::class, 'accountActivatedEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_ACTIVATED_BY_ADMIN, [UserEvents::class, 'accountActivatedByAdminEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_SUSPENDED, [UserEvents::class, 'accountSuspendedEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_SUSPENDED_BY_ADMIN, [UserEvents::class, 'accountSuspendedByAdminEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_TERMINATED, [UserEvents::class, 'accountTerminatedEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_TERMINATED_BY_ADMIN, [UserEvents::class, 'accountTerminatedByAdminEventHandler'], ['user' => $this]);
        //$this->on(UserEvents::EVENT_ACCOUNT_EMAIL_CHANGED, [UserEvents::class, 'accountEmailChangedEventHandler'], ['user' => $this]);
        //$this->on(UserEvents::EVENT_ACCOUNT_PHONE_CHANGED, [UserEvents::class, 'accountPhoneChangedEventHandler'], ['user' => $this]);
        $this->on(UserEvents::EVENT_ACCOUNT_PASSWORD_RESET, [UserEvents::class, 'accountPasswordResetEventHandler'], ['user' => $this]);

        parent::init();
    }

    /**
     * Setting some attributes by default before insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            // Collects verification needed attributes and values. If attribute is verifiable, not required and empty then it will not be taken (phone2)
            if(in_array($this->getScenario(), [self::SCENARIO_CREATE, self::SCENARIO_UPDATE], true)) {
                $this->_verification_needed_attributes = array_filter( $this->getDirtyAttributes(), function($value, $attribute){
                    return ( in_array($attribute, ChangeHistory::verificationNeededAttributes(), true) && ($this->isAttributeRequired($attribute) || !empty($value)) );
                }, ARRAY_FILTER_USE_BOTH);
            }

            if(empty($this->ip_address)) {
                if (!empty(Yii::$app->request->userIP)) {
                    $this->setAttribute('ip_address', Yii::$app->request->userIP);
                } else {
                    $this->setAttribute('ip_address', filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP));
                }
            }

            if ($insert) {

                $this->setAttributes([
                    'note_sms_news_and_promotions' => $this->note_sms_news_and_promotions ?? ConstHelper::FLAG_NO,
                    'note_sms_account_updated' => $this->note_sms_account_updated ?? ConstHelper::FLAG_NO,
                    'note_sms_order_submitted' => $this->note_sms_order_submitted ?? ConstHelper::FLAG_NO,
                    'note_sms_order_accepted' => $this->note_sms_order_accepted ?? ConstHelper::FLAG_NO,
                    'note_sms_order_rejected' => $this->note_sms_order_rejected ?? ConstHelper::FLAG_NO,
                    'note_sms_order_canceled' => $this->note_email_order_canceled ?? ConstHelper::FLAG_NO,
                    'note_sms_order_reminder' => $this->note_sms_order_reminder ?? ConstHelper::FLAG_NO,
                    'note_sms_rate_service' => $this->note_sms_rate_service ?? ConstHelper::FLAG_NO,
                    'note_email_news_and_promotions' => $this->note_email_news_and_promotions ?? ConstHelper::FLAG_YES,
                    'note_email_account_updated' => $this->note_email_account_updated ?? ConstHelper::FLAG_YES,
                    'note_email_order_submitted' => $this->note_email_order_submitted ?? ConstHelper::FLAG_YES,
                    'note_email_order_accepted' => $this->note_email_order_accepted ?? ConstHelper::FLAG_YES,
                    'note_email_order_rejected' => $this->note_email_order_rejected ?? ConstHelper::FLAG_YES,
                    'note_email_order_canceled' => $this->note_email_order_canceled ?? ConstHelper::FLAG_YES,
                    'note_email_order_reminder' => $this->note_email_order_reminder ?? ConstHelper::FLAG_YES,
                    'note_email_rate_service' => $this->note_email_rate_service ?? ConstHelper::FLAG_YES,
                ]);

                /**
                 * This code is temporary and should be removed later.
                 * Custom solution for users signed up from Armenia.
                 * There is a problem to get correct location by IP that's why we manually set default location
                 */
                $locationData = GeoDataHelper::getLocationFromIPAddress($this->getAttribute('ip_address'));
                $this->setAttributes([
                    'timezone' => $this->timezone ?? $locationData->timeZone,
                    'city' => $this->city ?? $locationData->cityName,
                    'state' => $this->state ?? $locationData->stateCode,
                    'country' => $this->country ?? $locationData->countryName,
                    'zip_code' => $this->zip_code ?? $locationData->postalCode
                ]);

                $locationData = GeoDataHelper::getLocationFromAddress($this->address.' '.$this->city.', '.$this->state.' '.$this->zip_code);
                if(!empty($locationData->latitude)){
                    $this->setAttribute('lat', round($locationData->latitude, 14));
                }
                if(!empty($locationData->longitude)){
                    $this->setAttribute('lng', round($locationData->longitude, 14));
                }

                // Exclude email field from verification needed attributes. Email will be stored in User but not in History
                unset($this->_verification_needed_attributes['email']);
                // Other verification needed attributes will be saved in History but not in User
                foreach ($this->_verification_needed_attributes as $name => $value){
                    $this->setAttribute($name, null);
                }

            } else {

                // Verification needed attributes will not be saved in the User but will be in the History
                if(!empty($this->_verification_needed_attributes)) {
                    $fill_old_attributes = array_intersect_key($this->getOldAttributes(), $this->_verification_needed_attributes);
                    $this->setAttributes($fill_old_attributes);
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Setting some attributes automatically after insert or update of the table
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        try {
            if($insert) {
                if (empty($this->avatar)) {
                    // Creates empty user avatar record
                    $userAvatar = new UserAvatar(['scenario' => 'create']);
                    $userAvatar->setAttributes([
                        'user_id' => $this->getId(),
                        'file_name' => $this->getId().'-no-image.png',
                        'status' => ConstHelper::STATUS_ACTIVE
                    ]);
                    if (!$userAvatar->save()) {
                        throw new Exception(Yii::t('app', 'Unable to save User Avatar. Please try later'));
                    }
                }

                // Creates empty User Rating record
                $userRating = new UserRating(['scenario' => 'create']);
                $userRating->setAttributes([
                    'user_id' => $this->getId()
                ]);
                if (!$userRating->save()) {
                    throw new Exception(Yii::t('app', 'Unable to save User Rating. Please try later'));
                }

                if (Yii::$app->getUser()->getIsGuest()) {
                    $this->trigger(UserEvents::EVENT_ACCOUNT_CREATED);
                } else {
                    $this->trigger(UserEvents::EVENT_ACCOUNT_CREATED_BY_ADMIN);
                }

                if (!empty($this->_verification_needed_attributes)) {
                    foreach ($this->_verification_needed_attributes as $field_name => $new_value) {
                        if (!empty($new_value)) {
                            ChangeHistory::saveChanges($this->id, $field_name, $this->$field_name, $new_value);
                        }
                    }
                }

            } else {

                // Store verification needed attributes in the change history for Create and Update scenarios
                if (!empty($this->_verification_needed_attributes)) {
                    foreach ($this->_verification_needed_attributes as $field_name => $new_value) {
                        if (!empty($new_value)) {

                            $changeHistory = ChangeHistory::saveChanges($this->id, $field_name, $this->$field_name, $new_value);

                            if ($field_name === 'email') {
                                $this->on(UserEvents::EVENT_ACCOUNT_EMAIL_CHANGED, [UserEvents::class, 'accountEmailChangedEventHandler'], $changeHistory);
                                $this->trigger(UserEvents::EVENT_ACCOUNT_EMAIL_CHANGED);
                            }

                            if ($field_name === 'phone1' || $field_name === 'phone2') {
                                $this->on(UserEvents::EVENT_ACCOUNT_PHONE_CHANGED, [UserEvents::class, 'accountPhoneChangedEventHandler'], $changeHistory);
                                $this->trigger(UserEvents::EVENT_ACCOUNT_PHONE_CHANGED);
                            }
                        }
                    }
                }

                // Excludes Excepted and verification needed attributes
                $dirtyAttributes = array_diff_key($changedAttributes, array_flip(ChangeHistory::exceptedAttributes()), $this->_verification_needed_attributes);

                if(!empty($dirtyAttributes)) {

                    //Store normal attributes in the change history.
                    if ($this->getScenario() === self::SCENARIO_UPDATE) {
                        foreach ($dirtyAttributes as $field_name => $old_value) {
                            ChangeHistory::saveChanges($this->id, $field_name, $old_value, $this->$field_name);
                        }
                    }

                    if (isset($dirtyAttributes['password_hash']) && $dirtyAttributes['password_hash'] !== $this->password_hash) {
                        $this->trigger(UserEvents::EVENT_ACCOUNT_PASSWORD_RESET);
                        unset($dirtyAttributes['password_hash']);
                    }

                    if (isset($dirtyAttributes['status']) && $dirtyAttributes['status'] !== self::USER_STATUS_ACTIVE && $this->status === self::USER_STATUS_ACTIVE) {
                        if (Yii::$app->getUser()->getIsGuest()) {
                            $this->trigger(UserEvents::EVENT_ACCOUNT_ACTIVATED);
                        } elseif ($this->getId() !== Yii::$app->getUser()->getId()) {
                            $this->trigger(UserEvents::EVENT_ACCOUNT_ACTIVATED_BY_ADMIN);
                        }
                        unset($dirtyAttributes['status']);
                    }

                    if (isset($dirtyAttributes['status']) && $dirtyAttributes['status'] !== self::USER_STATUS_SUSPENDED && $this->status === self::USER_STATUS_SUSPENDED) {
                        if (!Yii::$app->getUser()->getIsGuest()) {
                            if ($this->getId() === Yii::$app->getUser()->getId()) {
                                $this->trigger(UserEvents::EVENT_ACCOUNT_SUSPENDED);
                            } elseif (Yii::$app->getUser()->can('admin')) {
                                $this->trigger(UserEvents::EVENT_ACCOUNT_SUSPENDED_BY_ADMIN);
                            }
                        }
                        unset($dirtyAttributes['status']);
                    }

                    if (isset($dirtyAttributes['status']) && $dirtyAttributes['status'] !== self::USER_STATUS_TERMINATED && $this->status === self::USER_STATUS_TERMINATED) {
                        if (!Yii::$app->getUser()->getIsGuest()) {
                            if ($this->getId() === Yii::$app->getUser()->getId()) {
                                $this->trigger(UserEvents::EVENT_ACCOUNT_TERMINATED);
                            } elseif (Yii::$app->getUser()->can('admin')) {
                                $this->trigger(UserEvents::EVENT_ACCOUNT_TERMINATED_BY_ADMIN);
                            }
                        }
                        unset($dirtyAttributes['status']);
                    }

                    if(!empty($dirtyAttributes) && $this->getScenario() !== self::SCENARIO_ACTIVATE_ACCOUNT) {
                        if ($this->getId() === Yii::$app->getUser()->getId()) {
                            $this->trigger(UserEvents::EVENT_ACCOUNT_UPDATED);
                        } elseif (Yii::$app->getUser()->can('admin')) {
                            $this->trigger(UserEvents::EVENT_ACCOUNT_UPDATED_BY_ADMIN);
                        }
                    }
                }
            }

        } catch (Exception $e) {
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'User-Model-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', Yii::t('app', 'Profile event did not triggered due to issue. '. $e->getMessage()));
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Finds user by username.
     *
     * @param  string $username
     * @return User|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by email.
     *
     * @param  string $email
     * @return User|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Finds user by password reset token.
     *
     * @param  string $token Password reset token.
     * @param  string $status Status of user.
     * @return null|static
     */
    public static function findByPasswordResetToken($token, $status = User::USER_STATUS_ACTIVE)
    {
        if (!static::isPasswordResetTokenValid($token))
        {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => $status,
        ]);
    }

    /**
     * Finds user by account activation token.
     *
     * @param  string $token Account activation token.
     * @param  string|array $status Status of user.
     * @return static|null
     */
    public static function findByAccountActivationToken($token, $status = User::USER_STATUS_NOT_ACTIVATED)
    {
        if (!static::isAccountActivationTokenValid($token))
        {
            return null;
        }

        return static::findOne([
            'account_activation_token' => $token,
            'status' => $status,
        ]);
    }

    /**
     * Returns the possible values of title field.
     *
     * @param null|string $selected
     * @return array|string Array of possible values of title.
     */
    public static function getTitleList($selected = null)
    {
        $data = [
            self::USER_TITLE_RPT => Yii::t('app', 'RPT'),
            self::USER_TITLE_PTA => Yii::t('app', 'PTA'),
            self::USER_TITLE_DPT => Yii::t('app', 'DPT'),
            self::USER_TITLE_MPT => Yii::t('app', 'MPT'),
            self::USER_TITLE_OTR => Yii::t('app', 'OTR'),
            self::USER_TITLE_OTD => Yii::t('app', 'OTD'),
            self::USER_TITLE_COTA => Yii::t('app', 'COTA'),
            self::USER_TITLE_SLP => Yii::t('app', 'SLP'),
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns user full name
     * @param bool $showRole
     * @param bool $showEmail
     * @param bool $showUsername
     * @param $user_id
     * @return string
     */
    public function getUserFullName($showRole = false, $showEmail = false, $showUsername = false, $user_id = false)
    {
        $fullName = '';

        if(is_null($user_id)){
            return null;
        }

        if($user_id === User::USER_SYSTEM_USER_ID){
            $fullName =  Yii::t('app', 'System Account');
        }

        if (!empty($user_id)) {
            /** @var User $user */
            $user = self::find()->joinWith(['role'])->where(['user.id'=>$user_id])->one();
            if($user !== null){

                $fullName = trim($user->first_name . ' ' .$user->last_name);

                if($showEmail){
                    $fullName .= ' (' . $user->email . ')';
                }
                if($showUsername){
                    $fullName = $user->username . ' ' . trim($fullName);
                }
                if ($showRole && isset($user->role)){
                    $fullName .= ' [ ' .self::getRoleList($user->role->item_name). ' ]';
                }
            }
        }

        if (!empty($this->id) && empty($user_id)){
            if ( !(empty($this->first_name) && empty($this->last_name)) ) {

                $fullName = trim($this->first_name . ' ' . $this->last_name);

                if ($showEmail) {
                    $fullName .= ' (' . $this->email . ')';
                }
                if ($showUsername) {
                    $fullName = $this->username . ' ' . trim($fullName);
                }

                if ($showRole && isset($this->role)) {
                    $fullName .= ' [ ' . self::getRoleList($this->role->item_name) . ' ]';
                }
            }
        }

        return trim($fullName);
    }

    /**
     * Returns User model of currently logged user
     * @return User|null
     */
    public static function currentLoggedUser()
    {
        if(Yii::$app->user->isGuest){
            return new self();
        }
        $id = Yii::$app->has('user') ? Yii::$app->user->id : null;
        $model = self::findOne([$id]);
        return $model ?? new self();
    }

    /**
     * Returns user city address
     * @return string
     */
    public function getUserAddress()
    {
        $result = !empty($this->address) ? $this->address.' ': '';
        $result .= !empty($this->city) ? $this->city.', ': '';
        $result .= !empty($this->state) ? $this->state: '';
        $result .= !empty($this->zip_code) ? (' '.$this->zip_code) : '';
        $result .= !empty($this->country) ? (', '.$this->country) : '';
        return trim($result, ', ');
    }

    /**
     * Returns defined name by given attribute and value
     * Before using this method make sure defined method definition for attribute values
     * Please check get{Attribute}List() methods.
     *
     * @param string $attribute Model's attribute
     * @param mixed $attribute_value Model's Attribute value
     * @return string Returns Correct defined name of value of attribute
     */
    public function getDefinedName($attribute, $attribute_value = null){
        $value = $attribute_value;
        $attr = explode('.', $attribute);
        if(!empty($attr[1])){
            if(is_null($attribute_value)){
                $value = $this->{$attr[0]}->{$attr[1]};
            }
            return ($this->{BaseInflector::variablize($attr[1]).'List'}[$value]) ?? $value;
        }

        if (is_null($attribute_value)) {
            $value = $this->{$attribute};
        } elseif (is_array($attribute_value)) {
            $value = $attribute_value[0];
        }

        return ($this->{BaseInflector::variablize($attribute) . 'List'}[$value]) ?? $value;
    }

    /**
     * Returns the user status name from status code.
     * We are setting role as provider to allow inactive and active status names to display
     *
     * @param  bool|integer $state Status integer value if sent to method.
     * @return string Nicely formatted status name.
     */
    public function getUserStatusName($state = false)
    {
        $status = ($state === false) ? $this->status : $state;
        return self::getUserStatusList($status, self::USER_SUPER_ADMIN);
    }

    /**
     * Returns the possible values of user status.
     *
     * @param bool|string $selected Selected Status
     * @param string $current_role Role to get statuses for
     * @return array|string Array of possible states of user status.
     */
    public static function getUserStatusList($selected = false, $current_role = null)
    {
        $user_statuses = [
            self::USER_SUPER_ADMIN => [
                self::USER_STATUS_ACTIVE => Yii::t('app', 'Active'),
                self::USER_STATUS_NOT_ACTIVATED => Yii::t('app', 'Not Activated'),
                self::USER_STATUS_INACTIVE => Yii::t('app', 'Inactive'),
                self::USER_STATUS_SUSPENDED => Yii::t('app', 'Suspended'),
                self::USER_STATUS_TERMINATED => Yii::t('app', 'Terminated')
            ],
            self::USER_ADMIN =>[
                self::USER_STATUS_ACTIVE => Yii::t('app', 'Active'),
                self::USER_STATUS_NOT_ACTIVATED => Yii::t('app', 'Not Activated'),
                self::USER_STATUS_INACTIVE => Yii::t('app', 'Inactive')
            ],
            self::USER_EDITOR => [
                self::USER_STATUS_ACTIVE => Yii::t('app', 'Active'),
                self::USER_STATUS_INACTIVE => Yii::t('app', 'Inactive')
            ],
            self::USER_PROVIDER => [
                self::USER_STATUS_ACTIVE => Yii::t('app', 'Active'),
                self::USER_STATUS_INACTIVE => Yii::t('app', 'Inactive')
            ],
            self::USER_CUSTOMER => [
                self::USER_STATUS_ACTIVE => Yii::t('app', 'Active')
            ]
        ];

        $current_role = $current_role ?: User::findOne(Yii::$app->user->getId())->role->item_name;

        if(empty($current_role)){
            $current_role = self::USER_CUSTOMER;
        }

        if($selected !== false){
            return $user_statuses[$current_role][$selected] ?? $selected;
        }

        return $user_statuses[$current_role];
    }

    /**
     * Returns the possible values of gender fields.
     *
     * @param bool|string $selected
     * @return array|string Array of possible states of gender.
     */
    public static function getGenderList($selected = false)
    {
        $data = [
            self::USER_GENDER_NONE => Yii::t('app', 'Not Disclosed'),
            self::USER_GENDER_MALE => Yii::t('app', 'Male'),
            self::USER_GENDER_FEMALE => Yii::t('app', 'Female'),
        ];
        if($selected !== false){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns array of possible rating values
     *
     * @param boolean $text Flag if output should be in text format (for drop down)
     * @param bool|int $selected
     * @return array|string|int Array of rating values or selected one
     */
    public static function getRatingList($text = false, $selected = false)
    {
        $data = array(0=>(($text===true) ? Yii::t('app', 'No Stars'): Html::tag('span', Yii::t('app', 'No Stars'), ['class' => 'label label-danger'])));
        for($i=0;$i<5;$i++){
            $stars = '';
            for($j=0;$j<5;$j++){
                $stars .= $text===true ? ($j<($i+1) ? '* ' : '') : ($j<($i+1) ? Html::tag('span', '', ['class' => 'glyphicon glyphicon-star']) : Html::tag('span', '', ['class' => 'glyphicon glyphicon-star-empty']));
            }
            $data[$i+1] = $text===true ? $stars : Html::tag('div', $stars, ['class' => 'rating-stars']) ;
        }
        if($selected !== false){
            return isset($data[$selected]) ?? $selected;
        }
        return $data;
    }

    /**
     * Returns role name by given user ID
     *
     * @param null|int $id User ID to get role name
     * @return string Role Name
     */
    public function getRoleNameById($id = null){
        if(!is_null($id)){
            $role = User::findOne($id)->role->item_name;
        }else{
            $role = $this->role->item_name;
        }
        return $this->getRoleName($role);
    }

    /**
     * Returns the role name ( item_name )
     * @param bool|string $role_name
     * @return string
     */
    public function getRoleName($role_name = false)
    {
        $role_name = ($role_name === false) ? $this->role->item_name : $role_name;
        return self::getRoleList($role_name);
    }

    /**
     * Returns the array of possible user roles.
     * NOTE: used in user/index view.
     *
     * @param bool|string $selected
     * @return array|string
     */
    public static function getRoleList($selected = false)
    {
        $data = [
            self::USER_SUPER_ADMIN=>Yii::t('app', 'Super Admin'),
            self::USER_ADMIN=>Yii::t('app', 'Administrator'),
            self::USER_EDITOR=>Yii::t('app', 'Site Editor'),
            self::USER_PROVIDER=>Yii::t('app', 'Therapist'),
            self::USER_CUSTOMER=>Yii::t('app', 'Agency'),
        ];
        if($selected !== false){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns the array of possible reasons of suspended account.
     * @param bool|string $selected
     * @return array|string
     */
    public static function getUserSuspensionReasonList($selected = false)
    {
        $data = [
            self::USER_SUSPENSION_REASON_NOT_USING_ANY_MORE => Yii::t('app', 'Not using any more'),
            self::USER_SUSPENSION_REASON_OTHER_REASONS => Yii::t('app', 'Other Reasons')
        ];
        if($selected !== false){
            return isset($data[$selected]) ? $data[$selected] : $selected;
        }
        return $data;
    }

    /**
     * Returns the array of possible reasons of terminated account.
     * @param bool|string $selected
     * @return array|string
     */
    public static function getUserTerminationReasonList($selected = false)
    {
        $data = [
            self::USER_TERMINATION_REASON_NOT_USING_ANY_MORE => Yii::t('app', 'Not using any more'),
            self::USER_TERMINATION_REASON_OTHER_REASONS => Yii::t('app', 'Other Reasons')
        ];
        if($selected !== false){
            return isset($data[$selected]) ? $data[$selected] : $selected;
        }
        return $data;
    }

    /**
     * Generates new password reset token.
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        try {
            $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Removes password reset token.
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Finds out if password reset token is valid.
     *
     * @param  string $token Password reset token.
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token))
        {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Generates new account activation token.
     * @throws Exception
     */
    public function generateAccountActivationToken()
    {
        try {
            $this->account_activation_token = Yii::$app->security->generateRandomString() . '_' . time();
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Removes account activation token.
     */
    public function removeAccountActivationToken()
    {
        $this->account_activation_token = null;
    }

    /**
     * Finds out if password reset token is valid.
     *
     * @param  string $token Password reset token.
     * @return bool
     */
    public static function isAccountActivationTokenValid($token)
    {
        if (empty($token))
        {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.accountActivationTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Returns the array of id and given property of given model and id for current logged user.
     * You can specify model as User some property as id and some properties separated by comma as value
     * It can be used in combo box as id value pairs;
     *
     * First argument is name of model
     * Second is property name as id
     * Third one is property name as name or value
     *
     * @param string $model_name kind of model
     * @param string $field_id field id
     * @param string $field_name field name
     * @param int|null $user_id user id
     * @return array Array of $field_names
     */
    public static function currentUserOrderDetailList($model_name, $field_id, $field_name, $user_id = null)
    {
        /** @var ActiveRecord $model_name */
        if(empty($user_id)){
            $field_ids = ArrayHelper::map(Order::find()->all(), 'id', $field_id);
        } else {
            if($field_id === 'customer_id'){
                $field_ids = ArrayHelper::map(Order::find()->where(['customer_id' => $user_id])->all(), 'id', $field_id);
            }else{
                $field_ids = ArrayHelper::map(Order::find()->where(['provider_id' => $user_id])->all(), 'id', $field_id);
            }
        }
        $result = ArrayHelper::map($model_name::find()->where(['id' => $field_ids])->all(), 'id', $field_name);
        return $result;
    }

    /**
     * Years provider is created in the system
     *
     * @return array list of years
     * @throws Exception
     */
    public function userActiveYears()
    {
        $data = array();
        $currentTimeZone =  new DateTimeZone(Yii::$app->timeZone);
        $now = date_create('now', $currentTimeZone);
        $createdDate = date_create($this->created_at, $currentTimeZone);

        $currentYear = $now->format('Y');
        $createdYear = $createdDate->format('Y');

        for ($i=$createdYear; $i<=$currentYear; $i++) {
            $data[$i] = $i;
        }
        return $data;
    }

    /**
     * Returns the array of service prices to filter on search view.
     *
     * @param string $currency
     * @return mixed
     */
    public static function getPriceFilterList($currency = 'AMD')
    {
        $config = Yii::$app->params['serviceFeeSliderConfig'];
        $step = $config[$currency] ?? $config['default'];
        return  ['min' => 500, 'max' => (int) UserService::find()->withActiveUser()->max('service_fee'), 'step' => $step];
    }

    /**
     * Activates user account
     * @return bool
     * @throws Exception
     */
    public function activateUserAccount()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setScenario(self::SCENARIO_ACTIVATE_ACCOUNT);
            $this->setAttribute('status', self::USER_STATUS_ACTIVE);
            //$this->setAttribute('terminated_by', null);
            //$this->setAttribute('terminated_at', null);
            //$this->setAttribute('termination_reason', null);

            if (!$this->save()) {
                throw new AccountActivationException(Yii::t('app', 'User model failed to save record.'));
            }

            $transaction->commit();
        }catch(Exception $e){
            $transaction->rollBack();
            Yii::error(sprintf('%s.%s: for user: %s - %s', __CLASS__, __METHOD__, $this->getUserFullName(), $e->getMessage() ));
            Yii::$app->session->addFlash('error', $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Activates user account
     * @return bool
     * @throws Exception
     */
    public function inactivateUserAccount()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setScenario(self::SCENARIO_ACTIVATE_ACCOUNT);
            $this->setAttribute('status', self::USER_STATUS_INACTIVE);
            $this->setAttribute('terminated_by', null);
            $this->setAttribute('terminated_at', null);
            $this->setAttribute('termination_reason', null);
            $this->setAttribute('suspended_by', null);
            $this->setAttribute('suspended_at', null);

            if (!$this->save()) {
                throw new AccountActivationException(Yii::t('app', 'User model failed to save record.'));
            }

            $transaction->commit();
        }catch(Exception $e){
            $transaction->rollBack();
            Yii::error(sprintf('%s.%s: for user: %s - %s', __CLASS__, __METHOD__, $this->getUserFullName(), $e->getMessage() ));
            Yii::$app->session->addFlash('error', $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Suspends user account
     * @return bool
     * @throws Exception
     */
    public function suspendUserAccount()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            $this->setScenario(User::SCENARIO_SUSPEND_ACCOUNT);
            $this->setAttribute('status', User::USER_STATUS_SUSPENDED);
            $this->setAttribute('suspended_by', Yii::$app->getUser()->getId());
            $this->setAttribute('suspended_at', new Expression('NOW()'));

            if (!$this->save()) {
                throw new AccountSuspensionException(Yii::t('app', 'User model failed to save record.'));
            }

            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollBack();
            Yii::error(sprintf('%s.%s: for user: %s - %s', __CLASS__, __METHOD__, $this->getUserFullName(), $e->getMessage() ));
            Yii::$app->session->addFlash('error', $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Terminates user account
     * @return bool
     * @throws Exception
     */
    public function terminateUserAccount()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //$currentTimeZone = new \DateTimeZone(\Yii::$app->timeZone);
            //$terminated_at = (new \DateTime('now', $currentTimeZone))->format('Y-m-d H:i:sO');

            $this->setScenario(User::SCENARIO_TERMINATE_ACCOUNT);
            $this->setAttribute('status', User::USER_STATUS_TERMINATED);
            $this->setAttribute('terminated_at', new Expression('NOW()'));
            $this->setAttribute('terminated_by', Yii::$app->getUser()->getId());

            if (!$this->save()) {
                throw new AccountTerminationException(Yii::t('app', 'User model failed to save record.'));
            }

            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollBack();
            Yii::error(sprintf('%s.%s: for user: %s - %s', __CLASS__, __METHOD__, $this->getUserFullName(), $e->getMessage() ));
            Yii::$app->session->addFlash('error', $e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * Returns count of reviews for user
     * @return int
     */
    public function getCountOfReviews()
    {
        $review_count = 0;
        $ratingDetails = $this->userRating->ratingDetails;

        if(empty($ratingDetails)){
            return $review_count;
        }

        $review_count = count(array_filter($ratingDetails, function($item){
            return ($item->review_rate !== 0);
        }));

        return $review_count;
    }

    /**
     * Returns verification message to show field state
     * @param string $field
     * @param bool $on_form
     * @return string|null
     */
    public function verificationCheck($field, $on_form = false)
    {
        $msg = Html::tag('span', Yii::t('app', 'Field not found'), ['class' => 'text-danger']);

        if($this->hasAttribute($field)){
            $changeHistory = ChangeHistory::getNotVerified(Yii::$app->user->id, $field);
            $value = $msg = ($changeHistory === null) ? $this->$field : $changeHistory->new_value;

            if(empty($value) || !in_array($field, ChangeHistory::verificationNeededAttributes(), true)){
                return null;
            }

            $verify_link = '';
            switch ($field){
                case 'phone1':
                case 'phone2':
                    $value = Yii::$app->formatter->asPhone($value);
                    $verify_link = Html::a(Yii::t('app', 'Verify'), ['/profile/verify-phone', 'field' => $field], [
                        'class' => 'btn btn-primary btn-xs',
                        'data' => [
                            'toggle' => 'modal',
                            'target' => '#phone_verification_modal_window',
                        ],
                    ]);
                    break;
                case 'email':
                    $value = Yii::$app->formatter->asEmail($value);
                    $verify_link = Html::a(Yii::t('app', 'Verify'), ['/profile/send-verification-email'], [
                        'class' => 'btn btn-primary btn-xs',
                        'data' => [
                            'method' => 'post',
                            'confirm' => Yii::t('app', 'Are you sure you want to resend verification email again?')
                        ]
                    ]);
                    break;
            }

            $dismiss_link = Html::a(Yii::t('app', 'Dismiss'), ['/profile/dismiss-change', 'field' => $field], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'method' => 'post',
                    'confirm' => Yii::t('app', 'Are you sure you want to dismiss changes and restore {attribute}?', ['attribute' => $this->getAttributeLabel($field)])
                ]
            ]);
            $buttons = Html::tag('div', $verify_link. '&nbsp;&nbsp;' .$dismiss_link, ['class' => 'hint-block']);

            if($changeHistory === null){
                $msg = ($on_form || empty($value)) ? null : Yii::t('app', '{value} {message}', [
                        'value' => $value,
                        'message' => Html::tag('span', Yii::t('app','Verified'), ['class' => 'label label-success small'])]
                );
            }else{
                $msg = Yii::t('app', '{value} {message}', [
                        'value' => $value,
                        'message' => Html::tag('span', Yii::t('app','Not verified'), ['class' => 'text text-danger small'])]
                );

                $msg = ($on_form) ? Html::tag('div', $msg, ['class' => 'alert alert-danger small']) : Html::tag('div', $msg.$buttons);
            }
        }
        return $msg;
    }

    /**
     * Returns a list of customers having patients for drop down box
     * @param bool $status
     * @param bool $filter_by_has_patient
     * @param bool $only_agency_name
     * @return array
     */
    public static function customerList(bool $status = true, bool $filter_by_has_patient = false, bool $only_agency_name = false):array
    {
        $query = self::find()
            ->customer($status)
            ->select(['id' => '[[user.id]]', 'name' => ($only_agency_name ? '[[user.agency_name]]' : "concat_ws(' ', [[user.first_name]], [[user.last_name]])") ])
            ->asArray();
        if($filter_by_has_patient) {
            $query->hasPatient(true);
        }
        return ArrayHelper::map($query->all(), 'id', 'name');
    }

    /**
     * Returns a list of providers having service for drop down box
     * @param bool $status
     * @param bool $filter_by_has_service
     * @return array
     */
    public static function providerList(bool $status = true, bool $filter_by_has_service = false):array
    {
        $query = self::find()
            ->provider($status)
            ->select(['id' => '[[user.id]]', 'name' => "concat_ws(' ', [[user.first_name]], [[user.last_name]])"])
            ->asArray();
        if($filter_by_has_service) {
            $query->hasService(true);
        }
        return ArrayHelper::map($query->all(), 'id', 'name');
    }

    /**
     * Returns a list of RPT providers having given service for drop down box
     * @param int $service_id
     * @return array
     */
    public static function rptProviderListWithService($service_id):array
    {
        if(empty($service_id)) {
            return [];
        }
        $service = Service::findOne($service_id);
        $rptUsers = array_filter($service->rptUsers, function($item){
            $status = true;
            $status = ($status && ($item->status === User::USER_STATUS_ACTIVE));
            $status = ($status && ($item->isProviderCredentialsApproved()));
            return $status;
        });
        return ($service === null) ? [] : ArrayHelper::map($rptUsers, 'id', 'first_name,last_name');
    }

    /**
     * Returns a list of PTA providers having given service for drop down box
     * @param int $service_id
     * @return array
     */
    public static function ptaProviderListWithService($service_id):array
    {
        if(empty($service_id)) {
            return [];
        }
        $service = Service::findOne($service_id);
        $ptaUsers = array_filter($service->ptaUsers, function($item){
            $status = true;
            $status = ($status && ($item->status === User::USER_STATUS_ACTIVE));
            $status = ($status && ($item->isProviderCredentialsApproved()));
            return $status;
        });
        return ($service === null) ? [] : ArrayHelper::map($ptaUsers, 'id', 'first_name,last_name');
    }

    public function hasProviderService () {
        if ($this->role->item_name !== self::USER_PROVIDER) {
            return false;
        }
        return !empty($this->service);
    }

    /**
     * Sets user service by give service id.
     * @param int $service_id Service ID
     * @throws ServiceException
     * @throws UserNotFoundException
     */
    public function setService($service_id)
    {
        if(empty($this->id) || $this->role->item_name !== User::USER_PROVIDER) {
            throw new UserNotFoundException('Therapist not found.');
        }

        $service = Service::findOne($service_id);
        if(empty($service)) {
            throw new ServiceException('Service not found.');
        }

        UserService::deleteAll(['AND', 'user_id = :userId'], [':userId' => $this->id]);
        $userService = new UserService();
        $userService->setAttributes(['user_id' => $this->id, 'service_id' => $service->id]);

        if (!$userService->save()) {
            throw new ServiceException(Yii::t('app', 'Service not assigned to the user.'));
        }
    }

    /**
     * Returns boolean true value if user is provider with all approved credentials
     * @return bool
     */
    public function isProviderCredentialsApproved () {
        if ($this->role->item_name !== self::USER_PROVIDER) {
            return false;
        }

        if (empty($this->userCredentials)) {
            return false;
        }

        if (!empty($this->userPendingCredentials) || !empty($this->userExpiredCredentials)) {
            return false;
        }

        return true;
    }

    /**
     * Returns comma separated languages
     * @return string
     */
    public function getUserLanguage ()
    {
        $result = null;
        if (is_array($this->language)) {
            $tmp = [];
            foreach ($this->language as $language) {
                $tmp[] = Language::englishNameByCode($language);
            }
            $result = implode(', ', $tmp);
        }
        return $result;
    }

    /**
     * Returns comma separated county coverage
     * @return string
     */
    public function getUserCoveredCounty ()
    {
        $result = null;
        if (is_array($this->covered_county)) {
            $tmp = [];
            foreach ($this->covered_county as $covered_county) {
                $tmp[] = UsCity::getCountyName($covered_county);
            }
            $result = implode(', ', $tmp);
        }
        return $result;
    }

    /**
     * Returns comma separated city coverage
     * @return string
     */
    public function getUserCoveredCity ()
    {
        $result = null;
        if (is_array($this->covered_city)) {
            $tmp = [];
            foreach ($this->covered_city as $covered_city) {
                $tmp[] = UsCity::getCityName($covered_city);
            }
            $result = implode(', ', $tmp);
        }
        return $result;
    }

    /**
     * Check if user account is active
     * @return bool
     */
    public function isActiveAccount()
    {
        if(empty($this->status)){
            return false;
        }
        return ($this->status === ConstHelper::STATUS_ACTIVE);
    }

    /**
     * Checks if user is active RPT on the given order
     * @param int $id Order ID to check
     * @return bool
     */
    public function isActiveRPTOnOrder($id)
    {
        if( !($this->isRPT && !empty($this->userOrders)) ) {
            return false;
        }

        $userOrders = ArrayHelper::index($this->userOrders, 'order_id');
        return (!empty($userOrders[$id]) && ($userOrders[$id]->status === ConstHelper::STATUS_ACTIVE));
    }

    /**
     * Checks if user is active PTA on the given order
     * @param int $id Order ID
     * @return bool
     */
    public function isActivePTAOnOrder($id)
    {
        if( !($this->isPTA && !empty($this->userOrders)) ) {
            return false;
        }

        $userOrders = ArrayHelper::index($this->userOrders, 'order_id');
        return (!empty($userOrders[$id]) && ($userOrders[$id]->status === ConstHelper::STATUS_ACTIVE));
    }
}