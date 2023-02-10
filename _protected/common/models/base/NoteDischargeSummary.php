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
use common\models\queries\NoteDischargeSummaryQuery;

/**
 * This is the model class for table "{{%note_discharge_summary}}".
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
 * @property string|null $pt
 * @property string|null $ot
 * @property string|null $patient_name
 * @property string|null $mrn
 * @property string|null $diagnosis
 * @property string|null $discharge_reason_no_care_needed
 * @property string|null $discharge_reason_admission_hospital
 * @property string|null $discharge_reason_admission_snf_icf
 * @property string|null $discharge_reason_pt_assumed_responsibility
 * @property string|null $discharge_reason_pt_moved_out_service_area
 * @property string|null $discharge_reason_lack_of_progress
 * @property string|null $discharge_reason_pt_refused_service
 * @property string|null $discharge_reason_transfer_hha
 * @property string|null $discharge_reason_transfer_op_rehab
 * @property string|null $discharge_reason_death
 * @property string|null $discharge_reason_lack_of_funds
 * @property string|null $discharge_reason_transfer_hospice
 * @property string|null $discharge_reason_transfer_personal_agency
 * @property string|null $discharge_reason_md_request
 * @property string|null $discharge_reason_other
 * @property string|null $mental_oriented
 * @property string|null $mental_forgetful
 * @property string|null $mental_depressed
 * @property string|null $mental_other
 * @property string|null $functional_ind
 * @property string|null $functional_sup
 * @property string|null $functional_asst
 * @property string|null $functional_dep
 * @property string|null $mobile_ind
 * @property string|null $mobile_sup
 * @property string|null $mobile_asst
 * @property string|null $mobile_dep
 * @property string|null $device_wheelchair
 * @property string|null $device_walker
 * @property string|null $device_crutches
 * @property string|null $device_cane
 * @property string|null $device_other
 * @property string|null $problem_identified1
 * @property string|null $problem_identified2
 * @property string|null $problem_identified3
 * @property string|null $problem_identified4
 * @property string|null $problem_identified5
 * @property string|null $status_of_problem_at_discharge1
 * @property string|null $status_of_problem_at_discharge2
 * @property string|null $status_of_problem_at_discharge3
 * @property string|null $status_of_problem_at_discharge4
 * @property string|null $status_of_problem_at_discharge5
 * @property string|null $summary_care_provided
 * @property string|null $goals_attained_yes
 * @property string|null $goals_attained_no
 * @property string|null $goals_attained_partial
 * @property string|null $discharge_plan_with_mid_supervision
 * @property string|null $discharge_plan_hha
 * @property string|null $discharge_plan_other
 * @property string|null $notification_of_discharge_tc_to_md
 * @property string|null $notification_of_discharge_tc_to_md_date
 * @property string|null $notification_of_discharge_tc_to_pt
 * @property string|null $notification_of_discharge_tc_to_pt_date
 * @property string|null $physician_name
 * @property string|null $therapist_name
 * @property string|null $therapist_title
 * @property string|null $therapist_signature
 * @property string|null $note_date
 *
 * @property User $provider
 * @property Order $order
 * @property Visit $visit
 * @property User $createdBy
 * @property User $updatedBy
 */
class NoteDischargeSummary extends ActiveRecord
{
    public $save_signature;
    public $submit;
    public $name = 'Discharge Summary';
    public $docRoute = 'document/note-discharge-summary';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%note_discharge_summary}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'visit_id', 'provider_id', 'note_date', 'therapist_signature'], 'required'],
            [['order_id', 'visit_id', 'provider_id', 'created_by', 'updated_by'], 'integer'],
            [['updated_by', 'submitted_by', 'accepted_by'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'submitted_at', 'accepted_at', 'therapist_signature'], 'safe'],
            [['status', 'submit', 'save_signature', 'pt', 'ot', 'discharge_reason_no_care_needed', 'discharge_reason_admission_hospital', 'discharge_reason_admission_snf_icf', 'discharge_reason_pt_assumed_responsibility', 'discharge_reason_pt_moved_out_service_area', 'discharge_reason_lack_of_progress', 'discharge_reason_pt_refused_service', 'discharge_reason_transfer_hha', 'discharge_reason_transfer_op_rehab', 'discharge_reason_death', 'discharge_reason_lack_of_funds', 'discharge_reason_transfer_hospice', 'discharge_reason_transfer_personal_agency', 'discharge_reason_md_request', 'mental_oriented', 'mental_forgetful', 'mental_depressed', 'functional_ind', 'functional_sup', 'functional_asst', 'functional_dep', 'mobile_ind', 'mobile_sup', 'mobile_asst', 'mobile_dep', 'device_wheelchair', 'device_walker', 'device_crutches', 'device_cane', 'goals_attained_yes', 'goals_attained_no', 'goals_attained_partial', 'discharge_plan_with_mid_supervision', 'discharge_plan_hha', 'notification_of_discharge_tc_to_md', 'notification_of_discharge_tc_to_pt'], 'string', 'max' => 1],
            [['patient_name', 'diagnosis', 'mental_other', 'discharge_reason_other', 'discharge_plan_other', 'summary_care_provided', 'physician_name', 'therapist_name'], 'string', 'max' => 255],
            [['mrn', 'device_other', 'problem_identified1', 'problem_identified2', 'problem_identified3', 'problem_identified4', 'problem_identified5', 'status_of_problem_at_discharge1', 'status_of_problem_at_discharge2', 'status_of_problem_at_discharge3', 'status_of_problem_at_discharge4', 'status_of_problem_at_discharge5', 'notification_of_discharge_tc_to_md_date', 'notification_of_discharge_tc_to_pt_date', 'therapist_title', 'note_date'], 'string', 'max' => 100],
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
            'patient_name' => Yii::t('app', 'Patient Name'),
            'mrn' => Yii::t('app', 'MRN'),
            'pt' => Yii::t('app', 'PT'),
            'ot' => Yii::t('app', 'OT'),
            'diagnosis' => Yii::t('app', 'Diagnosis'),
            'discharge_reason_no_care_needed' => Yii::t('app', 'No Care Needed'),
            'discharge_reason_admission_hospital' => Yii::t('app', 'Admission Hospital'),
            'discharge_reason_admission_snf_icf' => Yii::t('app', 'Admission SNF ICF'),
            'discharge_reason_pt_assumed_responsibility' => Yii::t('app', 'PT Assumed Responsibility'),
            'discharge_reason_pt_moved_out_service_area' => Yii::t('app', 'PT Moved Out Service Area'),
            'discharge_reason_lack_of_progress' => Yii::t('app', 'Lack Of Progress'),
            'discharge_reason_pt_refused_service' => Yii::t('app', 'PT Refused Service'),
            'discharge_reason_transfer_hha' => Yii::t('app', 'Transfer HHA'),
            'discharge_reason_transfer_op_rehab' => Yii::t('app', 'Transfer OP Rehab'),
            'discharge_reason_death' => Yii::t('app', 'Death'),
            'discharge_reason_lack_of_funds' => Yii::t('app', 'Lack Of Funds'),
            'discharge_reason_transfer_hospice' => Yii::t('app', 'Transfer Hospice'),
            'discharge_reason_transfer_personal_agency' => Yii::t('app', 'Transfer Personal Agency'),
            'discharge_reason_md_request' => Yii::t('app', 'MD Request'),
            'discharge_reason_other' => Yii::t('app', 'Other'),
            'mental_oriented' => Yii::t('app', 'Oriented'),
            'mental_forgetful' => Yii::t('app', 'Forgetful'),
            'mental_depressed' => Yii::t('app', 'Depressed'),
            'mental_other' => Yii::t('app', 'Other'),
            'functional_ind' => Yii::t('app', 'Ind'),
            'functional_sup' => Yii::t('app', 'Sup'),
            'functional_asst' => Yii::t('app', 'Asst'),
            'functional_dep' => Yii::t('app', 'Dep'),
            'mobile_ind' => Yii::t('app', 'Ind'),
            'mobile_sup' => Yii::t('app', 'Sup'),
            'mobile_asst' => Yii::t('app', 'Asst'),
            'mobile_dep' => Yii::t('app', 'Dep'),
            'device_wheelchair' => Yii::t('app', 'Wheelchair'),
            'device_walker' => Yii::t('app', 'Walker'),
            'device_crutches' => Yii::t('app', 'Crutches'),
            'device_cane' => Yii::t('app', 'Cane'),
            'device_other' => Yii::t('app', 'Other'),
            'problem_identified1' => Yii::t('app', 'Problem 1'),
            'problem_identified2' => Yii::t('app', 'Problem 2'),
            'problem_identified3' => Yii::t('app', 'Problem 3'),
            'problem_identified4' => Yii::t('app', 'Problem 4'),
            'problem_identified5' => Yii::t('app', 'Problem 5'),
            'status_of_problem_at_discharge1' => Yii::t('app', 'Status Of Problem 1 At Discharge'),
            'status_of_problem_at_discharge2' => Yii::t('app', 'Status Of Problem 2 At Discharge'),
            'status_of_problem_at_discharge3' => Yii::t('app', 'Status Of Problem 3 At Discharge'),
            'status_of_problem_at_discharge4' => Yii::t('app', 'Status Of Problem 4 At Discharge'),
            'status_of_problem_at_discharge5' => Yii::t('app', 'Status Of Problem 5 At Discharge'),
            'summary_care_provided' => Yii::t('app', 'Summary Care Provided'),
            'goals_attained_yes' => Yii::t('app', 'Yes'),
            'goals_attained_no' => Yii::t('app', 'No'),
            'goals_attained_partial' => Yii::t('app', 'Partial'),
            'discharge_plan_with_mid_supervision' => Yii::t('app', 'Home With Mid Supervision'),
            'discharge_plan_hha' => Yii::t('app', 'HHA'),
            'discharge_plan_other' => Yii::t('app', 'Other'),
            'notification_of_discharge_tc_to_md' => Yii::t('app', 'TC To MD'),
            'notification_of_discharge_tc_to_md_date' => Yii::t('app', 'TC To MD Date'),
            'notification_of_discharge_tc_to_pt' => Yii::t('app', 'TC To PT'),
            'notification_of_discharge_tc_to_pt_date' => Yii::t('app', 'TC To PT Date'),
            'physician_name' => Yii::t('app', 'Physician Name'),
            'therapist_name' => Yii::t('app', 'Discipline Name'),
            'therapist_title' => Yii::t('app', 'Title'),
            'therapist_signature' => Yii::t('app', 'Signature'),
            'note_date' => Yii::t('app', 'Date'),
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
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('dischargeSummaryNotes');
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
        return $this->hasOne(Visit::class, ['id' => 'visit_id', 'order_id' => 'order_id'])->inverseOf('dischargeSummaryNote');
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
     * @return NoteDischargeSummaryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoteDischargeSummaryQuery(get_called_class());
    }
}
