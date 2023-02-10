<?php

namespace common\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
use common\helpers\ConstHelper;
use common\models\queries\UserAvatarQuery;
use common\models\User;
use common\traits\ImageCropTrait;

/**
 * This is the model class for table "{{%user_avatar}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $file_name
 * @property int $file_size
 * @property resource $file_content
 * @property string $mime_type
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 * @property int $status
 *
 * @property User $user
 *
 */
class UserAvatar extends ActiveRecord
{
    public const SCENARIO_CREATE = 'create';

    use ImageCropTrait;

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
        return '{{%user_avatar}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_name', 'user_id', 'status'], 'required', 'on' => [self::SCENARIO_CREATE]],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'mimeTypes' => 'image/jpeg, image/png, image/gif', 'extensions' => 'png, jpg, jpeg, gif, bmp'],
            [['file_size', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => [ConstHelper::STATUS_ACTIVE, ConstHelper::STATUS_PASSIVE, ConstHelper::STATUS_DELETED]],
            [['mime_type'], 'string', 'max' => 50],
            [['file_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_size' => Yii::t('app', 'File Size'),
            'file_content' => Yii::t('app', 'File Content'),
            'upload_file' => Yii::t('app', 'Upload File'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Returns a list of behaviors that this component behave for
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
                'defaultValue' => User::USER_SYSTEM_ADMIN_ID,
                'skipUpdateOnClean' => false
            ],
        ];
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('avatar');
    }

    /**
     * @inheritdoc
     * @return UserAvatarQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserAvatarQuery(get_called_class());
    }
}
