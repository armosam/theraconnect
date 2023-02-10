<?php

namespace common\models\base;

use Yii;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\Order;
use common\models\Visit;
use common\helpers\ConstHelper;
use common\models\queries\OrderQuery;
use common\models\queries\UserQuery;
use common\models\queries\VisitQuery;
use common\models\queries\NoteSupplementalQuery;

/**
 * This is the model class for table "{{%note_supplemental_order}}".
 *
 * @property int $id
 * @property int $order_id
 * @property int $visit_id
 * @property int $provider_id
 * @property string $status
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $submitted_by
 * @property string|null $submitted_at
 * @property int|null $accepted_by
 * @property string|null $accepted_at
 * @property string|null $dob
 * @property string|null $mrn
 * @property string|null $patient_name
 * @property string|null $patient_address
 * @property string|null $patient_phone_number
 * @property string|null $physician_name
 * @property string|null $physician_address
 * @property string|null $physician_phone_number
 * @property string|null $health_agency
 * @property string|null $patient_status_findings
 * @property string|null $frequency
 * @property string|null $physician_orders
 * @property string|null $therapist_name
 * @property string|null $therapist_title
 * @property string|null $therapist_signature
 * @property string|null $physician_signature
 * @property string|null $physician_date
 * @property string|null $note_date
 * @property string|null $time_in
 * @property string|null $time_out
 *
 * @property User $provider
 * @property Order $order
 * @property Visit $visit
 * @property User $createdBy
 * @property User $updatedBy
 */
class NoteSupplemental extends ActiveRecord
{
    public $save_signature;
    public $submit;
    public $name = 'Physician Order';
    public $docRoute = 'document/note-supplemental';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%note_supplemental}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'visit_id', 'provider_id', 'frequency', 'note_date', 'time_in', 'time_out', 'therapist_signature'], 'required'],
            [['order_id', 'visit_id', 'provider_id', 'created_by', 'updated_by'], 'integer'],
            [['updated_by', 'submitted_by', 'accepted_by'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'submitted_at', 'accepted_at', 'therapist_signature'], 'safe'],
            [['status', 'submit', 'save_signature'], 'string', 'max' => 1],
            [['dob', 'mrn', 'patient_phone_number', 'physician_phone_number', 'therapist_title', 'physician_date', 'note_date', 'time_in', 'time_out'], 'string', 'max' => 100],
            [['health_agency', 'patient_name', 'patient_address', 'physician_name', 'physician_address', 'patient_status_findings', 'frequency', 'physician_orders', 'therapist_name', 'physician_signature'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['provider_id' => 'id']],
            [['visit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Visit::class, 'targetAttribute' => ['visit_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Service Request'),
            'visit_id' => Yii::t('app', 'Visit'),
            'provider_id' => Yii::t('app', 'Therapist'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'submitted_by' => Yii::t('app', 'Submitted By'),
            'submitted_at' => Yii::t('app', 'Submitted At'),
            'accepted_by' => Yii::t('app', 'Accepted By'),
            'accepted_at' => Yii::t('app', 'Accepted At'),
            'dob' => Yii::t('app', 'DOB'),
            'mrn' => Yii::t('app', 'MRN'),
            'health_agency' => Yii::t('app', 'Health Agency'),
            'patient_name' => Yii::t('app', 'Patient Name'),
            'patient_address' => Yii::t('app', 'Patient Address'),
            'patient_phone_number' => Yii::t('app', 'Patient Phone Number'),
            'physician_name' => Yii::t('app', 'Physician Name'),
            'physician_address' => Yii::t('app', 'Physician Address'),
            'physician_phone_number' => Yii::t('app', 'Physician Phone Number'),
            'patient_status_findings' => Yii::t('app', 'Patient Status Findings'),
            'frequency' => Yii::t('app', 'Frequency'),
            'physician_orders' => Yii::t('app', 'Physician Orders'),
            'therapist_name' => Yii::t('app', 'Therapist Name'),
            'therapist_title' => Yii::t('app', 'Title'),
            'therapist_signature' => Yii::t('app', 'Therapist Signature'),
            'physician_signature' => Yii::t('app', 'Physician Signature'),
            'physician_date' => Yii::t('app', 'Date'),
            'note_date' => Yii::t('app', 'Date'),
            'time_in' => Yii::t('app', 'Time In'),
            'time_out' => Yii::t('app', 'Time Out'),
            'submit' => Yii::t('app', 'Save and Submit to Review'),
            'save_signature' => Yii::t('app', 'Save signature to use later'),
        ];
    }

    /**
     * Special behavior of model translation for configured fields
     * @return array Array of translation configuration
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
     * Gets query for [[Order]].
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('supplementalNotes');
    }

    /**
     * Gets query for [[Provider]].
     *
     * @return ActiveQuery|UserQuery
     */
    public function getProvider()
    {
        return $this->hasOne(User::class, ['id' => 'provider_id']);
    }

    /**
     * Gets query for [[Visit]].
     *
     * @return ActiveQuery|VisitQuery
     */
    public function getVisit()
    {
        return $this->hasOne(Visit::class, ['id' => 'visit_id', 'order_id' => 'order_id'])->inverseOf('supplementalNote');
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getCreatedBy(){
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getUpdatedBy(){
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return NoteSupplementalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoteSupplementalQuery(get_called_class());
    }
}
