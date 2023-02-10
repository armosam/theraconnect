<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use common\widgets\signature\Signature;

/* @var $this yii\web\View */
/* @var $model common\models\NoteProgress */
/* @var $form yii\widgets\ActiveForm */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My patients'), 'url' => ['provider-order/index']];
$this->params['breadcrumbs'][] = $this->title;

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="note-progress-manage">

    <div class="note-progress-form box box-primary">
        <?php $form = ActiveForm::begin(['id' => 'progress-note-form']); ?>
        <div class="box-body table-responsive">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-12">
                        <h4>Skilled Service Provided</h4>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_pt_evaluation')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_therapeutic_exercises')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_therapeutic_activities')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_bed_mobility')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_gait_training')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_muscle_reduction')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_prosthetic_training')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_home_exercise_program')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_ultrasound')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_hot_pack')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_cold_pack_message')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_massage_mobilization')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_joint_mobilization')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_pt_cg_education')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'service_provided_other', ['options' => ['class' => 'form-group form-inline']])->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h4>Vital Signs</h4>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'vital_signs_bp')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'vital_signs_pulse')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'vital_signs_resp')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'vital_signs_spo2')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-2">
                            <?= $form->field($model, 'vital_signs_temp')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h4>Subjective</h4>
                        <div class="row">
                            <div class="col-lg-2">
                                <?= $form->field($model, 'subjective_co')->textInput(['maxlength' => true])->label(false) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $form->field($model, 'subjective_pain')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $form->field($model, 'subjective_fatigue')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $form->field($model, 'subjective_weakness')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $form->field($model, 'subjective_unsteady_gait')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                            </div>
                            <div class="col-lg-2">
                                <?= $form->field($model, 'subjective_condition_improved')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-lg-4">
                                    <?= $form->field($model, 'subjective_pain_intensity')->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="col-lg-4">
                                    <?= $form->field($model, 'subjective_pain_location')->textInput(['maxlength' => true]) ?>
                                </div>
                                <div class="col-lg-4">
                                    <?= $form->field($model, 'subjective_pain_description')->textInput(['maxlength' => true]) ?>
                                </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <h4>Pain Frequency</h4>
                                <div class="col-lg-2">
                                    <?= $form->field($model, 'subjective_pain_frequency_all_time')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                                </div>
                                <div class="col-lg-2">
                                    <?= $form->field($model, 'subjective_pain_frequency_daily')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                                </div>
                                <div class="col-lg-2">
                                    <?= $form->field($model, 'subjective_pain_frequency_less_daily')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                                </div>
                                <div class="col-lg-2">
                                    <?= $form->field($model, 'subjective_pain_no_pain')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                                </div>
                                <div class="col-lg-4">
                                    <?= $form->field($model, 'subjective_pain_other', ['options' => ['class' => 'form-group form-inline']])->textInput(['maxlength' => true]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <h4>Subjective Data</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <h4>Bed Mobility</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><b>Bridge</b></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'bed_mobility_bridge_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
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
                                <td><b>Supine to sit</b></td>
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
                                <td><b>Sit to supine</b></td>
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

                        <h4>Transfer</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><b>Sit <--> Stand</b></td>
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
                                <td><b>Bed <--> Chair</b></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_bed2chair_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
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
                            <tr>
                                <td><b>Bathing/Shower</b></td>
                                <td><?= $form->field($model, 'transfer_shower_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_shower_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_shower_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_shower_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_shower_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_shower_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_shower_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'transfer_shower_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                        </table>

                        <h4>Gait</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><?= $form->field($model, 'gait_even_surface_distance')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_even_surface_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                            <tr>
                                <td><?= $form->field($model, 'gait_uneven_surface_distance')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'gait_uneven_surface_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                            <tr>
                                <td colspan="9"><?= $form->field($model, 'gait_device')->textInput(['maxlength' => true]) ?></td>
                            </tr>
                        </table>

                        <h4>Stairs</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td>
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'stairs_steps')->textInput(['maxlength' => true]) ?>
                                    </div>
                                    <div class="col-lg-6">
                                        <?= $form->field($model, 'stairs_device')->textInput(['maxlength' => true]) ?>
                                    </div>
                                </td>
                                <td><?= $form->field($model, 'stairs_unable')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'stairs_max')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'stairs_mod')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'stairs_min')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'stairs_cg')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'stairs_sba')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'stairs_s')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'stairs_i')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                        </table>

                        <div class="col-lg-12">
                            <?= $form->field($model, 'pattern')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-lg-12">
                            <?= $form->field($model, 'precautions')->textInput(['maxlength' => true]) ?>
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
                    <div class="col-lg-12">
                        <?= $form->field($model, 'therapeutic_exercises')->textarea(['rows' => 3]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <h4>Assessments</h4>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'patient_is_uncooperative')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'patient_requires_encouragement')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'patient_is_cooperative')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-bordered">
                            <tr>
                                <td><b>Patient’s response to today’s treatment</b></td>
                                <td><?= $form->field($model, 'patient_response_today_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_response_today_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_response_today_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_response_today_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                            <tr>
                                <td><b>Patient’s progress toward established goals</b></td>
                                <td><?= $form->field($model, 'patient_progress_toward_goals_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_progress_toward_goals_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_progress_toward_goals_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_progress_toward_goals_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                            <tr>
                                <td><b>Activity Tolerance/Endurance</b></td>
                                <td><?= $form->field($model, 'patient_activity_tolerance_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_activity_tolerance_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_activity_tolerance_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_activity_tolerance_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                            <tr>
                                <td><b>Safety/Body mechanics</b></td>
                                <td><?= $form->field($model, 'patient_safety_mechanics_poor')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_safety_mechanics_fair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_safety_mechanics_good')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                                <td><?= $form->field($model, 'patient_safety_mechanics_excellent')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'assessment_comments')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <h4>Deficit Areas / Reasons for being home bound</h4>
                        <table class="table table-bordered">
                            <tr>
                                <td><b>ROM</b></td>
                                <td><?= $form->field($model, 'rom_ue')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'rom_le')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'rom_cervical')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'rom_trunk')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'rom_other')->textInput(['maxlength' => true]) ?></td>
                            </tr>
                            <tr>
                                <td><b>Strength</b></td>
                                <td><?= $form->field($model, 'strength_ue')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'strength_le')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'strength_cervical')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'strength_trunk')->textInput(['maxlength' => true]) ?></td>
                                <td><?= $form->field($model, 'strength_other')->textInput(['maxlength' => true]) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-lg-3">
                            <?= $form->field($model, 'inadequate_safety_awareness')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'unable_ambulating_outdoors')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'difficulty_transfer')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'localized_weakness')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'inefficient_gait')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'bed_chair_bound')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'impaired_balance')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'limited_rom')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'profound_general_weakness')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'requires_assistance_to_leave_home')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'difficulty_manage_stairs')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-3">
                            <?= $form->field($model, 'significant_effort_performing_tasks')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-12">
                            <?= $form->field($model, 'pt_plan_comments')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                </div>
        <hr>
                <div class="row well">
                    <div class="col-lg-12">
                        <div class="col-lg-6">
                            <?php if ($model->isRPTSignatureVisible(Yii::$app->user->id)): ?>
                                <?= $form->field($model, 'therapist_signature0')->widget(Signature::class, ['allowed' => $model->isRPTSignatureEditable(Yii::$app->user->id)]) ?>
                                <div class="text-muted"><?= $model->rptTherapistNameTitle(Yii::$app->user->id) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-lg-6">
                            <?php if ($model->isPTASignatureVisible(Yii::$app->user->id)): ?>
                                <?= $form->field($model, 'therapist_signature')->widget(Signature::class, ['save_signature_attribute' => 'save_signature', 'allowed' => $model->isPTASignatureEditable(Yii::$app->user->id)]) ?>
                                <?= $form->field($model, 'save_signature')->hiddenInput()->label(false) ?>
                                <div class="text-muted"><?= $model->ptaTherapistNameTitle(Yii::$app->user->id) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-lg-offset-6">
                        <?= $form->field($model, 'note_date')->widget(MaskedInput::class, [
                            'clientOptions' => [
                                'alias' => 'mm/dd/yyyy',
                                "placeholder" => "__/__/____",
                                "separator" => "/"
                            ]])->textInput() ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'time_in')->textInput(['maxlength' => true, 'type' => 'time']) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'time_out')->textInput(['maxlength' => true, 'type' => 'time']) ?>
                    </div>
                </div>
            </div>
        <br>
        <div class="box-footer">
            <div class="form-group">
                <div style="padding: 0 0 0 20px"><?= $form->field($model, 'submit')
                    ->checkbox(['labelOptions' => ['style' => 'color:green']])
                    ->hint('After setting this checkbox and submitting the note will not be able to change later.', ['class'=>'hint-block small']) ?>
                </div>
                <?= Html::submitButton(Yii::t('app', 'Save'), [
                    'class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat',
                    'data' => [
                        'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create new record?") : Yii::t('app', "Are you sure you want to change this record?"),
                        'method' => 'post',
                    ],
                ]) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
                <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
