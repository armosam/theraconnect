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
use common\models\queries\NoteProgressQuery;

/**
 * This is the model class for table "{{%note_progress}}".
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
 * @property string|null $patient_name
 * @property string|null $mrn
 * @property string|null $dob
 * @property string|null $gender
 * @property string|null $service_provided_pt_evaluation
 * @property string|null $service_provided_therapeutic_exercises
 * @property string|null $service_provided_therapeutic_activities
 * @property string|null $service_provided_bed_mobility
 * @property string|null $service_provided_gait_training
 * @property string|null $service_provided_muscle_reduction
 * @property string|null $service_provided_prosthetic_training
 * @property string|null $service_provided_home_exercise_program
 * @property string|null $service_provided_ultrasound
 * @property string|null $service_provided_hot_pack
 * @property string|null $service_provided_cold_pack_message
 * @property string|null $service_provided_massage_mobilization
 * @property string|null $service_provided_joint_mobilization
 * @property string|null $service_provided_pt_cg_education
 * @property string|null $service_provided_other
 * @property string|null $vital_signs_bp
 * @property string|null $vital_signs_pulse
 * @property string|null $vital_signs_resp
 * @property string|null $vital_signs_spo2
 * @property string|null $vital_signs_temp
 * @property string|null $subjective_co
 * @property string|null $subjective_pain
 * @property string|null $subjective_fatigue
 * @property string|null $subjective_weakness
 * @property string|null $subjective_unsteady_gait
 * @property string|null $subjective_condition_improved
 * @property string|null $subjective_pain_intensity
 * @property string|null $subjective_pain_location
 * @property string|null $subjective_pain_description
 * @property string|null $subjective_pain_frequency_all_time
 * @property string|null $subjective_pain_frequency_daily
 * @property string|null $subjective_pain_frequency_less_daily
 * @property string|null $subjective_pain_no_pain
 * @property string|null $subjective_pain_other
 * @property string|null $bed_mobility_roll_unable
 * @property string|null $bed_mobility_roll_max
 * @property string|null $bed_mobility_roll_mod
 * @property string|null $bed_mobility_roll_min
 * @property string|null $bed_mobility_roll_cg
 * @property string|null $bed_mobility_roll_sba
 * @property string|null $bed_mobility_roll_s
 * @property string|null $bed_mobility_roll_i
 * @property string|null $bed_mobility_bridge_unable
 * @property string|null $bed_mobility_bridge_max
 * @property string|null $bed_mobility_bridge_mod
 * @property string|null $bed_mobility_bridge_min
 * @property string|null $bed_mobility_bridge_cg
 * @property string|null $bed_mobility_bridge_sba
 * @property string|null $bed_mobility_bridge_s
 * @property string|null $bed_mobility_bridge_i
 * @property string|null $bed_mobility_supine2sit_unable
 * @property string|null $bed_mobility_supine2sit_max
 * @property string|null $bed_mobility_supine2sit_mod
 * @property string|null $bed_mobility_supine2sit_min
 * @property string|null $bed_mobility_supine2sit_cg
 * @property string|null $bed_mobility_supine2sit_sba
 * @property string|null $bed_mobility_supine2sit_s
 * @property string|null $bed_mobility_supine2sit_i
 * @property string|null $bed_mobility_sit2supine_unable
 * @property string|null $bed_mobility_sit2supine_max
 * @property string|null $bed_mobility_sit2supine_mod
 * @property string|null $bed_mobility_sit2supine_min
 * @property string|null $bed_mobility_sit2supine_cg
 * @property string|null $bed_mobility_sit2supine_sba
 * @property string|null $bed_mobility_sit2supine_s
 * @property string|null $bed_mobility_sit2supine_i
 * @property string|null $transfer_sit2stand_unable
 * @property string|null $transfer_sit2stand_max
 * @property string|null $transfer_sit2stand_mod
 * @property string|null $transfer_sit2stand_min
 * @property string|null $transfer_sit2stand_cg
 * @property string|null $transfer_sit2stand_sba
 * @property string|null $transfer_sit2stand_s
 * @property string|null $transfer_sit2stand_i
 * @property string|null $transfer_bed2chair_unable
 * @property string|null $transfer_bed2chair_max
 * @property string|null $transfer_bed2chair_mod
 * @property string|null $transfer_bed2chair_min
 * @property string|null $transfer_bed2chair_cg
 * @property string|null $transfer_bed2chair_sba
 * @property string|null $transfer_bed2chair_s
 * @property string|null $transfer_bed2chair_i
 * @property string|null $transfer_toilet_unable
 * @property string|null $transfer_toilet_max
 * @property string|null $transfer_toilet_mod
 * @property string|null $transfer_toilet_min
 * @property string|null $transfer_toilet_cg
 * @property string|null $transfer_toilet_sba
 * @property string|null $transfer_toilet_s
 * @property string|null $transfer_toilet_i
 * @property string|null $transfer_shower_unable
 * @property string|null $transfer_shower_max
 * @property string|null $transfer_shower_mod
 * @property string|null $transfer_shower_min
 * @property string|null $transfer_shower_cg
 * @property string|null $transfer_shower_sba
 * @property string|null $transfer_shower_s
 * @property string|null $transfer_shower_i
 * @property string|null $gait_even_surface_distance
 * @property string|null $gait_even_surface_unable
 * @property string|null $gait_even_surface_max
 * @property string|null $gait_even_surface_mod
 * @property string|null $gait_even_surface_min
 * @property string|null $gait_even_surface_cg
 * @property string|null $gait_even_surface_sba
 * @property string|null $gait_even_surface_s
 * @property string|null $gait_even_surface_i
 * @property string|null $gait_uneven_surface_distance
 * @property string|null $gait_uneven_surface_unable
 * @property string|null $gait_uneven_surface_max
 * @property string|null $gait_uneven_surface_mod
 * @property string|null $gait_uneven_surface_min
 * @property string|null $gait_uneven_surface_cg
 * @property string|null $gait_uneven_surface_sba
 * @property string|null $gait_uneven_surface_s
 * @property string|null $gait_uneven_surface_i
 * @property string|null $gait_device
 * @property string|null $stairs_unable
 * @property string|null $stairs_max
 * @property string|null $stairs_mod
 * @property string|null $stairs_min
 * @property string|null $stairs_cg
 * @property string|null $stairs_sba
 * @property string|null $stairs_s
 * @property string|null $stairs_i
 * @property string|null $stairs_steps
 * @property string|null $stairs_device
 * @property string|null $pattern
 * @property string|null $precautions
 * @property string|null $balance_sitting_static_p_minus
 * @property string|null $balance_sitting_static_p
 * @property string|null $balance_sitting_static_p_plus
 * @property string|null $balance_sitting_static_f_minus
 * @property string|null $balance_sitting_static_f
 * @property string|null $balance_sitting_static_f_plus
 * @property string|null $balance_sitting_static_g_minus
 * @property string|null $balance_sitting_static_g
 * @property string|null $balance_sitting_static_g_plus
 * @property string|null $balance_standing_static_p_minus
 * @property string|null $balance_standing_static_p
 * @property string|null $balance_standing_static_p_plus
 * @property string|null $balance_standing_static_f_minus
 * @property string|null $balance_standing_static_f
 * @property string|null $balance_standing_static_f_plus
 * @property string|null $balance_standing_static_g_minus
 * @property string|null $balance_standing_static_g
 * @property string|null $balance_standing_static_g_plus
 * @property string|null $balance_sitting_dynamic_p_minus
 * @property string|null $balance_sitting_dynamic_p
 * @property string|null $balance_sitting_dynamic_p_plus
 * @property string|null $balance_sitting_dynamic_f_minus
 * @property string|null $balance_sitting_dynamic_f
 * @property string|null $balance_sitting_dynamic_f_plus
 * @property string|null $balance_sitting_dynamic_g_minus
 * @property string|null $balance_sitting_dynamic_g
 * @property string|null $balance_sitting_dynamic_g_plus
 * @property string|null $balance_standing_dynamic_p_minus
 * @property string|null $balance_standing_dynamic_p
 * @property string|null $balance_standing_dynamic_p_plus
 * @property string|null $balance_standing_dynamic_f_minus
 * @property string|null $balance_standing_dynamic_f
 * @property string|null $balance_standing_dynamic_f_plus
 * @property string|null $balance_standing_dynamic_g_minus
 * @property string|null $balance_standing_dynamic_g
 * @property string|null $balance_standing_dynamic_g_plus
 * @property string|null $therapeutic_exercises
 * @property string|null $patient_is_uncooperative
 * @property string|null $patient_requires_encouragement
 * @property string|null $patient_is_cooperative
 * @property string|null $patient_response_today_poor
 * @property string|null $patient_response_today_fair
 * @property string|null $patient_response_today_good
 * @property string|null $patient_response_today_excellent
 * @property string|null $patient_progress_toward_goals_poor
 * @property string|null $patient_progress_toward_goals_fair
 * @property string|null $patient_progress_toward_goals_good
 * @property string|null $patient_progress_toward_goals_excellent
 * @property string|null $patient_activity_tolerance_poor
 * @property string|null $patient_activity_tolerance_fair
 * @property string|null $patient_activity_tolerance_good
 * @property string|null $patient_activity_tolerance_excellent
 * @property string|null $patient_safety_mechanics_poor
 * @property string|null $patient_safety_mechanics_fair
 * @property string|null $patient_safety_mechanics_good
 * @property string|null $patient_safety_mechanics_excellent
 * @property string|null $assessment_comments
 * @property string|null $rom_ue
 * @property string|null $rom_le
 * @property string|null $rom_cervical
 * @property string|null $rom_trunk
 * @property string|null $rom_other
 * @property string|null $strength_ue
 * @property string|null $strength_le
 * @property string|null $strength_cervical
 * @property string|null $strength_trunk
 * @property string|null $strength_other
 * @property string|null $inadequate_safety_awareness
 * @property string|null $unable_ambulating_outdoors
 * @property string|null $difficulty_transfer
 * @property string|null $localized_weakness
 * @property string|null $inefficient_gait
 * @property string|null $bed_chair_bound
 * @property string|null $impaired_balance
 * @property string|null $requires_assistance_to_leave_home
 * @property string|null $profound_general_weakness
 * @property string|null $limited_rom
 * @property string|null $difficulty_manage_stairs
 * @property string|null $significant_effort_performing_tasks
 * @property string|null $pt_plan_comments
 * @property string|null $therapist_name
 * @property string|null $therapist_title
 * @property string|null $therapist_signature
 * @property string|null $therapist_name0
 * @property string|null $therapist_title0
 * @property string|null $therapist_signature0
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
class NoteProgress extends ActiveRecord
{
    /**
     * Cached user to be used for signature checking
     * @var User $_user
     */
    protected $_user;

    public $save_signature;
    public $submit;
    public $name = 'Progress Note';
    public $docRoute = 'document/note-progress';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%note_progress}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['order_id', 'visit_id', 'provider_id', 'note_date', 'time_in', 'time_out'], 'required'],
            [['order_id', 'visit_id', 'provider_id', 'created_by', 'updated_by'], 'integer'],
            [['updated_by', 'submitted_by', 'accepted_by'], 'default', 'value' => null],
            [['created_at', 'updated_at', 'submitted_at', 'accepted_at'], 'safe'],
            [['therapist_signature', 'therapist_signature0'], 'string'],
            [['status', 'submit', 'save_signature', 'gender', 'service_provided_pt_evaluation', 'service_provided_therapeutic_exercises', 'service_provided_therapeutic_activities', 'service_provided_bed_mobility', 'service_provided_gait_training', 'service_provided_muscle_reduction', 'service_provided_prosthetic_training', 'service_provided_home_exercise_program', 'service_provided_ultrasound', 'service_provided_hot_pack', 'service_provided_cold_pack_message', 'service_provided_massage_mobilization', 'service_provided_joint_mobilization', 'service_provided_pt_cg_education', 'subjective_pain', 'subjective_fatigue', 'subjective_weakness', 'subjective_unsteady_gait', 'subjective_condition_improved', 'subjective_pain_frequency_all_time', 'subjective_pain_frequency_daily', 'subjective_pain_frequency_less_daily', 'subjective_pain_no_pain', 'bed_mobility_roll_unable', 'bed_mobility_roll_max', 'bed_mobility_roll_mod', 'bed_mobility_roll_min', 'bed_mobility_roll_cg', 'bed_mobility_roll_sba', 'bed_mobility_roll_s', 'bed_mobility_roll_i', 'bed_mobility_bridge_unable', 'bed_mobility_bridge_max', 'bed_mobility_bridge_mod', 'bed_mobility_bridge_min', 'bed_mobility_bridge_cg', 'bed_mobility_bridge_sba', 'bed_mobility_bridge_s', 'bed_mobility_bridge_i', 'bed_mobility_supine2sit_unable', 'bed_mobility_supine2sit_max', 'bed_mobility_supine2sit_mod', 'bed_mobility_supine2sit_min', 'bed_mobility_supine2sit_cg', 'bed_mobility_supine2sit_sba', 'bed_mobility_supine2sit_s', 'bed_mobility_supine2sit_i', 'bed_mobility_sit2supine_unable', 'bed_mobility_sit2supine_max', 'bed_mobility_sit2supine_mod', 'bed_mobility_sit2supine_min', 'bed_mobility_sit2supine_cg', 'bed_mobility_sit2supine_sba', 'bed_mobility_sit2supine_s', 'bed_mobility_sit2supine_i', 'transfer_sit2stand_unable', 'transfer_sit2stand_max', 'transfer_sit2stand_mod', 'transfer_sit2stand_min', 'transfer_sit2stand_cg', 'transfer_sit2stand_sba', 'transfer_sit2stand_s', 'transfer_sit2stand_i', 'transfer_bed2chair_unable', 'transfer_bed2chair_max', 'transfer_bed2chair_mod', 'transfer_bed2chair_min', 'transfer_bed2chair_cg', 'transfer_bed2chair_sba', 'transfer_bed2chair_s', 'transfer_bed2chair_i', 'transfer_toilet_unable', 'transfer_toilet_max', 'transfer_toilet_mod', 'transfer_toilet_min', 'transfer_toilet_cg', 'transfer_toilet_sba', 'transfer_toilet_s', 'transfer_toilet_i', 'transfer_shower_unable', 'transfer_shower_max', 'transfer_shower_mod', 'transfer_shower_min', 'transfer_shower_cg', 'transfer_shower_sba', 'transfer_shower_s', 'transfer_shower_i', 'gait_even_surface_unable', 'gait_even_surface_max', 'gait_even_surface_mod', 'gait_even_surface_min', 'gait_even_surface_cg', 'gait_even_surface_sba', 'gait_even_surface_s', 'gait_even_surface_i', 'gait_uneven_surface_unable', 'gait_uneven_surface_max', 'gait_uneven_surface_mod', 'gait_uneven_surface_min', 'gait_uneven_surface_cg', 'gait_uneven_surface_sba', 'gait_uneven_surface_s', 'gait_uneven_surface_i', 'stairs_unable', 'stairs_max', 'stairs_mod', 'stairs_min', 'stairs_cg', 'stairs_sba', 'stairs_s', 'stairs_i', 'balance_sitting_static_p_minus', 'balance_sitting_static_p', 'balance_sitting_static_p_plus', 'balance_sitting_static_p_plus', 'balance_sitting_static_f_minus', 'balance_sitting_static_f', 'balance_sitting_static_f_plus', 'balance_sitting_static_g_minus', 'balance_sitting_static_g', 'balance_sitting_static_g_plus', 'balance_standing_static_p_minus', 'balance_standing_static_p', 'balance_standing_static_p_plus', 'balance_standing_static_f_minus', 'balance_standing_static_f', 'balance_standing_static_f_plus', 'balance_standing_static_g_minus', 'balance_standing_static_g', 'balance_standing_static_g_plus', 'balance_sitting_dynamic_p_minus', 'balance_sitting_dynamic_p', 'balance_sitting_dynamic_p_plus', 'balance_sitting_dynamic_f_minus', 'balance_sitting_dynamic_f', 'balance_sitting_dynamic_f_plus', 'balance_sitting_dynamic_g_minus', 'balance_sitting_dynamic_g', 'balance_sitting_dynamic_g_plus', 'balance_standing_dynamic_p_minus', 'balance_standing_dynamic_p', 'balance_standing_dynamic_p_plus', 'balance_standing_dynamic_f_minus', 'balance_standing_dynamic_f', 'balance_standing_dynamic_f_plus', 'balance_standing_dynamic_g_minus', 'balance_standing_dynamic_g', 'balance_standing_dynamic_g_plus', 'patient_is_uncooperative', 'patient_requires_encouragement', 'patient_is_cooperative', 'patient_response_today_poor', 'patient_response_today_fair', 'patient_response_today_good', 'patient_response_today_excellent', 'patient_progress_toward_goals_poor', 'patient_progress_toward_goals_fair', 'patient_progress_toward_goals_good', 'patient_progress_toward_goals_excellent', 'patient_activity_tolerance_poor', 'patient_activity_tolerance_fair', 'patient_activity_tolerance_good', 'patient_activity_tolerance_excellent', 'patient_safety_mechanics_poor', 'patient_safety_mechanics_fair', 'patient_safety_mechanics_good', 'patient_safety_mechanics_excellent', 'inadequate_safety_awareness', 'unable_ambulating_outdoors', 'difficulty_transfer', 'localized_weakness', 'inefficient_gait', 'bed_chair_bound', 'impaired_balance', 'requires_assistance_to_leave_home', 'profound_general_weakness', 'limited_rom', 'difficulty_manage_stairs', 'significant_effort_performing_tasks'], 'string', 'max' => 1],
            [['patient_name', 'subjective_pain_other', 'service_provided_other', 'pattern', 'precautions', 'therapeutic_exercises', 'assessment_comments', 'pt_plan_comments', 'therapist_name', 'therapist_name0'], 'string', 'max' => 255],
            [['mrn', 'dob', 'vital_signs_bp', 'vital_signs_pulse', 'vital_signs_resp', 'vital_signs_spo2', 'vital_signs_temp', 'subjective_co', 'subjective_pain_intensity', 'subjective_pain_location', 'subjective_pain_description', 'gait_even_surface_distance', 'gait_uneven_surface_distance', 'gait_device', 'stairs_steps', 'stairs_device', 'rom_ue', 'rom_le', 'rom_cervical', 'rom_trunk', 'rom_other', 'strength_ue', 'strength_le', 'strength_cervical', 'strength_trunk', 'strength_other', 'therapist_title', 'therapist_title0', 'note_date', 'time_in', 'time_out'], 'string', 'max' => 100],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['provider_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['provider_id' => 'id']],
            [['visit_id'], 'exist', 'skipOnError' => true, 'targetClass' => Visit::class, 'targetAttribute' => ['visit_id' => 'id']],
        ];

        if ($this->isRPTSignatureRequired(Yii::$app->user->id)){
            $rules[] = [['therapist_signature0'], 'required'];
        }

        if ($this->isPTASignatureRequired(Yii::$app->user->id)){
            $rules[] = [['therapist_signature'], 'required'];
        }

        return $rules;
    }

    /**
     * Caches and returns user by ID
     * @param int $id User ID
     * @return User|null
     */
    protected function _getUser(int $id):User
    {
        if (empty($this->_user) || $this->_user->id !== $id) {
            $this->_user = User::findOne($id);
        }
        return $this->_user;
    }

    /**
     * Checks if given RPT is required for RPT signature on the note
     * @param int $user_id User ID to be checked
     * @return bool
     */
    public function isRPTSignatureRequired(int $user_id):bool
    {
        $user = $this->_getUser($user_id);
        // If Active RPT on the order
        if (!empty($user) && $user->isActiveRPTOnOrder($this->order->id)) {
            return true;
        }
        return false;
    }

    /**
     * Checks if given RPT is required for RPT signature on the note
     * @param int $user_id User ID to be checked
     * @return bool
     */
    public function isPTASignatureRequired(int $user_id):bool
    {
        $user = $this->_getUser($user_id);
        // If Active PTA on the order and owner of the note or it is a new note but owner of visit
        if (!empty($user) && $user->isActivePTAOnOrder($this->order->id)) {

            // PTA is owner of visit
            if (!empty($this->visit->created_by) && ($this->visit->created_by === $user->id)) {
                return true;
            }
        }
        if (!empty($user) && $user->isActiveRPTOnOrder($this->order->id)) {

            // RPT is not owner of visit
            if (!empty($this->visit->created_by) && ($this->visit->created_by !== $user->id)){
                return true;
            }
        }
        return false;
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
            'dob' => Yii::t('app', 'DOB'),
            'gender' => Yii::t('app', 'Gender'),
            'service_provided_pt_evaluation' => Yii::t('app', 'PT Evaluation'),
            'service_provided_therapeutic_exercises' => Yii::t('app', 'Therapeutic Exercises'),
            'service_provided_therapeutic_activities' => Yii::t('app', 'Therapeutic Activities'),
            'service_provided_bed_mobility' => Yii::t('app', 'Bed mobility/Transfer training'),
            'service_provided_gait_training' => Yii::t('app', 'Gait Training'),
            'service_provided_muscle_reduction' => Yii::t('app', 'Muscle Reduction'),
            'service_provided_prosthetic_training' => Yii::t('app', 'Prosthetic/Orthotic training'),
            'service_provided_home_exercise_program' => Yii::t('app', 'Home Exercise Program'),
            'service_provided_ultrasound' => Yii::t('app', 'Ultrasound'),
            'service_provided_hot_pack' => Yii::t('app', 'Hot Pack'),
            'service_provided_cold_pack_message' => Yii::t('app', 'Cold Pack Message'),
            'service_provided_massage_mobilization' => Yii::t('app', 'Massage/Soft Tissue Mobilization'),
            'service_provided_joint_mobilization' => Yii::t('app', 'Joint Mobilization'),
            'service_provided_pt_cg_education' => Yii::t('app', 'PT CG Education'),
            'service_provided_other' => Yii::t('app', 'Other'),
            'vital_signs_bp' => Yii::t('app', 'Vital Sign BP'),
            'vital_signs_pulse' => Yii::t('app', 'Vital Sign Pulse'),
            'vital_signs_resp' => Yii::t('app', 'Vital Sign Resp'),
            'vital_signs_spo2' => Yii::t('app', 'Vital Sign SpO2'),
            'vital_signs_temp' => Yii::t('app', 'Vital Sign Temp'),
            'subjective_co' => Yii::t('app', 'Subjective Co'),
            'subjective_pain' => Yii::t('app', 'Pain'),
            'subjective_fatigue' => Yii::t('app', 'Fatigue'),
            'subjective_weakness' => Yii::t('app', 'Weakness'),
            'subjective_unsteady_gait' => Yii::t('app', 'Unsteady Gait'),
            'subjective_condition_improved' => Yii::t('app', 'Condition Improved'),
            'subjective_pain_intensity' => Yii::t('app', 'Pain Intensity'),
            'subjective_pain_location' => Yii::t('app', 'Site'),
            'subjective_pain_description' => Yii::t('app', 'Pain Description'),
            'subjective_pain_frequency_all_time' => Yii::t('app', 'All the time'),
            'subjective_pain_frequency_daily' => Yii::t('app', 'Daily but not constantly'),
            'subjective_pain_frequency_less_daily' => Yii::t('app', 'Less often than daily'),
            'subjective_pain_no_pain' => Yii::t('app', 'Patient has no pain'),
            'subjective_pain_other' => Yii::t('app', 'Other'),
            'bed_mobility_roll_unable' => Yii::t('app', 'Dependent'),
            'bed_mobility_roll_max' => Yii::t('app', 'Max'),
            'bed_mobility_roll_mod' => Yii::t('app', 'Mod'),
            'bed_mobility_roll_min' => Yii::t('app', 'Min'),
            'bed_mobility_roll_cg' => Yii::t('app', 'CG'),
            'bed_mobility_roll_sba' => Yii::t('app', 'SBA'),
            'bed_mobility_roll_s' => Yii::t('app', 'S'),
            'bed_mobility_roll_i' => Yii::t('app', 'I'),
            'bed_mobility_bridge_unable' => Yii::t('app', 'Dependent'),
            'bed_mobility_bridge_max' => Yii::t('app', 'Max'),
            'bed_mobility_bridge_mod' => Yii::t('app', 'Mod'),
            'bed_mobility_bridge_min' => Yii::t('app', 'Min'),
            'bed_mobility_bridge_cg' => Yii::t('app', 'CG'),
            'bed_mobility_bridge_sba' => Yii::t('app', 'SBA'),
            'bed_mobility_bridge_s' => Yii::t('app', 'S'),
            'bed_mobility_bridge_i' => Yii::t('app', 'I'),
            'bed_mobility_supine2sit_unable' => Yii::t('app', 'Dependent'),
            'bed_mobility_supine2sit_max' => Yii::t('app', 'Max'),
            'bed_mobility_supine2sit_mod' => Yii::t('app', 'Mod'),
            'bed_mobility_supine2sit_min' => Yii::t('app', 'Min'),
            'bed_mobility_supine2sit_cg' => Yii::t('app', 'CG'),
            'bed_mobility_supine2sit_sba' => Yii::t('app', 'SBA'),
            'bed_mobility_supine2sit_s' => Yii::t('app', 'S'),
            'bed_mobility_supine2sit_i' => Yii::t('app', 'I'),
            'bed_mobility_sit2supine_unable' => Yii::t('app', 'Dependent'),
            'bed_mobility_sit2supine_max' => Yii::t('app', 'Max'),
            'bed_mobility_sit2supine_mod' => Yii::t('app', 'Mod'),
            'bed_mobility_sit2supine_min' => Yii::t('app', 'Min'),
            'bed_mobility_sit2supine_cg' => Yii::t('app', 'CG'),
            'bed_mobility_sit2supine_sba' => Yii::t('app', 'SBA'),
            'bed_mobility_sit2supine_s' => Yii::t('app', 'S'),
            'bed_mobility_sit2supine_i' => Yii::t('app', 'I'),
            'transfer_sit2stand_unable' => Yii::t('app', 'Dependent'),
            'transfer_sit2stand_max' => Yii::t('app', 'Max'),
            'transfer_sit2stand_mod' => Yii::t('app', 'Mod'),
            'transfer_sit2stand_min' => Yii::t('app', 'Min'),
            'transfer_sit2stand_cg' => Yii::t('app', 'CG'),
            'transfer_sit2stand_sba' => Yii::t('app', 'SBA'),
            'transfer_sit2stand_s' => Yii::t('app', 'S'),
            'transfer_sit2stand_i' => Yii::t('app', 'I'),
            'transfer_bed2chair_unable' => Yii::t('app', 'Dependent'),
            'transfer_bed2chair_max' => Yii::t('app', 'Max'),
            'transfer_bed2chair_mod' => Yii::t('app', 'Mod'),
            'transfer_bed2chair_min' => Yii::t('app', 'Min'),
            'transfer_bed2chair_cg' => Yii::t('app', 'CG'),
            'transfer_bed2chair_sba' => Yii::t('app', 'SBA'),
            'transfer_bed2chair_s' => Yii::t('app', 'S'),
            'transfer_bed2chair_i' => Yii::t('app', 'I'),
            'transfer_toilet_unable' => Yii::t('app', 'Dependent'),
            'transfer_toilet_max' => Yii::t('app', 'Max'),
            'transfer_toilet_mod' => Yii::t('app', 'Mod'),
            'transfer_toilet_min' => Yii::t('app', 'Min'),
            'transfer_toilet_cg' => Yii::t('app', 'CG'),
            'transfer_toilet_sba' => Yii::t('app', 'SBA'),
            'transfer_toilet_s' => Yii::t('app', 'S'),
            'transfer_toilet_i' => Yii::t('app', 'I'),
            'transfer_shower_unable' => Yii::t('app', 'Dependent'),
            'transfer_shower_max' => Yii::t('app', 'Max'),
            'transfer_shower_mod' => Yii::t('app', 'Mod'),
            'transfer_shower_min' => Yii::t('app', 'Min'),
            'transfer_shower_cg' => Yii::t('app', 'CG'),
            'transfer_shower_sba' => Yii::t('app', 'SBA'),
            'transfer_shower_s' => Yii::t('app', 'S'),
            'transfer_shower_i' => Yii::t('app', 'I'),
            'gait_even_surface_distance' => Yii::t('app', 'Even Surface Distance'),
            'gait_even_surface_unable' => Yii::t('app', 'Dependent'),
            'gait_even_surface_max' => Yii::t('app', 'Max'),
            'gait_even_surface_mod' => Yii::t('app', 'Mod'),
            'gait_even_surface_min' => Yii::t('app', 'Min'),
            'gait_even_surface_cg' => Yii::t('app', 'CG'),
            'gait_even_surface_sba' => Yii::t('app', 'SBA'),
            'gait_even_surface_s' => Yii::t('app', 'S'),
            'gait_even_surface_i' => Yii::t('app', 'I'),
            'gait_uneven_surface_distance' => Yii::t('app', 'Uneven Surface Distance'),
            'gait_uneven_surface_unable' => Yii::t('app', 'Dependent'),
            'gait_uneven_surface_max' => Yii::t('app', 'Max'),
            'gait_uneven_surface_mod' => Yii::t('app', 'Mod'),
            'gait_uneven_surface_min' => Yii::t('app', 'Min'),
            'gait_uneven_surface_cg' => Yii::t('app', 'CG'),
            'gait_uneven_surface_sba' => Yii::t('app', 'SBA'),
            'gait_uneven_surface_s' => Yii::t('app', 'S'),
            'gait_uneven_surface_i' => Yii::t('app', 'I'),
            'gait_device' => Yii::t('app', 'Gait Device'),
            'stairs_unable' => Yii::t('app', 'Dependent'),
            'stairs_max' => Yii::t('app', 'Max'),
            'stairs_mod' => Yii::t('app', 'Mod'),
            'stairs_min' => Yii::t('app', 'Min'),
            'stairs_cg' => Yii::t('app', 'CG'),
            'stairs_sba' => Yii::t('app', 'SBA'),
            'stairs_s' => Yii::t('app', 'S'),
            'stairs_i' => Yii::t('app', 'I'),
            'stairs_steps' => Yii::t('app', 'Stairs Steps'),
            'stairs_device' => Yii::t('app', 'Stairs Device'),
            'pattern' => Yii::t('app', 'Pattern'),
            'precautions' => Yii::t('app', 'Precautions'),
            'balance_sitting_static_p_minus' => Yii::t('app', 'P-'),
            'balance_sitting_static_p' => Yii::t('app', 'P'),
            'balance_sitting_static_p_plus' => Yii::t('app', 'P+'),
            'balance_sitting_static_f_minus' => Yii::t('app', 'F-'),
            'balance_sitting_static_f' => Yii::t('app', 'F+'),
            'balance_sitting_static_f_plus' => Yii::t('app', 'F+'),
            'balance_sitting_static_g_minus' => Yii::t('app', 'G-'),
            'balance_sitting_static_g' => Yii::t('app', 'G'),
            'balance_sitting_static_g_plus' => Yii::t('app', 'G+'),
            'balance_standing_static_p_minus' => Yii::t('app', 'P-'),
            'balance_standing_static_p' => Yii::t('app', 'P'),
            'balance_standing_static_p_plus' => Yii::t('app', 'P+'),
            'balance_standing_static_f_minus' => Yii::t('app', 'F-'),
            'balance_standing_static_f' => Yii::t('app', 'F'),
            'balance_standing_static_f_plus' => Yii::t('app', 'F+'),
            'balance_standing_static_g_minus' => Yii::t('app', 'G-'),
            'balance_standing_static_g' => Yii::t('app', 'G+'),
            'balance_standing_static_g_plus' => Yii::t('app', 'G+'),
            'balance_sitting_dynamic_p_minus' => Yii::t('app', 'P-'),
            'balance_sitting_dynamic_p' => Yii::t('app', 'P'),
            'balance_sitting_dynamic_p_plus' => Yii::t('app', 'P+'),
            'balance_sitting_dynamic_f_minus' => Yii::t('app', 'F-'),
            'balance_sitting_dynamic_f' => Yii::t('app', 'F'),
            'balance_sitting_dynamic_f_plus' => Yii::t('app', 'F+'),
            'balance_sitting_dynamic_g_minus' => Yii::t('app', 'G-'),
            'balance_sitting_dynamic_g' => Yii::t('app', 'G'),
            'balance_sitting_dynamic_g_plus' => Yii::t('app', 'G+'),
            'balance_standing_dynamic_p_minus' => Yii::t('app', 'P-'),
            'balance_standing_dynamic_p' => Yii::t('app', 'P'),
            'balance_standing_dynamic_p_plus' => Yii::t('app', 'P+'),
            'balance_standing_dynamic_f_minus' => Yii::t('app', 'F-'),
            'balance_standing_dynamic_f' => Yii::t('app', 'F'),
            'balance_standing_dynamic_f_plus' => Yii::t('app', 'F+'),
            'balance_standing_dynamic_g_minus' => Yii::t('app', 'G-'),
            'balance_standing_dynamic_g' => Yii::t('app', 'G'),
            'balance_standing_dynamic_g_plus' => Yii::t('app', 'G+'),
            'therapeutic_exercises' => Yii::t('app', 'Therapeutic Exercises / Activities Performed'),
            'patient_is_uncooperative' => Yii::t('app', 'Patient Is Uncooperative'),
            'patient_requires_encouragement' => Yii::t('app', 'Patient Requires Encouragement'),
            'patient_is_cooperative' => Yii::t('app', 'Patient Is Cooperative'),
            'patient_response_today_poor' => Yii::t('app', 'Poor'),
            'patient_response_today_fair' => Yii::t('app', 'Fair'),
            'patient_response_today_good' => Yii::t('app', 'Good'),
            'patient_response_today_excellent' => Yii::t('app', 'Excellent'),
            'patient_progress_toward_goals_poor' => Yii::t('app', 'Poor'),
            'patient_progress_toward_goals_fair' => Yii::t('app', 'Fair'),
            'patient_progress_toward_goals_good' => Yii::t('app', 'Good'),
            'patient_progress_toward_goals_excellent' => Yii::t('app', 'Excellent'),
            'patient_activity_tolerance_poor' => Yii::t('app', 'Poor'),
            'patient_activity_tolerance_fair' => Yii::t('app', 'Fair'),
            'patient_activity_tolerance_good' => Yii::t('app', 'Good'),
            'patient_activity_tolerance_excellent' => Yii::t('app', 'Excellent'),
            'patient_safety_mechanics_poor' => Yii::t('app', 'Poor'),
            'patient_safety_mechanics_fair' => Yii::t('app', 'Fair'),
            'patient_safety_mechanics_good' => Yii::t('app', 'Good'),
            'patient_safety_mechanics_excellent' => Yii::t('app', 'Excellent'),
            'assessment_comments' => Yii::t('app', 'Assessment Comments'),
            'rom_ue' => Yii::t('app', 'Rom Ue'),
            'rom_le' => Yii::t('app', 'Rom Le'),
            'rom_cervical' => Yii::t('app', 'Rom Cervical'),
            'rom_trunk' => Yii::t('app', 'Rom Trunk'),
            'rom_other' => Yii::t('app', 'Rom Other'),
            'strength_ue' => Yii::t('app', 'Strength Ue'),
            'strength_le' => Yii::t('app', 'Strength Le'),
            'strength_cervical' => Yii::t('app', 'Strength Cervical'),
            'strength_trunk' => Yii::t('app', 'Strength Trunk'),
            'strength_other' => Yii::t('app', 'Strength Other'),
            'inadequate_safety_awareness' => Yii::t('app', 'Inadequate Safety Awareness'),
            'unable_ambulating_outdoors' => Yii::t('app', 'Unable Ambulating Outdoors'),
            'difficulty_transfer' => Yii::t('app', 'Difficulty Transfer'),
            'localized_weakness' => Yii::t('app', 'Localized Weakness'),
            'inefficient_gait' => Yii::t('app', 'Inefficient Gait'),
            'bed_chair_bound' => Yii::t('app', 'Bed Chair Bound'),
            'impaired_balance' => Yii::t('app', 'Impaired Balance'),
            'requires_assistance_to_leave_home' => Yii::t('app', 'Requires Assistance To Leave Home'),
            'profound_general_weakness' => Yii::t('app', 'Profound General Weakness'),
            'limited_rom' => Yii::t('app', 'Limited Rom'),
            'difficulty_manage_stairs' => Yii::t('app', 'Difficulty Manage Stairs'),
            'significant_effort_performing_tasks' => Yii::t('app', 'Significant Effort Performing Tasks'),
            'pt_plan_comments' => Yii::t('app', 'PT Plan Comments'),
            'therapist_name0' => Yii::t('app', 'RPT Name'),
            'therapist_title0' => Yii::t('app', 'RPT Title'),
            'therapist_signature0' => Yii::t('app', 'RPT Signature'),
            'therapist_name' => Yii::t('app', 'PTA Name'),
            'therapist_title' => Yii::t('app', 'PTA Title'),
            'therapist_signature' => Yii::t('app', 'PTA Signature'),
            'note_date' => Yii::t('app', 'Note Date'),
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
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('progressNotes');
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
        return $this->hasOne(Visit::class, ['id' => 'visit_id', 'order_id' => 'order_id'])->inverseOf('progressNote');
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
     * @return NoteProgressQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoteProgressQuery(get_called_class());
    }
}
