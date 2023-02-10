<?php

namespace common\models\base;

use Yii;
use DateInterval;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
use common\models\User;
use common\helpers\ConstHelper;
use common\models\queries\UserCredentialQuery;

/**
 * This is the model class for the table "{{%user_credential}}".
 *
 * @property int $id
 * @property int $user_id
 * @property int $credential_type_id
 * @property string $assigned_number
 * @property string $mime_type
 * @property int $file_size
 * @property string $file_name
 * @property string $file_content
 * @property string $file_content_uri
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 * @property int $approved_by
 * @property string $approved_at
 * @property string $expire_date
 * @property string $status
 * @property int $ordering
 *
 * @property CredentialType $credentialType
 * @property User $user
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $approvedBy
 */
class UserCredential extends ActiveRecord
{
    public const STATUS_PENDING = 'P';
    public const STATUS_APPROVED = 'A';
    public const STATUS_EXPIRED = 'E';

    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';
    public const SCENARIO_APPROVE = 'approve';
    public const SCENARIO_EXPIRE = 'expire';

    /**
     * Temporary attribute for storing a file
     * @var UploadedFile
     */
    public $upload_file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_credential}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'credential_type_id', 'status'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['user_id', 'upload_file', 'expire_date', 'status'], 'required', 'on' => [self::SCENARIO_UPDATE]],
            [['approved_at', 'approved_by', 'status'], 'required', 'on' => [self::SCENARIO_APPROVE]],
            [['status'], 'required', 'on' => [self::SCENARIO_EXPIRE]],
            [['user_id', 'credential_type_id', 'file_size', 'created_by', 'updated_by', 'ordering'], 'integer'],
            [['assigned_number', 'mime_type', 'file_name', 'file_content_uri', 'status'], 'trim'],
            [['assigned_number', 'mime_type', 'file_name', 'file_content_uri'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_EXPIRED]],
            //[['created_at', 'updated_at', 'approved_by', 'approved_at'], 'safe'],
            [['expire_date'], 'date', 'format'=>'php:m/d/Y', 'min' => ConstHelper::dateTime()->add(new DateInterval('P2M'))->format('m/d/Y'), 'tooSmall' => '{attribute} should not be in 2 months or before {min}.'],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'mimeTypes' => 'application/pdf, application/word, plain/text, image/jpg, image/jpeg, image/png', 'extensions' => 'pdf, txt, doc, docx, png, jpg, jpeg'],
            [['credential_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => CredentialType::class, 'targetAttribute' => ['credential_type_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'credential_type_id' => Yii::t('app', 'Credential Type'),
            'assigned_number' => Yii::t('app', 'Credential Number'),
            'expire_date' => Yii::t('app', 'Expiration Date'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_size' => Yii::t('app', 'File Size'),
            'file_content' => Yii::t('app', 'File Content'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'upload_file' => Yii::t('app', 'Attached Document'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'approved_by' => Yii::t('app', 'Approved By'),
            'approved_at' => Yii::t('app', 'Approval Date'),
            'status' => Yii::t('app', 'Status'),
            'ordering' => Yii::t('app', 'Ordering'),
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
            'expire_date' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'expire_date',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'expire_date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'expire_date',
                ],
                'value' => function ($event) {
                    $format = 'Y-m-d';
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        $format = 'm/d/Y';
                    }
                    return ($this->expire_date && ConstHelper::dateTime($this->expire_date)) ? ConstHelper::dateTime($this->expire_date)->format($format) : null;
                },
            ],
            'assigned_number' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'assigned_number',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'assigned_number',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'assigned_number',
                ],
                'value' => function ($event) {
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        return !empty($this->assigned_number) ? base64_decode($this->assigned_number) : null;
                    }
                    return !empty($this->assigned_number) ? base64_encode($this->assigned_number) : null;
                },
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getCredentialType()
    {
        return $this->hasOne(CredentialType::class, ['id' => 'credential_type_id'])->inverseOf('userCredentials');
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->inverseOf('userCredentials');
    }

    /**
     * Get credential created user
     *
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get credential updated user
     *
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Get credential approved user
     *
     * @return ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'approved_by']);
    }

    /**
     * @inheritdoc
     * @return UserCredentialQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserCredentialQuery(get_called_class());
    }
}
