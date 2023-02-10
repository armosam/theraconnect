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
use common\models\queries\UserQuery;
use common\models\queries\VisitQuery;
use common\models\queries\OrderQuery;
use common\models\queries\NoteEvalQuery;

/**
 * This is the model class for table "{{%note_eval}}".
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
 * @property string|null $dob
 * @property string|null $gender
 * @property string|null $mrn
 * @property string|null $initial
 * @property string|null $recertification
 * @property string|null $soc
 * @property string|null $diagnosis
 * @property string|null $prior_level_independent
 * @property string|null $prior_level_min_assist
 * @property string|null $prior_level_mod_assist
 * @property string|null $prior_level_max_assist
 * @property string|null $prior_level_wc_bound
 * @property string|null $prior_level_bed_bound
 * @property string|null $living_situation_apartment
 * @property string|null $living_situation_house
 * @property string|null $living_situation_facility
 * @property string|null $living_situation_bc
 * @property string|null $living_situation_mobile
 * @property string|null $living_situation_alone
 * @property string|null $living_situation_with_family
 * @property string|null $living_situation_cg
 * @property string|null $living_situation_cg_name
 * @property string|null $living_situation_stairs
 * @property string|null $living_situation_stairs_count
 * @property string|null $living_situation_elevator
 * @property string|null $living_situation_no_stairs
 * @property string|null $vital_signs_bp
 * @property string|null $vital_signs_pulse
 * @property string|null $vital_signs_resp
 * @property string|null $vital_signs_spo2
 * @property string|null $vital_signs_temp
 * @property string|null $frequency
 * @property string|null $problems
 * @property string|null $goals
 * @property string|null $estimate_completion_date
 * @property string|null $rehab_potential_excellent
 * @property string|null $rehab_potential_good
 * @property string|null $rehab_potential_fair
 * @property string|null $rehab_potential_poor
 * @property string|null $plan_care_evaluation
 * @property string|null $plan_care_therapeutic_exercise
 * @property string|null $plan_care_therapeutic_activities
 * @property string|null $plan_care_transfer_training
 * @property string|null $plan_care_nero_muscular
 * @property string|null $plan_care_upgrade_home_program
 * @property string|null $plan_care_gait_training
 * @property string|null $plan_care_wb_status
 * @property string|null $discharge_plan_safety_education
 * @property string|null $discharge_plan_electrotherapy
 * @property string|null $discharge_plan_ultrasound
 * @property string|null $discharge_plan_prosthetic_training
 * @property string|null $discharge_plan_fabrication_orthotic_device
 * @property string|null $discharge_plan_muscle_reduction
 * @property string|null $discharge_plan_manage_eval_care_plan
 * @property string|null $discharge_plan_pain_management
 * @property string|null $discharge_plan_massage
 * @property string|null $discharge_plan_stair_training
 * @property string|null $reason_for_skilled_service
 * @property string|null $bed_mobility_roll_unable
 * @property string|null $bed_mobility_roll_max
 * @property string|null $bed_mobility_roll_mod
 * @property string|null $bed_mobility_roll_min
 * @property string|null $bed_mobility_roll_cg
 * @property string|null $bed_mobility_roll_sba
 * @property string|null $bed_mobility_roll_s
 * @property string|null $bed_mobility_roll_i
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
 * @property string|null $transfer_bed_chair_unable
 * @property string|null $transfer_bed_chair_max
 * @property string|null $transfer_bed_chair_mod
 * @property string|null $transfer_bed_chair_min
 * @property string|null $transfer_bed_chair_cg
 * @property string|null $transfer_bed_chair_sba
 * @property string|null $transfer_bed_chair_s
 * @property string|null $transfer_bed_chair_i
 * @property string|null $transfer_toilet_unable
 * @property string|null $transfer_toilet_max
 * @property string|null $transfer_toilet_mod
 * @property string|null $transfer_toilet_min
 * @property string|null $transfer_toilet_cg
 * @property string|null $transfer_toilet_sba
 * @property string|null $transfer_toilet_s
 * @property string|null $transfer_toilet_i
 * @property string|null $adl_dressing_unable
 * @property string|null $adl_dressing_max
 * @property string|null $adl_dressing_mod
 * @property string|null $adl_dressing_min
 * @property string|null $adl_dressing_cg
 * @property string|null $adl_dressing_sba
 * @property string|null $adl_dressing_s
 * @property string|null $adl_dressing_i
 * @property string|null $adl_personal_hygiene_unable
 * @property string|null $adl_personal_hygiene_max
 * @property string|null $adl_personal_hygiene_mod
 * @property string|null $adl_personal_hygiene_min
 * @property string|null $adl_personal_hygiene_cg
 * @property string|null $adl_personal_hygiene_sba
 * @property string|null $adl_personal_hygiene_s
 * @property string|null $adl_personal_hygiene_i
 * @property string|null $adl_shower_unable
 * @property string|null $adl_shower_max
 * @property string|null $adl_shower_mod
 * @property string|null $adl_shower_min
 * @property string|null $adl_shower_cg
 * @property string|null $adl_shower_sba
 * @property string|null $adl_shower_s
 * @property string|null $adl_shower_i
 * @property string|null $adl_feeding_unable
 * @property string|null $adl_feeding_max
 * @property string|null $adl_feeding_mod
 * @property string|null $adl_feeding_min
 * @property string|null $adl_feeding_cg
 * @property string|null $adl_feeding_sba
 * @property string|null $adl_feeding_s
 * @property string|null $adl_feeding_i
 * @property string|null $adl_meal_prep_unable
 * @property string|null $adl_meal_prep_max
 * @property string|null $adl_meal_prep_mod
 * @property string|null $adl_meal_prep_min
 * @property string|null $adl_meal_prep_cg
 * @property string|null $adl_meal_prep_sba
 * @property string|null $adl_meal_prep_s
 * @property string|null $adl_meal_prep_i
 * @property string|null $adl_home_making_unable
 * @property string|null $adl_home_making_max
 * @property string|null $adl_home_making_mod
 * @property string|null $adl_home_making_min
 * @property string|null $adl_home_making_cg
 * @property string|null $adl_home_making_sba
 * @property string|null $adl_home_making_s
 * @property string|null $adl_home_making_i
 * @property string|null $adl_car_unable
 * @property string|null $adl_car_max
 * @property string|null $adl_car_mod
 * @property string|null $adl_car_min
 * @property string|null $adl_car_cg
 * @property string|null $adl_car_sba
 * @property string|null $adl_car_s
 * @property string|null $adl_car_i
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
 * @property string|null $pain_0
 * @property string|null $pain_1
 * @property string|null $pain_2
 * @property string|null $pain_3
 * @property string|null $pain_4
 * @property string|null $pain_5
 * @property string|null $pain_6
 * @property string|null $pain_7
 * @property string|null $pain_8
 * @property string|null $pain_9
 * @property string|null $pain_location
 * @property string|null $edema
 * @property string|null $sensory
 * @property string|null $strength_ue
 * @property string|null $rom_ue
 * @property string|null $normal_ue
 * @property string|null $strength_le
 * @property string|null $rom_le
 * @property string|null $normal_le
 * @property string|null $cog_communication
 * @property string|null $activity_tolerance_poor
 * @property string|null $activity_tolerance_fair
 * @property string|null $activity_tolerance_good
 * @property string|null $activity_tolerance_excellent
 * @property string|null $endurance_poor
 * @property string|null $endurance_fair
 * @property string|null $endurance_good
 * @property string|null $endurance_excellent
 * @property string|null $posture_poor
 * @property string|null $posture_fair
 * @property string|null $posture_good
 * @property string|null $posture_excellent
 * @property string|null $safety_awareness_poor
 * @property string|null $safety_awareness_fair
 * @property string|null $safety_awareness_good
 * @property string|null $safety_awareness_excellent
 * @property string|null $gait_even_surface
 * @property string|null $gait_uneven_surface
 * @property string|null $gait_distance
 * @property string|null $gait_device
 * @property string|null $gait_stairs
 * @property string|null $precautions
 * @property string|null $pattern
 * @property string|null $patient_has_spc
 * @property string|null $patient_has_wc
 * @property string|null $patient_has_qc
 * @property string|null $patient_has_crutches
 * @property string|null $patient_has_toilet_seat
 * @property string|null $patient_has_shower_chair
 * @property string|null $patient_has_fww
 * @property string|null $patient_has_commode
 * @property string|null $patient_has_hw
 * @property string|null $patient_has_hospital_bed
 * @property string|null $patient_has_hoyer_lift
 * @property string|null $patient_has_other
 * @property string|null $recommended_equipment_spc
 * @property string|null $recommended_equipment_wc
 * @property string|null $recommended_equipment_qc
 * @property string|null $recommended_equipment_crutches
 * @property string|null $recommended_equipment_toilet_seat
 * @property string|null $recommended_equipment_shower_chair
 * @property string|null $recommended_equipment_fww
 * @property string|null $recommended_equipment_commode
 * @property string|null $recommended_equipment_hw
 * @property string|null $recommended_equipment_hospital_bed
 * @property string|null $recommended_equipment_hoyer_lift
 * @property string|null $recommended_equipment_other
 * @property string|null $functional_limitation_amputation
 * @property string|null $functional_limitation_bowel
 * @property string|null $functional_limitation_contractures
 * @property string|null $functional_limitation_hearing
 * @property string|null $functional_limitation_poor_vision
 * @property string|null $functional_limitation_paralysis
 * @property string|null $functional_limitation_endurance
 * @property string|null $functional_limitation_ambulation
 * @property string|null $functional_limitation_speech
 * @property string|null $functional_limitation_legally_blind
 * @property string|null $functional_limitation_dyspnea
 * @property string|null $functional_limitation_impaired_vision
 * @property string|null $functional_limitation_other
 * @property string|null $knowledge_patient
 * @property string|null $knowledge_pcg
 * @property string|null $body_mech_poor
 * @property string|null $body_mech_fair
 * @property string|null $body_mech_good
 * @property string|null $home_exercise_program_poor
 * @property string|null $home_exercise_program_fair
 * @property string|null $home_exercise_program_good
 * @property string|null $home_safety_program_poor
 * @property string|null $home_safety_program_fair
 * @property string|null $home_safety_program_good
 * @property string|null $activities_permitted_complete_bedrest
 * @property string|null $activities_permitted_bedrest_brp
 * @property string|null $activities_permitted_up_tolerated
 * @property string|null $activities_permitted_transfer_bed
 * @property string|null $activities_permitted_exercise_prescribed
 * @property string|null $activities_permitted_pwb
 * @property string|null $activities_permitted_crutches
 * @property string|null $activities_permitted_walker
 * @property string|null $activities_permitted_cane
 * @property string|null $activities_permitted_wheelchair
 * @property string|null $activities_permitted_amb
 * @property string|null $activities_permitted_other
 * @property string|null $mental_oriented
 * @property string|null $mental_forgetful
 * @property string|null $mental_comatose
 * @property string|null $mental_disoriented
 * @property string|null $mental_agitated
 * @property string|null $mental_depressed
 * @property string|null $mental_lethargic
 * @property string|null $mental_other
 * @property string|null $comments
 * @property string|null $therapist_name
 * @property string|null $therapist_title
 * @property string|null $therapist_signature
 * @property string|null $note_date
 * @property string|null $time_in
 * @property string|null $time_out
 * @property string|null $physician_name
 * @property string|null $physician_phone_number
 * @property string|null $physician_signature
 *
 * @property User $provider
 * @property Order $order
 * @property Visit $visit
 * @property User $createdBy
 * @property User $updatedBy
 */
class NoteEval extends ActiveRecord
{
    public $save_signature;
    public $submit;
    public $name = 'Eval Note';
    public $docRoute = 'document/note-eval';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%note_eval}}';
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
            [['status', 'submit', 'save_signature', 'gender', 'initial', 'recertification', 'prior_level_independent', 'prior_level_min_assist', 'prior_level_mod_assist', 'prior_level_max_assist', 'prior_level_wc_bound', 'prior_level_bed_bound', 'living_situation_apartment', 'living_situation_house', 'living_situation_facility', 'living_situation_bc', 'living_situation_mobile', 'living_situation_alone', 'living_situation_with_family', 'living_situation_cg', 'living_situation_stairs', 'living_situation_elevator', 'living_situation_no_stairs', 'rehab_potential_excellent', 'rehab_potential_good', 'rehab_potential_fair', 'rehab_potential_poor', 'plan_care_evaluation', 'plan_care_therapeutic_exercise', 'plan_care_therapeutic_activities', 'plan_care_transfer_training', 'plan_care_nero_muscular', 'plan_care_upgrade_home_program', 'plan_care_gait_training', 'discharge_plan_safety_education', 'discharge_plan_electrotherapy', 'discharge_plan_ultrasound', 'discharge_plan_prosthetic_training', 'discharge_plan_fabrication_orthotic_device', 'discharge_plan_muscle_reduction', 'discharge_plan_manage_eval_care_plan', 'discharge_plan_pain_management', 'discharge_plan_massage', 'discharge_plan_stair_training', 'bed_mobility_roll_unable', 'bed_mobility_roll_max', 'bed_mobility_roll_mod', 'bed_mobility_roll_min', 'bed_mobility_roll_cg', 'bed_mobility_roll_sba', 'bed_mobility_roll_s', 'bed_mobility_roll_i', 'bed_mobility_supine2sit_unable', 'bed_mobility_supine2sit_max', 'bed_mobility_supine2sit_mod', 'bed_mobility_supine2sit_min', 'bed_mobility_supine2sit_cg', 'bed_mobility_supine2sit_sba', 'bed_mobility_supine2sit_s', 'bed_mobility_supine2sit_i', 'bed_mobility_sit2supine_unable', 'bed_mobility_sit2supine_max', 'bed_mobility_sit2supine_mod', 'bed_mobility_sit2supine_min', 'bed_mobility_sit2supine_cg', 'bed_mobility_sit2supine_sba', 'bed_mobility_sit2supine_s', 'bed_mobility_sit2supine_i', 'transfer_sit2stand_unable', 'transfer_sit2stand_max', 'transfer_sit2stand_mod', 'transfer_sit2stand_min', 'transfer_sit2stand_cg', 'transfer_sit2stand_sba', 'transfer_sit2stand_s', 'transfer_sit2stand_i', 'transfer_bed_chair_unable', 'transfer_bed_chair_max', 'transfer_bed_chair_mod', 'transfer_bed_chair_min', 'transfer_bed_chair_cg', 'transfer_bed_chair_sba', 'transfer_bed_chair_s', 'transfer_bed_chair_i', 'transfer_toilet_unable', 'transfer_toilet_max', 'transfer_toilet_mod', 'transfer_toilet_min', 'transfer_toilet_cg', 'transfer_toilet_sba', 'transfer_toilet_s', 'transfer_toilet_i', 'adl_dressing_unable', 'adl_dressing_max', 'adl_dressing_mod', 'adl_dressing_min', 'adl_dressing_cg', 'adl_dressing_sba', 'adl_dressing_s', 'adl_dressing_i', 'adl_personal_hygiene_unable', 'adl_personal_hygiene_max', 'adl_personal_hygiene_mod', 'adl_personal_hygiene_min', 'adl_personal_hygiene_cg', 'adl_personal_hygiene_sba', 'adl_personal_hygiene_s', 'adl_personal_hygiene_i', 'adl_shower_unable', 'adl_shower_max', 'adl_shower_mod', 'adl_shower_min', 'adl_shower_cg', 'adl_shower_sba', 'adl_shower_s', 'adl_shower_i', 'adl_feeding_unable', 'adl_feeding_max', 'adl_feeding_mod', 'adl_feeding_min', 'adl_feeding_cg', 'adl_feeding_sba', 'adl_feeding_s', 'adl_feeding_i', 'adl_meal_prep_unable', 'adl_meal_prep_max', 'adl_meal_prep_mod', 'adl_meal_prep_min', 'adl_meal_prep_cg', 'adl_meal_prep_sba', 'adl_meal_prep_s', 'adl_meal_prep_i', 'adl_home_making_unable', 'adl_home_making_max', 'adl_home_making_mod', 'adl_home_making_min', 'adl_home_making_cg', 'adl_home_making_sba', 'adl_home_making_s', 'adl_home_making_i', 'adl_car_unable', 'adl_car_max', 'adl_car_mod', 'adl_car_min', 'adl_car_cg', 'adl_car_sba', 'adl_car_s', 'adl_car_i', 'balance_sitting_static_p_minus', 'balance_sitting_static_p', 'balance_sitting_static_p_plus', 'balance_sitting_static_f_minus', 'balance_sitting_static_f', 'balance_sitting_static_f_plus', 'balance_sitting_static_g_minus', 'balance_sitting_static_g', 'balance_sitting_static_g_plus', 'balance_standing_static_p_minus', 'balance_standing_static_p', 'balance_standing_static_p_plus', 'balance_standing_static_f_minus', 'balance_standing_static_f', 'balance_standing_static_f_plus', 'balance_standing_static_g_minus', 'balance_standing_static_g', 'balance_standing_static_g_plus', 'balance_sitting_dynamic_p_minus', 'balance_sitting_dynamic_p', 'balance_sitting_dynamic_p_plus', 'balance_sitting_dynamic_f_minus', 'balance_sitting_dynamic_f', 'balance_sitting_dynamic_f_plus', 'balance_sitting_dynamic_g_minus', 'balance_sitting_dynamic_g', 'balance_sitting_dynamic_g_plus', 'balance_standing_dynamic_p_minus', 'balance_standing_dynamic_p', 'balance_standing_dynamic_p_plus', 'balance_standing_dynamic_f_minus', 'balance_standing_dynamic_f', 'balance_standing_dynamic_f_plus', 'balance_standing_dynamic_g_minus', 'balance_standing_dynamic_g', 'balance_standing_dynamic_g_plus', 'pain_0', 'pain_1', 'pain_2', 'pain_3', 'pain_4', 'pain_5', 'pain_6', 'pain_7', 'pain_8', 'pain_9', 'sensory', 'activity_tolerance_poor', 'activity_tolerance_fair', 'activity_tolerance_good', 'activity_tolerance_excellent', 'endurance_poor', 'endurance_fair', 'endurance_good', 'endurance_excellent', 'posture_poor', 'posture_fair', 'posture_good', 'posture_excellent', 'safety_awareness_poor', 'safety_awareness_fair', 'safety_awareness_good', 'safety_awareness_excellent', 'patient_has_spc', 'patient_has_wc', 'patient_has_qc', 'patient_has_crutches', 'patient_has_toilet_seat', 'patient_has_shower_chair', 'patient_has_fww', 'patient_has_commode', 'patient_has_hw', 'patient_has_hospital_bed', 'patient_has_hoyer_lift', 'patient_has_other', 'recommended_equipment_spc', 'recommended_equipment_wc', 'recommended_equipment_qc', 'recommended_equipment_crutches', 'recommended_equipment_toilet_seat', 'recommended_equipment_shower_chair', 'recommended_equipment_fww', 'recommended_equipment_commode', 'recommended_equipment_hw', 'recommended_equipment_hospital_bed', 'recommended_equipment_hoyer_lift', 'recommended_equipment_other', 'functional_limitation_amputation', 'functional_limitation_bowel', 'functional_limitation_contractures', 'functional_limitation_hearing', 'functional_limitation_poor_vision', 'functional_limitation_paralysis', 'functional_limitation_endurance', 'functional_limitation_ambulation', 'functional_limitation_speech', 'functional_limitation_legally_blind', 'functional_limitation_dyspnea', 'functional_limitation_impaired_vision', 'functional_limitation_other', 'knowledge_patient', 'knowledge_pcg', 'body_mech_poor', 'body_mech_fair', 'body_mech_good', 'home_exercise_program_poor', 'home_exercise_program_fair', 'home_exercise_program_good', 'home_safety_program_poor', 'home_safety_program_fair', 'home_safety_program_good', 'activities_permitted_complete_bedrest', 'activities_permitted_bedrest_brp', 'activities_permitted_up_tolerated', 'activities_permitted_transfer_bed', 'activities_permitted_exercise_prescribed', 'activities_permitted_pwb', 'activities_permitted_crutches', 'activities_permitted_walker', 'activities_permitted_cane', 'activities_permitted_wheelchair', 'activities_permitted_amb', 'activities_permitted_other', 'mental_oriented', 'mental_forgetful', 'mental_comatose', 'mental_disoriented', 'mental_agitated', 'mental_depressed', 'mental_lethargic', 'mental_other'], 'string', 'max' => 1],
            [['patient_name', 'diagnosis', 'living_situation_cg_name', 'frequency', 'problems', 'goals', 'reason_for_skilled_service', 'cog_communication', 'pattern', 'comments', 'therapist_name', 'physician_name', 'physician_signature'], 'string', 'max' => 255],
            [['mrn', 'dob', 'soc', 'living_situation_stairs_count', 'vital_signs_bp', 'vital_signs_pulse', 'vital_signs_resp', 'vital_signs_spo2', 'vital_signs_temp', 'estimate_completion_date', 'plan_care_wb_status', 'pain_location', 'edema', 'strength_ue', 'rom_ue', 'normal_ue', 'strength_le', 'rom_le', 'normal_le', 'gait_even_surface', 'gait_uneven_surface', 'gait_distance', 'gait_device', 'gait_stairs', 'precautions', 'therapist_title', 'physician_phone_number', 'note_date', 'time_in', 'time_out'], 'string', 'max' => 100],
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
            'initial' => Yii::t('app', 'Initial'),
            'recertification' => Yii::t('app', 'Recert'),
            'soc' => Yii::t('app', 'SOC'),
            'diagnosis' => Yii::t('app', 'Diagnosis'),
            'prior_level_independent' => Yii::t('app', 'Independent'),
            'prior_level_min_assist' => Yii::t('app', 'Min Assist'),
            'prior_level_mod_assist' => Yii::t('app', 'Mod Assist'),
            'prior_level_max_assist' => Yii::t('app', 'Max Assist'),
            'prior_level_wc_bound' => Yii::t('app', 'W/C Bound'),
            'prior_level_bed_bound' => Yii::t('app', 'Bed Bound'),
            'living_situation_apartment' => Yii::t('app', 'Apartment'),
            'living_situation_house' => Yii::t('app', 'House'),
            'living_situation_facility' => Yii::t('app', 'Facility'),
            'living_situation_bc' => Yii::t('app', 'B&C'),
            'living_situation_mobile' => Yii::t('app', 'Mobile'),
            'living_situation_alone' => Yii::t('app', 'Alone'),
            'living_situation_with_family' => Yii::t('app', 'With Family'),
            'living_situation_cg' => Yii::t('app', 'CG'),
            'living_situation_cg_name' => Yii::t('app', 'CG Name'),
            'living_situation_stairs' => Yii::t('app', 'Stairs'),
            'living_situation_stairs_count' => Yii::t('app', 'Stairs Count'),
            'living_situation_elevator' => Yii::t('app', 'Elevator'),
            'living_situation_no_stairs' => Yii::t('app', 'No Stairs'),
            'vital_signs_bp' => Yii::t('app', 'BP'),
            'vital_signs_pulse' => Yii::t('app', 'Pulse'),
            'vital_signs_resp' => Yii::t('app', 'Resp'),
            'vital_signs_spo2' => Yii::t('app', 'SpO2'),
            'vital_signs_temp' => Yii::t('app', 'Temp'),
            'frequency' => Yii::t('app', 'Frequency'),
            'dob' => Yii::t('app', 'DOB'),
            'gender' => Yii::t('app', 'Gender'),
            'problems' => Yii::t('app', 'Problems'),
            'goals' => Yii::t('app', 'Goals'),
            'estimate_completion_date' => Yii::t('app', 'Estimate Completion Date'),
            'rehab_potential_excellent' => Yii::t('app', 'Excellent'),
            'rehab_potential_good' => Yii::t('app', 'Good'),
            'rehab_potential_fair' => Yii::t('app', 'Fair'),
            'rehab_potential_poor' => Yii::t('app', 'Poor'),
            'plan_care_evaluation' => Yii::t('app', 'PT Evaluation'),
            'plan_care_therapeutic_exercise' => Yii::t('app', 'Therapeutic Exercise'),
            'plan_care_therapeutic_activities' => Yii::t('app', 'Therapeutic Activities'),
            'plan_care_transfer_training' => Yii::t('app', 'Transfer Training'),
            'plan_care_nero_muscular' => Yii::t('app', 'Nero Muscular'),
            'plan_care_upgrade_home_program' => Yii::t('app', 'Upgrade Home Program'),
            'plan_care_gait_training' => Yii::t('app', 'Gait Training'),
            'plan_care_wb_status' => Yii::t('app', 'Wb Status'),
            'discharge_plan_safety_education' => Yii::t('app', 'Safety Education'),
            'discharge_plan_electrotherapy' => Yii::t('app', 'Electrotherapy'),
            'discharge_plan_ultrasound' => Yii::t('app', 'Ultrasound'),
            'discharge_plan_prosthetic_training' => Yii::t('app', 'Prosthetic Training'),
            'discharge_plan_fabrication_orthotic_device' => Yii::t('app', 'Fabrication Orthotic Device'),
            'discharge_plan_muscle_reduction' => Yii::t('app', 'Muscle Reduction'),
            'discharge_plan_manage_eval_care_plan' => Yii::t('app', 'Manage Eval Care Plan'),
            'discharge_plan_pain_management' => Yii::t('app', 'Pain Management'),
            'discharge_plan_massage' => Yii::t('app', 'Massage'),
            'discharge_plan_stair_training' => Yii::t('app', 'Stair Training'),
            'reason_for_skilled_service' => Yii::t('app', 'Reason For Skilled Service'),
            'bed_mobility_roll_unable' => Yii::t('app', 'Dependent'),
            'bed_mobility_roll_max' => Yii::t('app', 'Max'),
            'bed_mobility_roll_mod' => Yii::t('app', 'Mod'),
            'bed_mobility_roll_min' => Yii::t('app', 'Min'),
            'bed_mobility_roll_cg' => Yii::t('app', 'CG'),
            'bed_mobility_roll_sba' => Yii::t('app', 'SBA'),
            'bed_mobility_roll_s' => Yii::t('app', 'S'),
            'bed_mobility_roll_i' => Yii::t('app', 'I'),
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
            'transfer_bed_chair_unable' => Yii::t('app', 'Dependent'),
            'transfer_bed_chair_max' => Yii::t('app', 'Max'),
            'transfer_bed_chair_mod' => Yii::t('app', 'Mod'),
            'transfer_bed_chair_min' => Yii::t('app', 'Min'),
            'transfer_bed_chair_cg' => Yii::t('app', 'CG'),
            'transfer_bed_chair_sba' => Yii::t('app', 'SBA'),
            'transfer_bed_chair_s' => Yii::t('app', 'S'),
            'transfer_bed_chair_i' => Yii::t('app', 'I'),
            'transfer_toilet_unable' => Yii::t('app', 'Dependent'),
            'transfer_toilet_max' => Yii::t('app', 'Max'),
            'transfer_toilet_mod' => Yii::t('app', 'Mod'),
            'transfer_toilet_min' => Yii::t('app', 'Min'),
            'transfer_toilet_cg' => Yii::t('app', 'CG'),
            'transfer_toilet_sba' => Yii::t('app', 'SBA'),
            'transfer_toilet_s' => Yii::t('app', 'S'),
            'transfer_toilet_i' => Yii::t('app', 'I'),
            'adl_dressing_unable' => Yii::t('app', 'Dependent'),
            'adl_dressing_max' => Yii::t('app', 'Max'),
            'adl_dressing_mod' => Yii::t('app', 'Mod'),
            'adl_dressing_min' => Yii::t('app', 'Min'),
            'adl_dressing_cg' => Yii::t('app', 'CG'),
            'adl_dressing_sba' => Yii::t('app', 'SBA'),
            'adl_dressing_s' => Yii::t('app', 'S'),
            'adl_dressing_i' => Yii::t('app', 'I'),
            'adl_personal_hygiene_unable' => Yii::t('app', 'Dependent'),
            'adl_personal_hygiene_max' => Yii::t('app', 'Max'),
            'adl_personal_hygiene_mod' => Yii::t('app', 'Mod'),
            'adl_personal_hygiene_min' => Yii::t('app', 'Min'),
            'adl_personal_hygiene_cg' => Yii::t('app', 'CG'),
            'adl_personal_hygiene_sba' => Yii::t('app', 'SBA'),
            'adl_personal_hygiene_s' => Yii::t('app', 'S'),
            'adl_personal_hygiene_i' => Yii::t('app', 'I'),
            'adl_shower_unable' => Yii::t('app', 'Dependent'),
            'adl_shower_max' => Yii::t('app', 'Max'),
            'adl_shower_mod' => Yii::t('app', 'Mod'),
            'adl_shower_min' => Yii::t('app', 'Min'),
            'adl_shower_cg' => Yii::t('app', 'CG'),
            'adl_shower_sba' => Yii::t('app', 'SBA'),
            'adl_shower_s' => Yii::t('app', 'S'),
            'adl_shower_i' => Yii::t('app', 'I'),
            'adl_feeding_unable' => Yii::t('app', 'Dependent'),
            'adl_feeding_max' => Yii::t('app', 'Max'),
            'adl_feeding_mod' => Yii::t('app', 'Mod'),
            'adl_feeding_min' => Yii::t('app', 'Min'),
            'adl_feeding_cg' => Yii::t('app', 'CG'),
            'adl_feeding_sba' => Yii::t('app', 'SBA'),
            'adl_feeding_s' => Yii::t('app', 'S'),
            'adl_feeding_i' => Yii::t('app', 'I'),
            'adl_meal_prep_unable' => Yii::t('app', 'Dependent'),
            'adl_meal_prep_max' => Yii::t('app', 'Max'),
            'adl_meal_prep_mod' => Yii::t('app', 'Mod'),
            'adl_meal_prep_min' => Yii::t('app', 'Min'),
            'adl_meal_prep_cg' => Yii::t('app', 'CG'),
            'adl_meal_prep_sba' => Yii::t('app', 'SBA'),
            'adl_meal_prep_s' => Yii::t('app', 'S'),
            'adl_meal_prep_i' => Yii::t('app', 'I'),
            'adl_home_making_unable' => Yii::t('app', 'Dependent'),
            'adl_home_making_max' => Yii::t('app', 'Max'),
            'adl_home_making_mod' => Yii::t('app', 'Mod'),
            'adl_home_making_min' => Yii::t('app', 'Min'),
            'adl_home_making_cg' => Yii::t('app', 'CG'),
            'adl_home_making_sba' => Yii::t('app', 'SBA'),
            'adl_home_making_s' => Yii::t('app', 'S'),
            'adl_home_making_i' => Yii::t('app', 'I'),
            'adl_car_unable' => Yii::t('app', 'Dependent'),
            'adl_car_max' => Yii::t('app', 'Max'),
            'adl_car_mod' => Yii::t('app', 'Mod'),
            'adl_car_min' => Yii::t('app', 'Min'),
            'adl_car_cg' => Yii::t('app', 'CG'),
            'adl_car_sba' => Yii::t('app', 'SBA'),
            'adl_car_s' => Yii::t('app', 'S'),
            'adl_car_i' => Yii::t('app', 'I'),
            'balance_sitting_static_p_minus' => Yii::t('app', 'P-'),
            'balance_sitting_static_p' => Yii::t('app', 'P'),
            'balance_sitting_static_p_plus' => Yii::t('app', 'P+'),
            'balance_sitting_static_f_minus' => Yii::t('app', 'F-'),
            'balance_sitting_static_f' => Yii::t('app', 'F'),
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
            'balance_standing_static_g' => Yii::t('app', 'G'),
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
            'pain_0' => Yii::t('app', '0'),
            'pain_1' => Yii::t('app', '1'),
            'pain_2' => Yii::t('app', '2'),
            'pain_3' => Yii::t('app', '3'),
            'pain_4' => Yii::t('app', '4'),
            'pain_5' => Yii::t('app', '5'),
            'pain_6' => Yii::t('app', '6'),
            'pain_7' => Yii::t('app', '7'),
            'pain_8' => Yii::t('app', '8'),
            'pain_9' => Yii::t('app', '9'),
            'pain_location' => Yii::t('app', 'Site'),
            'edema' => Yii::t('app', 'Edema'),
            'sensory' => Yii::t('app', 'Sensory/Tone/Neuro'),
            'strength_ue' => Yii::t('app', 'Strength UE'),
            'rom_ue' => Yii::t('app', 'ROM UE'),
            'normal_ue' => Yii::t('app', 'Normal UE'),
            'strength_le' => Yii::t('app', 'Strength LE'),
            'rom_le' => Yii::t('app', 'ROM LE'),
            'normal_le' => Yii::t('app', 'Normal LE'),
            'cog_communication' => Yii::t('app', 'Cognition / Communication'),
            'activity_tolerance_poor' => Yii::t('app', 'Poor'),
            'activity_tolerance_fair' => Yii::t('app', 'Fair'),
            'activity_tolerance_good' => Yii::t('app', 'Good'),
            'activity_tolerance_excellent' => Yii::t('app', 'Excellent'),
            'endurance_poor' => Yii::t('app', 'Poor'),
            'endurance_fair' => Yii::t('app', 'Fair'),
            'endurance_good' => Yii::t('app', 'Good'),
            'endurance_excellent' => Yii::t('app', 'Excellent'),
            'posture_poor' => Yii::t('app', 'Poor'),
            'posture_fair' => Yii::t('app', 'Fair'),
            'posture_good' => Yii::t('app', 'Good'),
            'posture_excellent' => Yii::t('app', 'Excellent'),
            'safety_awareness_poor' => Yii::t('app', 'Poor'),
            'safety_awareness_fair' => Yii::t('app', 'Fair'),
            'safety_awareness_good' => Yii::t('app', 'Good'),
            'safety_awareness_excellent' => Yii::t('app', 'Excellent'),
            'gait_even_surface' => Yii::t('app', 'Even Surface'),
            'gait_uneven_surface' => Yii::t('app', 'Uneven Surface'),
            'gait_distance' => Yii::t('app', 'Distance'),
            'gait_device' => Yii::t('app', 'Device'),
            'gait_stairs' => Yii::t('app', 'Stairs'),
            'precautions' => Yii::t('app', 'Precautions'),
            'pattern' => Yii::t('app', 'Pattern'),
            'patient_has_spc' => Yii::t('app', 'SPC'),
            'patient_has_wc' => Yii::t('app', 'W/C'),
            'patient_has_qc' => Yii::t('app', 'QC'),
            'patient_has_crutches' => Yii::t('app', 'Crutches'),
            'patient_has_toilet_seat' => Yii::t('app', 'Toilet Seat'),
            'patient_has_shower_chair' => Yii::t('app', 'Shower Chair'),
            'patient_has_fww' => Yii::t('app', 'FWW/4WW/PUW'),
            'patient_has_commode' => Yii::t('app', 'Commode'),
            'patient_has_hw' => Yii::t('app', 'HW'),
            'patient_has_hospital_bed' => Yii::t('app', 'Hospital Bed'),
            'patient_has_hoyer_lift' => Yii::t('app', 'Hoyer Lift'),
            'patient_has_other' => Yii::t('app', 'Other'),
            'recommended_equipment_spc' => Yii::t('app', 'SPC'),
            'recommended_equipment_wc' => Yii::t('app', 'W/C'),
            'recommended_equipment_qc' => Yii::t('app', 'QC'),
            'recommended_equipment_crutches' => Yii::t('app', 'Crutches'),
            'recommended_equipment_toilet_seat' => Yii::t('app', 'Toilet Seat'),
            'recommended_equipment_shower_chair' => Yii::t('app', 'Shower Chair'),
            'recommended_equipment_fww' => Yii::t('app', 'FWW/4WW/PUW'),
            'recommended_equipment_commode' => Yii::t('app', 'Commode'),
            'recommended_equipment_hw' => Yii::t('app', 'HW'),
            'recommended_equipment_hospital_bed' => Yii::t('app', 'Hospital Bed'),
            'recommended_equipment_hoyer_lift' => Yii::t('app', 'Hoyer Lift'),
            'recommended_equipment_other' => Yii::t('app', 'Other'),
            'functional_limitation_amputation' => Yii::t('app', 'Amputation'),
            'functional_limitation_bowel' => Yii::t('app', 'Bowel'),
            'functional_limitation_contractures' => Yii::t('app', 'Contractures'),
            'functional_limitation_hearing' => Yii::t('app', 'Hearing'),
            'functional_limitation_poor_vision' => Yii::t('app', 'Poor Vision'),
            'functional_limitation_paralysis' => Yii::t('app', 'Paralysis'),
            'functional_limitation_endurance' => Yii::t('app', 'Endurance'),
            'functional_limitation_ambulation' => Yii::t('app', 'Ambulation'),
            'functional_limitation_speech' => Yii::t('app', 'Speech'),
            'functional_limitation_legally_blind' => Yii::t('app', 'Legally Blind'),
            'functional_limitation_dyspnea' => Yii::t('app', ' Dyspnea'),
            'functional_limitation_impaired_vision' => Yii::t('app', 'Impaired Vision'),
            'functional_limitation_other' => Yii::t('app', 'Other'),
            'knowledge_patient' => Yii::t('app', 'Knowledge Patient'),
            'knowledge_pcg' => Yii::t('app', 'Knowledge PCG'),
            'body_mech_poor' => Yii::t('app', 'Poor'),
            'body_mech_fair' => Yii::t('app', 'Fair'),
            'body_mech_good' => Yii::t('app', 'Good'),
            'home_exercise_program_poor' => Yii::t('app', 'Poor'),
            'home_exercise_program_fair' => Yii::t('app', 'Fair'),
            'home_exercise_program_good' => Yii::t('app', 'Good'),
            'home_safety_program_poor' => Yii::t('app', 'Poor'),
            'home_safety_program_fair' => Yii::t('app', 'Fair'),
            'home_safety_program_good' => Yii::t('app', 'Good'),
            'activities_permitted_complete_bedrest' => Yii::t('app', 'Complete Bedrest'),
            'activities_permitted_bedrest_brp' => Yii::t('app', 'Bedrest Brp'),
            'activities_permitted_up_tolerated' => Yii::t('app', 'Up Tolerated'),
            'activities_permitted_transfer_bed' => Yii::t('app', 'Transfer Bed'),
            'activities_permitted_exercise_prescribed' => Yii::t('app', 'Exercise Prescribed'),
            'activities_permitted_pwb' => Yii::t('app', 'PWB'),
            'activities_permitted_crutches' => Yii::t('app', 'Crutches'),
            'activities_permitted_walker' => Yii::t('app', 'Walker'),
            'activities_permitted_cane' => Yii::t('app', 'Cane'),
            'activities_permitted_wheelchair' => Yii::t('app', 'Wheelchair'),
            'activities_permitted_amb' => Yii::t('app', 'Amb assist'),
            'activities_permitted_other' => Yii::t('app', 'Other'),
            'mental_oriented' => Yii::t('app', 'Oriented'),
            'mental_forgetful' => Yii::t('app', 'Forgetful'),
            'mental_comatose' => Yii::t('app', 'Comatose'),
            'mental_disoriented' => Yii::t('app', 'Disoriented'),
            'mental_agitated' => Yii::t('app', 'Agitated'),
            'mental_depressed' => Yii::t('app', 'Depressed'),
            'mental_lethargic' => Yii::t('app', 'Lethargic'),
            'mental_other' => Yii::t('app', 'Other'),
            'comments' => Yii::t('app', 'Comments'),
            'therapist_name' => Yii::t('app', 'Therapist Name'),
            'therapist_title' => Yii::t('app', 'Title'),
            'therapist_signature' => Yii::t('app', 'Therapist Signature'),
            'note_date' => Yii::t('app', 'Note Date'),
            'time_in' => Yii::t('app', 'Time In'),
            'time_out' => Yii::t('app', 'Time Out'),
            'physician_name' => Yii::t('app', 'Physician Name'),
            'physician_phone_number' => Yii::t('app', 'Physician Phone Number'),
            'physician_signature' => Yii::t('app', 'Physician Signature'),
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
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('evalNotes');
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
        return $this->hasOne(Visit::class, ['id' => 'visit_id', 'order_id' => 'order_id'])->inverseOf('evalNote');
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
     * @return NoteEvalQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoteEvalQuery(get_called_class());
    }
}
