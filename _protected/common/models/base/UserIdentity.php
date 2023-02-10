<?php

namespace common\models\base;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use common\models\User;

/**
 * UserIdentity class for "user" table.
 * This is a base user class that is implementing IdentityInterface.
 * User model should extend from this model, and other user related models should
 * extend from User model.
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $account_activation_token
 * @property string $phone_number_validation_code
 * @property string $access_token
 * @property string $auth_key
 * @property string $status
 * @property string $authKey
 * @property string $facebook_id
 * @property string $google_id
 * @property string $title
 * @property string $agency_name
 * @property string $rep_position
 * @property string $first_name
 * @property string $last_name
 * @property string $gender
 * @property string $phone1
 * @property string $phone2
 * @property string $lat
 * @property string $lng
 * @property string $address
 * @property string $city
 * @property string $state
 * @property string $country
 * @property string $zip_code
 * @property string $timezone
 * @property string $language
 * @property string $covered_county
 * @property string $covered_city
 * @property string $service_rate
 * @property string $ip_address
 * @property string $website_address
 * @property string $emergency_contact_name
 * @property string $emergency_contact_number
 * @property string $emergency_contact_relationship
 * @property string $note
 * @property string $note_email_news_and_promotions
 * @property string $note_email_account_updated
 * @property string $note_email_order_submitted
 * @property string $note_email_order_accepted
 * @property string $note_email_order_rejected
 * @property string $note_email_order_canceled
 * @property string $note_email_order_reminder
 * @property string $note_email_rate_service
 * @property string $note_sms_news_and_promotions
 * @property string $note_sms_account_updated
 * @property string $note_sms_order_submitted
 * @property string $note_sms_order_accepted
 * @property string $note_sms_order_rejected
 * @property string $note_sms_order_canceled
 * @property string $note_sms_order_reminder
 * @property string $note_sms_rate_service
 * @property string $suspension_reason
 * @property string $suspended_by
 * @property string $suspended_at
 * @property string $terminated_by
 * @property string $terminated_at
 * @property string $termination_reason
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property string|array $role
 * @property bool $isSuperAdmin
 * @property bool $isAdmin
 * @property bool $isEditor
 * @property bool $isProvider
 * @property bool $isCustomer
 * @property bool $isRPT
 * @property bool $isPTA
 */
class UserIdentity extends ActiveRecord implements IdentityInterface
{
    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

//------------------------------------------------------------------------------------------------//
// IDENTITY INTERFACE IMPLEMENTATION
//------------------------------------------------------------------------------------------------//

    /**
     * Finds an identity by the given ID.
     *
     * @param  int|string $id The user id.
     * @return IdentityInterface|static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => [User::USER_STATUS_ACTIVE, User::USER_STATUS_INACTIVE]]);
    }

    /**
     * Finds an identity by the given access token.
     *
     * @param mixed $token
     * @param null $type
     * @return void|IdentityInterface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token, 'status' => [User::USER_STATUS_ACTIVE, User::USER_STATUS_INACTIVE]]);
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     *
     * @return int|mixed|string
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given
     * identity ID. The key should be unique for each individual user, and
     * should be persistent so that it can be used to check the validity of
     * the user identity. The space of such keys should be big enough to defeat
     * potential identity attacks.
     *
     * @return string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     * 
     * @param  string  $authKey The given auth key.
     * @return boolean          Whether the given auth key is valid.
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

//------------------------------------------------------------------------------------------------//
// IMPORTANT IDENTITY HELPERS
//------------------------------------------------------------------------------------------------//


    /**
     * Generates "remember me" authentication key.
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Validates password.
     *
     * @param string $password
     * @return bool
     *
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param  string $password
     * 
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Returns role of logged user
     * @return array|string
     */
    public function getRole()
    {
        if(Yii::$app->user->isGuest){
            return 'guest';
        }
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if(!empty($roles)){
            $roles = array_keys($roles);
            if (count($roles) == 1){
                return $roles[0];
            }
        }
        return $roles;
    }

    public function getIsCustomer()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }
        return ($this->role === User::USER_CUSTOMER);
    }

    public function getIsProvider()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }
        return ($this->role === User::USER_PROVIDER);
    }

    public function getIsEditor()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }
        return ($this->role === User::USER_EDITOR);
    }

    public function getIsAdmin()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }
        return ($this->role === User::USER_ADMIN);
    }

    public function getIsSuperAdmin()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }
        return ($this->role === User::USER_SUPER_ADMIN);
    }

    public function getIsRPT()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }
        return ($this->title === User::USER_TITLE_RPT);
    }

    public function getIsPTA()
    {
        if(Yii::$app->user->isGuest){
            return false;
        }
        return ($this->title === User::USER_TITLE_PTA);
    }
}
