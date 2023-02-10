<?php

namespace common\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\ActiveRecord;
use common\helpers\ConstHelper;
use common\models\User;
use common\models\UserCredential;
use common\models\QualificationCategoryTranslation;
use common\models\queries\UserCredentialQuery;
use common\models\queries\CredentialTypeQuery;
use common\behaviors\TranslationBehavior;

/**
 * This is the model class for the table "{{%credential_type}}".
 *
 * @property int $id
 * @property string $icon_class
 * @property string $credential_type_name
 * @property string $assigned_number_label
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 * @property int $status
 * @property int $ordering
 *
 * @property UserCredential[] $userCredentials
 * @property UserCredential[] $userCredentialsActiveList
 * @property User $createdBy
 * @property User $updatedBy
 */
class CredentialType extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%credential_type}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['credential_type_name', 'status'], 'required'],
            [['credential_type_name', 'assigned_number_label', 'icon_class'], 'string', 'max' => 255],
            [['credential_type_name', 'assigned_number_label', 'icon_class', 'status'], 'trim'],
            [['created_by', 'updated_by', 'ordering'], 'integer'],
            [['created_at', 'updated_at'], 'date', 'format'=>'yyyy-MM-dd HH:mm:ssZ'],
            [['status'], 'in', 'range' => [ConstHelper::STATUS_DELETED, ConstHelper::STATUS_ACTIVE, ConstHelper::STATUS_PASSIVE]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'credential_type_name' => Yii::t('app', 'Credential Type Name'),
            'assigned_number_label' => Yii::t('app', 'Assigned Number Name'),
            'icon_class' => Yii::t('app', 'Icon CSS Class'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
            'ordering' => Yii::t('app', 'Ordering')
        ];
    }

    /**
     * Special behaviors
     * @return array THis is an array of behaviors
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
                'defaultValue' => User::USER_SYSTEM_ADMIN_ID,
                'skipUpdateOnClean' => false
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserCredentials()
    {
        return $this->hasMany(UserCredential::class, ['credential_type_id' => 'id'])->order(SORT_ASC)->inverseOf('credentialType');
    }

    /**
     * @return UserCredentialQuery
     */
    public function getUserCredentialsActiveList()
    {
        return $this->hasMany(UserCredential::class, ['credential_type_id' => 'id'])->active()->order(SORT_ASC)->inverseOf('credentialType');
    }

    /**
     * Get order created user
     *
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get order updated user
     *
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @inheritdoc
     * @return CredentialTypeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CredentialTypeQuery(get_called_class());
    }
}
