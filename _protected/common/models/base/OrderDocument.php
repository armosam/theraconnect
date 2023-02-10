<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\Order;
use common\models\Patient;
use common\models\UserOrder;
use common\helpers\ConstHelper;
use common\traits\FileManagerTrait;
use common\models\queries\OrderQuery;
use common\models\queries\PatientQuery;
use common\models\queries\UserOrderQuery;
use common\models\queries\OrderDocumentQuery;

/**
 * This is the model class for table "{{%order_document}}".
 *
 * @property int $id
 * @property int $order_id
 * @property string $document_type
 * @property string|null $mime_type
 * @property int|null $file_size
 * @property string|null $file_name
 * @property resource|null $file_content
 * @property string|null $file_content_uri
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $status
 * @property int|null $ordering
 *
 * @property Order $order
 * @property Patient $patient
 * @property UserOrder $userOrder
 */
class OrderDocument extends ActiveRecord
{
    use FileManagerTrait;

    public const DOCUMENT_TYPE_INTAKE = 'I';
    public const DOCUMENT_TYPE_FORM485 = 'F';
    public const DOCUMENT_TYPE_OTHER = 'O';

    /**
     * Temporary attribute for storing a file
     * @var UploadedFile
     */
    public $upload_file;

    /**
     * Sometimes we need to keep temp file for future use
     * @var bool $deleteTempFile
     */
    public $deleteTempFile = true;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_document}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'document_type'], 'required'],
            [['order_id', 'file_size', 'created_by', 'updated_by', 'ordering'], 'integer'],
            [['file_content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            ['document_type', 'in', 'range' => [self::DOCUMENT_TYPE_INTAKE, self::DOCUMENT_TYPE_FORM485, self::DOCUMENT_TYPE_OTHER]],
            ['status', 'in', 'range' => [ConstHelper::STATUS_ACTIVE,ConstHelper::STATUS_PASSIVE,ConstHelper::STATUS_DELETED]],
            [['mime_type', 'file_name', 'file_content_uri'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'mimeTypes' => 'application/pdf, application/word, plain/text, image/jpg, image/jpeg, image/png', 'extensions' => 'pdf, txt, doc, docx, png, jpg, jpeg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'document_type' => Yii::t('app', 'Document Type'),
            'upload_file' => Yii::t('app', 'Attached Document'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'file_size' => Yii::t('app', 'File Size'),
            'file_name' => Yii::t('app', 'File Name'),
            'file_content' => Yii::t('app', 'File Content'),
            'file_content_uri' => Yii::t('app', 'File Content Uri'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
            'ordering' => Yii::t('app', 'Ordering'),
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
     * Relation with Order model
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('orderDocuments');
    }

    /**
     * Relation with Patient model
     *
     * @return ActiveQuery|PatientQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::class, ['id' => 'patient_id'])->via('order');
    }

    /**
     * Relation with UserOrder model
     *
     * @return ActiveQuery|UserOrderQuery
     */
    public function getUserOrder()
    {
        return $this->hasOne(UserOrder::class, ['order_id' => 'id'])->via('order');
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
     * {@inheritdoc}
     * @return OrderDocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderDocumentQuery(get_called_class());
    }
}
