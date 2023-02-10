<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use Exception;
use common\helpers\ConstHelper;
use common\exceptions\ChangeHistoryException;
use common\exceptions\DataVerificationException;

/**
 * Class ChangeHistory
 * @package common\models
 */
class ChangeHistory extends base\ChangeHistory
{
    protected $_verification_fields = [
        'email',
        'phone1',
        'phone2'
    ];

    /**
     * @var array $_field_exceptions
     */
    protected $_excepted_fields = [
        'password_reset_token',
        'account_activation_token',
        'phone_number_validation_code',
        'auth_key',
        'updated_at',
        'updated_by',
        'access_token',
        'password_hash',
        'facebook_id',
        'google_id',
        'ip_address'
    ];

    /**
     * Returns an array of fields that should be excluded during save check
     * @return array
     */
    public static function exceptedAttributes() : array
    {
        return (new self())->_excepted_fields;
    }

    /**
     * Returns an array of fields that should be verified when changing
     * @return array
     */
    public static function verificationNeededAttributes() : array
    {
        return (new self())->_verification_fields;
    }

    /**
     * Saves all account changes in the history
     * If field needs spacial verification it will update history for that field to no more need to be verified
     * Then it will add new field and value in the history and will set it as needs to be verified (status=Y)
     * @param int $user_id
     * @param string $field_name
     * @param string $old_value
     * @param string $new_value
     * @return ChangeHistory|ActiveRecord|null
     */
    public static function saveChanges($user_id, $field_name, $old_value, $new_value)
    {
        if (in_array($field_name, self::exceptedAttributes(), true)){
            return null;
        }

        $model = self::getNotVerified($user_id, $field_name);
        try{
            // If there is verified record then consider to update existing instead to adding new record
            if($model === null){
                $model = new self;
                $model->setAttribute('user_id', $user_id);
                $model->setAttribute('field_name', $field_name);
                $model->setAttribute('old_value',$old_value?(string)$old_value:null);
            }

            if (in_array($field_name, self::verificationNeededAttributes(), true)){
                (!empty($new_value)) ? $model->generateVerificationCode($field_name) : $model->setAttribute('verification_code', null );
            }

            $model->setAttribute('new_value', !empty($new_value) ? (string)$new_value : "Removed: $model->old_value");
            $model->setAttribute('status',(!empty($new_value) && in_array($field_name, self::verificationNeededAttributes(), true)) ? ConstHelper::FLAG_YES : ConstHelper::FLAG_NO);

            if (!$model->save()){
                throw new ChangeHistoryException('User changes not saved in the change history.' . print_r($model->getErrors(null), true));
            }
        }catch (Exception $e){
            Yii::error('Change History not saved: '. $e->getMessage(), 'ChangeHistory-'.__FUNCTION__);
        }
        return $model;
    }

    /**
     * Returns change history model if verification needed for given field
     * @param int $user_id
     * @param string $field_name
     * @return ChangeHistory|ActiveRecord|null
     */
    public static function getNotVerified($user_id, $field_name)
    {
        return self::find()->where(['user_id' => $user_id, 'field_name' => $field_name, 'status' => ConstHelper::FLAG_YES])->one();
    }

    /**
     * Returns model by verification code
     * @param string $code
     * @return ChangeHistory|null
     */
    public static function findByVerificationCode($code)
    {
        return self::findOne(['verification_code' => $code, 'status' => ConstHelper::FLAG_YES]);
    }

    /**
     * Checks verification token is not expired and returns history object
     * @param string $token
     * @return ChangeHistory|null
     */
    public static function findByVerificationToken($token)
    {
        if (empty($token)){ return null; }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.accountActivationTokenExpire'];
        if(($timestamp + $expire) < time()){
            return null;
        }
        return self::findByVerificationCode($token);
    }

    /**
     * Sets field changes verified
     * @return bool
     */
    public function setVerified()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $this->setAttribute('status', ConstHelper::FLAG_NO);
            $this->setAttribute('verification_code', null);
            if (!$this->save()) {
                throw new ChangeHistoryException('Change History failed to save data.');
            }

            $user = User::findOne([$this->user_id]);
            if ($user === null) {
                throw new DataVerificationException('User not found. ID:'.$this->user_id);
            }

            $user->setScenario(User::SCENARIO_DEFAULT);
            $user->setAttribute($this->field_name, $this->new_value);
            if (!$user->save(true, [$this->field_name])) {
                throw new DataVerificationException("Field {$this->field_name} failed to verify". implode('|', $user->getErrorSummary(false)));
            }

            $transaction->commit();
            return true;
        }catch (Exception $e){
            Yii::error('Verification failed. '. $e->getMessage(), 'ChangeHistory-'.__FUNCTION__);
            $transaction->rollBack();
        }
        return false;
    }

    /**
     * Generates unique verification code and assigns to current instance
     * @param string $field_name
     * @return bool
     * @throws Exception
     */
    public function generateVerificationCode($field_name)
    {
        if($field_name === 'email'){
            $verification_code = Yii::$app->security->generateRandomString() . '_' . time();
        }elseif (in_array($field_name, ['phone1', 'phone2'])){
            $verification_code = (string)random_int(100000, 999999);
        }else{
            return false;
        }

        if (self::findByVerificationCode($verification_code)) {
            $this->generateVerificationCode($field_name);
        } else {
            $this->setAttribute('verification_code', $verification_code);
        }

        return true;
    }

    /**
     * Restores not verified chages to last not verified position
     * It returns true if success otherwise false
     * @param int $user_id
     * @param string $field_name
     * @return bool
     * @throws ChangeHistoryException
     */
    public static function restoreNotVerifiedChange($user_id, $field_name) : bool
    {
        $model = self::getNotVerified($user_id, $field_name);
        if($model === null){
            throw new ChangeHistoryException('Not verified field change not found to be restored.');
        }

        try{
            $model->setAttribute('new_value', "Restored: $model->old_value");
            $model->setAttribute('verification_code', null);
            $model->setAttribute('status', ConstHelper::FLAG_NO);
            if (!$model->save()){
                throw new ChangeHistoryException('Change History not saved. ' . implode('|', $model->getErrorSummary(false)));
            }
        }catch (Exception $e){
            Yii::error('Restoring not verified changes failed. '. $e->getMessage(), 'ChangeHistory-'.__FUNCTION__);
            return false;
        }
        return true;
    }

}
