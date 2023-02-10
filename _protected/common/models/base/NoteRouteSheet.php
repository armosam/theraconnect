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
use common\models\queries\NoteRouteSheetQuery;

/**
 * This is the model class for table "{{%note_route_sheet}}".
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
 * @property string|null $health_agency
 * @property string|null $patient_name
 * @property string|null $mrn
 * @property string|null $therapist_name
 * @property string|null $therapist_title
 * @property string|null $visit_code
 * @property string|null $note_date
 * @property string|null $time_in
 * @property string|null $time_out
 * @property int|null $visit_total_time
 * @property string|null $patient_signature
 * @property string|null $therapist_signature
 *
 * @property User $provider
 * @property Order $order
 * @property Visit $visit
 * @property User $createdBy
 * @property User $updatedBy
 */
class NoteRouteSheet extends ActiveRecord
{
    public $save_signature;
    public $submit;
    public $name = 'Route Sheet';
    public $docRoute = 'document/note-route-sheet';

    public const ROUTE_SHEET_VISIT_CODE_EVAL = 'EVAL';
    public const ROUTE_SHEET_VISIT_CODE_FOLLOW_UP = 'FOLLOW_UP';
    public const ROUTE_SHEET_VISIT_CODE_RECERTIFICATION = 'RECERTIFICATION';
    public const ROUTE_SHEET_VISIT_CODE_RESUMPTION_OF_CARE = 'RESUMPTION_OF_CARE';
    public const ROUTE_SHEET_VISIT_CODE_DISCHARGE = 'DISCHARGE';
    public const ROUTE_SHEET_VISIT_CODE_OTHER = 'OTHER';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%note_route_sheet}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'visit_id', 'provider_id', 'visit_code', 'note_date', 'time_in', 'time_out', 'patient_signature', 'therapist_signature'], 'required'],
            [['order_id', 'visit_id', 'provider_id', 'visit_total_time', 'created_by', 'updated_by'], 'integer'],
            [['updated_by', 'submitted_by', 'accepted_by'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'submitted_at', 'accepted_at', 'patient_signature', 'therapist_signature'], 'safe'],
            [['status', 'submit', 'save_signature'], 'string', 'max' => 1],
            [['health_agency', 'patient_name', 'therapist_name', 'therapist_title'], 'string', 'max' => 255],
            [['mrn', 'visit_code', 'note_date', 'time_in', 'time_out'], 'string', 'max' => 100],
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
            'health_agency' => Yii::t('app', 'Health Agency'),
            'patient_name' => Yii::t('app', 'Patient Name'),
            'mrn' => Yii::t('app', 'MRN'),
            'therapist_name' => Yii::t('app', 'Therapist Name'),
            'therapist_title' => Yii::t('app', 'Therapist Title'),
            'visit_code' => Yii::t('app', 'Visit Code'),
            'note_date' => Yii::t('app', 'Visit Date'),
            'time_in' => Yii::t('app', 'Start Time'),
            'time_out' => Yii::t('app', 'End Time'),
            'visit_total_time' => Yii::t('app', 'Total Time'),
            'patient_signature' => Yii::t('app', 'Patient Signature'),
            'therapist_signature' => Yii::t('app', 'Therapist Signature'),
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
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('routeSheetNotes');
    }

    /**
     * Gets query for [[Provider]].
     * Actually it should be connected to UserOrder table,
     * but to prevent edge cases we are connected to user table (to all users)
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
        return $this->hasOne(Visit::class, ['id' => 'visit_id', 'order_id' => 'order_id'])->inverseOf('routeSheetNote');
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
     * @return NoteRouteSheetQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoteRouteSheetQuery(get_called_class());
    }
}
