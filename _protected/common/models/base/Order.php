<?php

namespace common\models\base;

use Yii;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use common\models\User;
use common\models\Visit;
use common\models\Patient;
use common\models\Service;
use common\models\UserOrder;
use common\models\OrderDocument;
use common\helpers\ConstHelper;
use common\models\NoteCommunication;
use common\models\NoteDischargeOrder;
use common\models\NoteDischargeSummary;
use common\models\NoteEval;
use common\models\NoteProgress;
use common\models\NoteRouteSheet;
use common\models\NoteSupplemental;
use common\models\queries\NoteCommunicationQuery;
use common\models\queries\NoteDischargeOrderQuery;
use common\models\queries\NoteDischargeSummaryQuery;
use common\models\queries\NoteEvalQuery;
use common\models\queries\NoteProgressQuery;
use common\models\queries\NoteRouteSheetQuery;
use common\models\queries\NoteSupplementalQuery;
use common\models\queries\OrderQuery;
use common\models\queries\UserQuery;
use common\models\queries\UserOrderQuery;
use common\models\queries\PatientQuery;
use common\models\queries\ServiceQuery;
use common\models\queries\VisitQuery;
use common\models\queries\OrderDocumentQuery;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property int $id
 * @property string $order_number
 * @property int $patient_id
 * @property string $patient_name
 * @property string $patient_number
 * @property string $physician_name
 * @property string $physician_address
 * @property string $physician_phone_number
 * @property int $service_id
 * @property string $service_name
 * @property string $service_frequency
 * @property string $frequency_status
 * @property string $service_rate
 * @property string $certification_start_date
 * @property string $certification_end_date
 * @property string $allow_transfer_to
 * @property string|null $comment
 * @property int|null $submitted_by
 * @property string|null $submitted_at
 * @property int|null $accepted_by
 * @property string|null $accepted_at
 * @property int|null $completed_by
 * @property string|null $completed_at
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $status
 *
 * @property Patient $patient
 * @property User $customer
 * @property OrderDocument[] $orderDocuments
 * @property OrderDocument $orderIntakeDocument
 * @property OrderDocument $orderForm485Document
 * @property OrderDocument $orderOtherDocument
 * @property UserOrder[] $orderUsers
 * @property User[] $providers
 * @property User $orderRPT
 * @property User $orderPTA
 * @property Service $service
 * @property Visit[] $visits
 * @property NoteCommunication[] $communicationNotes
 * @property NoteDischargeOrder[] $dischargeOrderNotes
 * @property NoteDischargeSummary[] $dischargeSummaryNotes
 * @property NoteEval[] $evalNotes
 * @property NoteProgress[] $progressNotes
 * @property NoteRouteSheet[] $routeSheetNotes
 * @property NoteSupplemental[] $supplementalNotes
 * @property User $createdBy
 * @property User $updatedBy
 * @property User $submittedBy
 * @property User $acceptedBy
 * @property User $completedBy
 */
class Order extends ActiveRecord
{
    public const ORDER_STATUS_PENDING = 'P';
    public const ORDER_STATUS_SUBMITTED = 'S';
    public const ORDER_STATUS_ACCEPTED = 'A';
    public const ORDER_STATUS_COMPLETED = 'C';

    public const ORDER_FREQUENCY_STATUS_SUBMITTED = 'S';
    public const ORDER_FREQUENCY_STATUS_APPROVED = 'A';

    public const ORDER_SCENARIO_CREATE = 'create';
    public const ORDER_SCENARIO_SUBMIT = 'submit';
    public const ORDER_SCENARIO_ACCEPT = 'accept';
    public const ORDER_SCENARIO_COMPLETE = 'complete';
    public const ORDER_SCENARIO_APPROVE_FREQUENCY = 'approve_frequency';

    public $intake_file;
    public $form_485_file;
    public $other_file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_id', 'service_id', 'status'], 'required', 'on' => [self::ORDER_SCENARIO_CREATE, self::ORDER_SCENARIO_SUBMIT, self::ORDER_SCENARIO_ACCEPT, self::ORDER_SCENARIO_COMPLETE]],
            [['submitted_at', 'submitted_by', 'certification_start_date', 'certification_end_date', 'physician_name', 'physician_address', 'physician_phone_number', 'service_rate', 'status'], 'required', 'on' => [self::ORDER_SCENARIO_SUBMIT]],
            [['accepted_at', 'accepted_by', 'status'], 'required', 'on' => [self::ORDER_SCENARIO_ACCEPT]],
            [['completed_at', 'completed_by', 'status'], 'required', 'on' => [self::ORDER_SCENARIO_COMPLETE]],
            [['patient_id', 'service_id', 'service_rate', 'submitted_by', 'accepted_by', 'completed_by', 'created_by', 'updated_by'], 'integer'],
            [['submitted_at', 'accepted_at', 'completed_at', 'created_at', 'updated_at'], 'safe'],
            [['certification_start_date', 'certification_end_date'], 'date', 'format' => 'php:m/d/Y'],
            [['status'], 'in', 'range' => [self::ORDER_STATUS_PENDING, self::ORDER_STATUS_SUBMITTED, self::ORDER_STATUS_ACCEPTED, self::ORDER_STATUS_COMPLETED]],
            [['allow_transfer_to'], 'in', 'range' => [ConstHelper::FLAG_YES, ConstHelper::FLAG_NO], 'message' => Yii::t('app', 'To allow transfer please check this checkbox.')],
            [['frequency_status'], 'in', 'range' => [self::ORDER_FREQUENCY_STATUS_SUBMITTED, self::ORDER_FREQUENCY_STATUS_APPROVED]],
            [['order_number'], 'string', 'max' => 10],
            [['patient_name', 'patient_number', 'physician_name', 'physician_address', 'physician_phone_number', 'service_name', 'service_frequency', 'comment'], 'string', 'max' => 255],
            [['patient_id'], 'exist', 'skipOnError' => true, 'targetClass' => Patient::class, 'targetAttribute' => ['patient_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::class, 'targetAttribute' => ['service_id' => 'id']],
            [['intake_file', 'form_485_file', 'other_file'], 'file', 'skipOnEmpty' => true, 'mimeTypes' => 'application/pdf, application/word, plain/text, image/jpg, image/jpeg, image/png', 'extensions' => 'pdf, txt, doc, docx, png, jpg, jpeg'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_number' => Yii::t('app', 'Request Number'),
            'patient_id' => Yii::t('app', 'Patient Name'),
            'patient_name' => Yii::t('app', 'Patient Name'),
            'patient_number' => Yii::t('app', 'MR#'),
            'physician_name' => Yii::t('app', 'Physician Name'),
            'physician_address' => Yii::t('app', 'Physician Address'),
            'physician_phone_number' => Yii::t('app', 'Physician Phone Number'),
            'service_id' => Yii::t('app', 'Service Name'),
            'service_name' => Yii::t('app', 'Service Name'),
            'service_frequency' => Yii::t('app', 'Service Frequency'),
            'frequency_status' => Yii::t('app', 'Frequency Status'),
            'service_rate' => Yii::t('app', 'Service Rate($)'),
            'certification_start_date' => Yii::t('app', 'Certification Start'),
            'certification_end_date' => Yii::t('app', 'Certification End'),
            'allow_transfer_to' => Yii::t('app', 'Transfer To PTA'),
            'intake_file' => Yii::t('app', 'Intake Document'),
            'form_485_file' => Yii::t('app', 'Form-485 Document'),
            'other_file' => Yii::t('app', 'Other Document'),
            'comment' => Yii::t('app', 'Comments'),
            'submitted_by' => Yii::t('app', 'Submitted By'),
            'submitted_at' => Yii::t('app', 'Submitted At'),
            'accepted_by' => Yii::t('app', 'Accepted By'),
            'accepted_at' => Yii::t('app', 'Accepted At'),
            'completed_by' => Yii::t('app', 'Completed By'),
            'completed_at' => Yii::t('app', 'Completed At'),
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
            'certification_start_date' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'certification_start_date',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'certification_start_date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'certification_start_date',
                ],
                'value' => function ($event) {
                    $format = 'Y-m-d';
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        $format = 'm/d/Y';
                    }
                    return ($this->certification_start_date && ConstHelper::dateTime($this->certification_start_date)) ? ConstHelper::dateTime($this->certification_start_date)->format($format) : null;
                },
            ],
            'certification_end_date' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'certification_end_date',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'certification_end_date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'certification_end_date',
                ],
                'value' => function ($event) {
                    $format = 'Y-m-d';
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        $format = 'm/d/Y';
                    }
                    return ($this->certification_end_date && ConstHelper::dateTime($this->certification_end_date)) ? ConstHelper::dateTime($this->certification_end_date)->format($format) : null;
                },
            ],
            'phone_number' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'physician_phone_number',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'physician_phone_number',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'physician_phone_number',
                ],
                'value' => function ($event) {
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        return $this->physician_phone_number ? Yii::$app->formatter->asPhone($this->physician_phone_number) : null;
                    }
                    return $this->physician_phone_number ? '+1'.preg_replace('/\D/', '', $this->physician_phone_number) : null;
                },
            ],
        ];
    }

    /**
     * Gets query for [[Patient]].
     *
     * @return ActiveQuery|PatientQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::class, ['id' => 'patient_id'])->inverseOf('orders');
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery|UserQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(User::class, ['id' => 'customer_id'])->via('patient')->inverseOf('customerOrders');
    }

    /**
     * Relation with OrderDocument model
     *
     * @return ActiveQuery|OrderDocumentQuery
     */
    public function getOrderDocuments()
    {
        return $this->hasMany(OrderDocument::class, ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * Relation with OrderDocument model as Intake Document
     *
     * @return ActiveQuery|OrderDocumentQuery
     */
    public function getOrderIntakeDocument()
    {
        return $this->hasOne(OrderDocument::class, ['order_id' => 'id'])->andOnCondition(['order_document.document_type' => OrderDocument::DOCUMENT_TYPE_INTAKE]);
    }

    /**
     * Relation with OrderDocument model  as Form-485 Document
     *
     * @return ActiveQuery|OrderDocumentQuery
     */
    public function getOrderForm485Document()
    {
        return $this->hasOne(OrderDocument::class, ['order_id' => 'id'])->andOnCondition(['order_document.document_type' => OrderDocument::DOCUMENT_TYPE_FORM485]);
    }

    /**
     * Relation with OrderDocument model as Other Document
     *
     * @return ActiveQuery|OrderDocumentQuery
     */
    public function getOrderOtherDocument()
    {
        return $this->hasOne(OrderDocument::class, ['order_id' => 'id'])->andOnCondition(['order_document.document_type' => OrderDocument::DOCUMENT_TYPE_OTHER]);
    }

    /**
     * Relation with UserOrder model
     *
     * @return ActiveQuery|UserOrderQuery
     */
    public function getOrderUsers() {
        return $this->hasMany(UserOrder::class, ['order_id' => 'id'])->andOnCondition(['user_order.status' => ConstHelper::STATUS_ACTIVE])->inverseOf('order');
    }

    /**
     * Relation with User model as providers
     *
     * @return ActiveQuery|UserQuery
     */
    public function getProviders() {
        return $this->hasMany(User::class, ['id' => 'user_id'])->via('orderUsers')->inverseOf('providerOrders');
    }

    /**
     * Relation with User model as RPT
     *
     * @return ActiveQuery|UserQuery
     */
    public function getOrderRPT() {
        return $this->hasOne(User::class, ['id' => 'user_id'])->andOnCondition(['user.title' => User::USER_TITLE_RPT])->via('orderUsers');
    }

    /**
     * Relation with User model as PTA
     *
     * @return ActiveQuery|UserQuery
     */
    public function getOrderPTA() {
        return $this->hasOne(User::class, ['id' => 'user_id'])->andOnCondition(['user.title' => User::USER_TITLE_PTA])->via('orderUsers');
    }

    /**
     * Gets query for [[Service]].
     *
     * @return ActiveQuery|ServiceQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id'])->inverseOf('orders');
    }

    /**
     * Gets query for [[Visit]].
     *
     * @return ActiveQuery|VisitQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['order_id' => 'id'])->orderBy(['visited_at' => SORT_ASC])->inverseOf('order');
    }

    /**
     * Gets query for [[NoteCommunication]].
     *
     * @return ActiveQuery|NoteCommunicationQuery
     */
    public function getCommunicationNotes()
    {
        return $this->hasMany(NoteCommunication::class, ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * Gets query for [[NoteDischargeOrder]].
     *
     * @return ActiveQuery|NoteDischargeOrderQuery
     */
    public function getDischargeOrderNotes()
    {
        return $this->hasMany(NoteDischargeOrder::class, ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * Gets query for [[NoteDischargeSummary]].
     *
     * @return ActiveQuery|NoteDischargeSummaryQuery
     */
    public function getDischargeSummaryNotes()
    {
        return $this->hasMany(NoteDischargeSummary::class, ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * Gets query for [[NoteEval]].
     *
     * @return ActiveQuery|NoteEvalQuery
     */
    public function getEvalNotes()
    {
        return $this->hasMany(NoteEval::class, ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * Gets query for [[NoteProgress]].
     *
     * @return ActiveQuery|NoteProgressQuery
     */
    public function getProgressNotes()
    {
        return $this->hasMany(NoteProgress::class, ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * Gets query for [[NoteRouteSheet]].
     *
     * @return ActiveQuery|NoteRouteSheetQuery
     */
    public function getRouteSheetNotes()
    {
        return $this->hasMany(NoteRouteSheet::class, ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * Gets query for [[NoteSupplemental]].
     *
     * @return ActiveQuery|NoteSupplementalQuery
     */
    public function getSupplementalNotes()
    {
        return $this->hasMany(NoteSupplemental::class, ['order_id' => 'id'])->inverseOf('order');
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
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getSubmittedBy(){
        return $this->hasOne(User::class, ['id' => 'submitted_by']);
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getAcceptedBy(){
        return $this->hasOne(User::class, ['id' => 'accepted_by']);
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery|User
     */
    public function getCompletedBy(){
        return $this->hasOne(User::class, ['id' => 'completed_by']);
    }

    /**
     * {@inheritdoc}
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }
}
