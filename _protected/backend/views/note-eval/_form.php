<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use kartik\widgets\DepDrop;
use common\models\User;
use common\widgets\signature\Signature;

/* @var $this yii\web\View */
/* @var $model common\models\NoteEval */
/* @var $form yii\widgets\ActiveForm */

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="note-eval-form box box-primary">
    <?php $form = ActiveForm::begin(['id' => 'eval-note-form']); ?>
    <div class="box-body table-responsive">
        <div class="col-lg-12">

            <?php if($model->isNewRecord): ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4"><?= $form->field($model, 'provider_id')->dropDownList(User::providerList(true), ['prompt'=>'-- Select Therapist --']) ?></div>
                    <div class="col-lg-4"><?= $form->field($model, 'order_id')->widget(DepDrop::class, [
                            'options' => ['id'=>'noteeval-order_id'],
                            'pluginOptions'=>[
                                'depends'=>['noteeval-provider_id'],
                                'placeholder' => '-- Select Service Request --',
                                'url' => Url::to(['order/list-provider-orders'])
                            ]
                        ])?>
                    </div>
                    <div class="col-lg-4"><?= $form->field($model, 'visit_id')->widget(DepDrop::class, [
                            'options' => ['id'=>'noteeval-visit_id'],
                            'pluginOptions'=>[
                                'depends'=>['noteeval-provider_id', 'noteeval-order_id'],
                                'placeholder' => '-- Select Visit --',
                                'url' => Url::to(['order/list-order-visits'])
                            ]
                        ])?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-5">
                    <?= $form->field($model, 'diagnosis')->textarea(['rows' => 10]) ?>
                </div>
                <div class="col-lg-7">
                    <h4>Prior level of function</h4>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'prior_level_independent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'prior_level_wc_bound')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'prior_level_bed_bound')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <?= $form->field($model, 'prior_level_min_assist')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'prior_level_mod_assist')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'prior_level_max_assist')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>

                    <h4>Living Situation</h4>
                    <div class="row">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'living_situation_apartment')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'living_situation_house')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'living_situation_facility')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'living_situation_bc')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'living_situation_mobile')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'living_situation_alone')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'living_situation_with_family')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'living_situation_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'living_situation_cg_name')->textInput(['maxlength' => true])->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-2">
                            <?= $form->field($model, 'living_situation_stairs')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'living_situation_stairs_count')->textInput(['maxlength' => true])->label(false) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'living_situation_elevator')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'living_situation_no_stairs')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                </div>
            </div>
        <hr>
            <div class="row">
                <div class="col-lg-6">
                    <h4>Vital Signs</h4>
                    <div class="col-lg-2">
                    <?= $form->field($model, 'vital_signs_bp')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3">
                    <?= $form->field($model, 'vital_signs_pulse')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-3">
                    <?= $form->field($model, 'vital_signs_resp')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-2">
                    <?= $form->field($model, 'vital_signs_spo2')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-2">
                    <?= $form->field($model, 'vital_signs_temp')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4>Evaluation</h4>
                    <div class="col-lg-8">
                        <?= $form->field($model, 'frequency')->textInput(['maxlength' => true, 'disabled' => (isset($model->order) && $model->order->frequency_status === 'A')]) ?>
                    </div>
                    <!--<div class="col-lg-4">
                        <?php /*$form->field($model, 'soc')->widget(MaskedInput::class, [
                            'clientOptions' => [
                                'alias' => 'mm/dd/yyyy',
                                "placeholder" => "__/__/____",
                                "separator" => "/"
                            ]])->textInput() */?>
                    </div>-->
                    <div class="col-lg-4">
                        <?= $form->field($model, 'initial')->radioList(['I' => 'Initial', 'R' => 'Recert'])->label(false) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <?= $form->field($model, 'problems')->textarea(['rows' => 5]) ?>
                </div>
                <div class="col-lg-6">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'goals')->textarea(['rows' => 5]) ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'estimate_completion_date')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <h4>Rehab Potential / Prognosis</h4>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'rehab_potential_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'rehab_potential_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'rehab_potential_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $form->field($model, 'rehab_potential_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'reason_for_skilled_service')->textarea(['rows' => 5]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <h4>Plan of Care/Physical Therapy Orders</h4>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_evaluation')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_gait_training')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_wb_status')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_transfer_training')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_nero_muscular')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_upgrade_home_program')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_therapeutic_exercise')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'plan_care_therapeutic_activities')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4>Discharge Plan</h4>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_safety_education')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_electrotherapy')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_ultrasound')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_pain_management')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_stair_training')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_muscle_reduction')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_manage_eval_care_plan')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_prosthetic_training')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_fabrication_orthotic_device')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'discharge_plan_massage')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <h4>Balance</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th></th>
                            <th colspan="9" class="text-center">Static</th>
                            <th colspan="9" class="text-center">Dynamic</th>
                        </tr>
                        <tr>
                            <td><b>Sitting</b></td>
                            <td><?= $form->field($model, 'balance_sitting_static_p_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_p')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_p_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_f_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_f')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_f_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_g_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_g')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_static_g_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_p_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_p')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_p_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_f_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_f')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_f_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_g_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_g')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_static_g_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Standing</b></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_p_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_p')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_p_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_f_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_f')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_f_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_g_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_g')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_sitting_dynamic_g_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_p_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_p')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_p_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_f_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_f')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_f_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_g_minus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_g')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'balance_standing_dynamic_g_plus')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <h4>Bed Mobility</h4>
                    <table class="table table-bordered">
                        <tr>
                            <td><b>Rolling / Scoot</b></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_roll_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Supine to Sit</b></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_supine2sit_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Sit to Supine</b></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'bed_mobility_sit2supine_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                    </table>

                    <h4>Transfers</h4>
                    <table class="table table-bordered">
                        <tr>
                            <td><b>Sit to stand</b></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_sit2stand_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Bed to Chair</b></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_bed_chair_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Toilet</b></td>
                            <td><?= $form->field($model, 'transfer_toilet_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_toilet_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_toilet_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_toilet_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_toilet_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_toilet_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_toilet_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'transfer_toilet_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                    </table>

                    <h4>ADL</h4>
                    <table class="table table-bordered">
                        <tr>
                            <td><b>Dressing</b></td>
                            <td><?= $form->field($model, 'adl_dressing_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_dressing_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_dressing_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_dressing_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_dressing_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_dressing_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_dressing_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_dressing_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Personal Hygiene</b></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_personal_hygiene_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Bathing / Shower</b></td>
                            <td><?= $form->field($model, 'adl_shower_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_shower_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_shower_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_shower_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_shower_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_shower_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_shower_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_shower_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Feeding</b></td>
                            <td><?= $form->field($model, 'adl_feeding_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_feeding_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_feeding_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_feeding_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_feeding_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_feeding_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_feeding_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_feeding_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Meal Prep</b></td>
                            <td><?= $form->field($model, 'adl_meal_prep_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_meal_prep_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_meal_prep_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_meal_prep_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_meal_prep_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_meal_prep_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_meal_prep_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_meal_prep_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Home Making</b></td>
                            <td><?= $form->field($model, 'adl_home_making_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_home_making_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_home_making_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_home_making_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_home_making_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_home_making_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_home_making_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_home_making_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                        <tr>
                            <td><b>Car</b></td>
                            <td><?= $form->field($model, 'adl_car_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_car_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_car_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_car_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_car_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_car_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_car_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            <td><?= $form->field($model, 'adl_car_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Pain</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><?= $form->field($model, 'pain_0')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_1')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_2')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_3')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_4')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_5')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_6')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_7')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_8')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                    <td><?= $form->field($model, 'pain_9')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-lg-4"><?= $form->field($model, 'edema')->textInput(['maxlength' => true]) ?></div>
                        <div class="col-lg-8"><?= $form->field($model, 'pain_location')->textInput(['maxlength' => true]) ?></div>
                        <div class="col-lg-12"><?= $form->field($model, 'sensory')->radioList(['I' => 'Intact', 'N' => 'Not Intact']) ?></div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4">
                        <?= $form->field($model, 'strength_ue')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                        <?= $form->field($model, 'rom_ue')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                        <?= $form->field($model, 'normal_ue')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                        <?= $form->field($model, 'strength_le')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                        <?= $form->field($model, 'rom_le')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-4">
                        <?= $form->field($model, 'normal_le')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <?= $form->field($model, 'cog_communication')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12"><h4>Activity Tolerance</h4></div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'activity_tolerance_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'activity_tolerance_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'activity_tolerance_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'activity_tolerance_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12"><h4>Endurance</h4></div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'endurance_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'endurance_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'endurance_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'endurance_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12"><h4>Posture</h4></div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'posture_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'posture_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'posture_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'posture_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12"><h4>Safety Awareness</h4></div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'safety_awareness_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'safety_awareness_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'safety_awareness_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'safety_awareness_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <h4>Gait Description</h4>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'gait_even_surface')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'gait_uneven_surface')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'gait_distance')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'gait_device')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'gait_stairs')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'precautions')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-lg-12">
                    <?= $form->field($model, 'pattern')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="col-lg-5">
                        <div class="col-lg-12"><h4>Patient Has</h4></div>
                        <?= $form->field($model, 'patient_has_spc')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_wc')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_qc')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_crutches')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_toilet_seat')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_shower_chair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_fww')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_commode')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_hw')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_hospital_bed')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_hoyer_lift')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'patient_has_other')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-7">
                        <div class="col-lg-12"><h4>Recommended Equipment</h4></div>
                        <?= $form->field($model, 'recommended_equipment_spc')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_wc')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_qc')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_crutches')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_toilet_seat')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_shower_chair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_fww')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_commode')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_hw')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_hospital_bed')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_hoyer_lift')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'recommended_equipment_other')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                </div>
            </div>
        <hr>
            <div class="row">
                <div class="col-lg-6">
                    <h4>Mental Status</h4>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'mental_oriented')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'mental_forgetful')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'mental_comatose')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'mental_disoriented')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'mental_agitated')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'mental_depressed')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'mental_lethargic')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-8">
                    <?= $form->field($model, 'mental_other', ['options' => ['class' => 'form-group form-inline']])->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="col-lg-6">
                        <?= $form->field($model, 'knowledge_patient')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                        <?= $form->field($model, 'knowledge_pcg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>

                    <div class="col-lg-6">
                        <b>Body Mech</b>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'body_mech_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'body_mech_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'body_mech_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>

                    <div class="col-lg-6">
                        <b>Home Exercise Program</b>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'home_exercise_program_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'home_exercise_program_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'home_exercise_program_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>

                    <div class="col-lg-6">
                        <b>Home Safety Program</b>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'home_safety_program_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'home_safety_program_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'home_safety_program_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                </div>
            </div>
        <hr>
            <div class="row">
                <div class="col-lg-6">
                    <h4>Activities Permitted</h4>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_complete_bedrest')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_bedrest_brp')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_up_tolerated')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_transfer_bed')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_exercise_prescribed')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_pwb')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_crutches')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_walker')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_cane')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_wheelchair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_amb')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-6">
                    <?= $form->field($model, 'activities_permitted_other', ['options' => ['class' => 'form-group form-inline']])->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h4>Functional Limitation</h4>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'functional_limitation_amputation')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_bowel')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_contractures')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_hearing')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_poor_vision')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_paralysis')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_endurance')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_ambulation')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_speech')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_legally_blind')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_dyspnea')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                    <?= $form->field($model, 'functional_limitation_impaired_vision')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-12">
                    <?= $form->field($model, 'functional_limitation_other', ['options' => ['class' => 'form-group form-inline']])->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?= $form->field($model, 'comments')->textarea(['rows' => 2]) ?>
                </div>
            </div>
        <hr>
            <div class="row">
                <div class="col-lg-2">
                    <?= $form->field($model, 'note_date')->widget(MaskedInput::class, [
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            "placeholder" => "__/__/____",
                            "separator" => "/"
                        ]])->textInput() ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'time_in')->textInput(['maxlength' => true, 'type' =>'time']) ?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'time_out')->textInput(['maxlength' => true, 'type' =>'time']) ?>
                </div>
                <div class="col-lg-6">
                    <?= $form->field($model, 'therapist_signature')->widget(Signature::class, ['save_signature_attribute' => 'save_signature', 'allowed' => true]) ?>
                    <?= $form->field($model, 'save_signature')->hiddenInput()->label(false) ?>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="box-footer">
        <div class="form-group">
            <?php if (!$model->isNewRecord): ?>
                <div style="padding: 0 0 0 20px"><?= $form->field($model, 'submit')
                        ->checkbox(['labelOptions' => ['style' => 'color:green']])
                        ->hint('After setting this checkbox the note will be submitted', ['class'=>'hint-block small']) ?>
                </div>
            <?php endif; ?>
            <?= Html::submitButton(Yii::t('app', 'Save'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'data' => [
                    'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create new record?") : Yii::t('app', "Are you sure you want to change this record?"),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
