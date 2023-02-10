<?php

namespace common\models;

use Yii;
use Exception;
use DateInterval;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\base\ErrorException;
use common\helpers\ArrayHelper;
use common\helpers\ConstHelper;
use common\traits\FileManagerTrait;

/**
 * Class UserCredential
 * @package common\models
 */
class UserCredential extends base\UserCredential
{
    use FileManagerTrait;

    /**
     * Disapprove credential
     * @return bool
     * @throws ErrorException
     */
    public function disapprove()
    {
        if($this->status === self::STATUS_PENDING) {
            throw new ErrorException('Credential already disapproved');
        }
        $this->setScenario(self::SCENARIO_APPROVE);
        $this->setAttribute('status', self::STATUS_PENDING);
        $this->setAttribute('approved_by', new Expression('NULL'));
        $this->setAttribute('approved_at', new Expression('NULL'));
        if(!$this->save()){
            throw new ErrorException('Record failed to save');
        }
        return true;
    }

    /**
     * Approve credential
     * @return bool
     * @throws ErrorException
     */
    public function approve()
    {
        if($this->status === self::STATUS_APPROVED) {
            throw new ErrorException('Credential already approved');
        }
        if(ConstHelper::dateTime($this->expire_date) <= ConstHelper::dateTime('now')) {
            throw new ErrorException('Credential is expired or missing');
        }

        if(ConstHelper::dateTime($this->expire_date) <= ConstHelper::dateTime('now')->add(new DateInterval('P2M'))) {
            throw new ErrorException('Credential is expiring in 2 months');
        }
        $this->setScenario(self::SCENARIO_APPROVE);
        $this->setAttribute('status', self::STATUS_APPROVED);
        $this->setAttribute('approved_by', Yii::$app->user->id);
        $this->setAttribute('approved_at', new Expression('NOW()'));
        if(!$this->save()){
            throw new ErrorException('Record failed to save');
        }
        return true;
    }

    /**
     * Expire credential
     * @return bool
     * @throws ErrorException
     */
    public function expire()
    {
        if($this->status !== self::STATUS_EXPIRED && ConstHelper::dateTime($this->expire_date) <= ConstHelper::dateTime('now')){
            $this->setScenario(self::SCENARIO_EXPIRE);
            $this->setAttribute('status', self::STATUS_EXPIRED);
            $this->setAttribute('approved_by', new Expression('NULL'));
            $this->setAttribute('approved_by', new Expression('NULL'));
            if(!$this->save()){
                throw new ErrorException('Record failed to save');
            }
        }
        return true;
    }

    /**
     * Returns credential statuses for drop down
     * If argument selected then returns status name
     * @param bool|string $selected
     * @return array|mixed
     */
    public static function credentialStatuses($selected = false)
    {
        $data = [
            self::STATUS_PENDING => Yii::t('app', 'Pending'),
            self::STATUS_APPROVED => Yii::t('app', 'Approved'),
            self::STATUS_EXPIRED => Yii::t('app', 'Expired'),
        ];

        if($selected !== false){
            return $data[$selected] ?? $selected;
        }

        return $data;
    }

    /**
     * Returns credential types for drop down
     * If argument selected then returns type name
     * @param bool|string $selected
     * @return array|mixed
     */
    public static function credentialTypes($selected = false)
    {
        $data = ArrayHelper::map(CredentialType::find()->active(true)->order(SORT_ASC)->all(), 'id', 'credential_type_name');

        if($selected !== false){
            return $data[$selected] ?? $selected;
        }

        return $data;
    }

    /**
     * Assigns and creates all required credentials to the given user
     * @param int $user_id
     * @throws Exception
     */
    public static function assignRequiredCredentials(int $user_id)
    {
        $credentialTypes = CredentialType::find()->active(true)->all();
        foreach ($credentialTypes as $credentialType) {
            $userCredential = new self(['scenario' => self::SCENARIO_CREATE]);
            $userCredential->setAttributes([
                'user_id' => $user_id,
                'credential_type_id' => $credentialType->id,
                'ordering' => $credentialType->ordering,
                'status' => UserCredential::STATUS_PENDING
            ]);
            if (!$userCredential->save()) {
                Yii::error(sprintf('User Credential Type %s failed to save for user_id: %s', $credentialType->id, $user_id), 'UserCredential::' . __FUNCTION__);
                throw new Exception(Yii::t('app', 'Unable to save User Credential {credential} for user {user}. Please try later', ['credential' => $credentialType->credential_type_name, 'user' => $user_id]));
            }
        }
    }

    /**
     * Setting some attributes automatically before an insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {
                return true;
            }

            if($this->getScenario() === self::SCENARIO_APPROVE){

                // Event trigger
            }
            if($this->getScenario() === self::SCENARIO_EXPIRE){

                // Event trigger
            }
            if($this->getScenario() === self::SCENARIO_CREATE){
                $this->setAttribute('status', self::STATUS_PENDING);
                // Event trigger
            }
            if($this->getScenario() === self::SCENARIO_UPDATE){
                $this->setAttribute('status', self::STATUS_PENDING);
                $this->setAttribute('approved_by', new Expression('NULL'));
                $this->setAttribute('approved_at', new Expression('NULL'));
                // Event trigger
            }

            // Upload document to the server
            if($this->upload_file instanceof UploadedFile && is_readable($this->upload_file->tempName)){

                $this->setAttribute('file_name', $this->upload_file->name);
                $this->setAttribute('file_size', $this->upload_file->size);
                $this->setAttribute('mime_type', $this->upload_file->type);

                $file_content_uri = $this->getFileContentURI('credentialFile');
                $this->setAttribute('file_content_uri', $file_content_uri);

                $file = null;
                if(empty(Yii::$app->params['credentialFile']['store_in_database'])){
                    $this->upload_file->saveAs($file_content_uri);
                } else{
                    $file = file_get_contents($this->upload_file->tempName);
                }
                $this->setAttribute('file_content', $file);
            }

            return true;
        }

        return false;
    }

    /**
     * Setting some attributes automatically after an insert or update of the table
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        /*$transaction = Yii::$app->db->beginTransaction();
        try {

            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            echo $e->getTraceAsString();
        }*/
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * After finding record it will load translations into particular parameters of model
     */
    public function afterFind()
    {
        parent::afterFind();
    }

}
