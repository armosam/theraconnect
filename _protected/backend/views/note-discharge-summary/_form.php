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
/* @var $model common\models\NoteDischargeSummary */
/* @var $form yii\widgets\ActiveForm */

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="note-discharge-summary-form box box-primary">
    <?php $form = ActiveForm::begin(['id' => 'discharge-summary-form']); ?>
    <div class="box-body table-responsive">

        <?php if($model->isNewRecord): ?>
            <div class="col-lg-12">
                <div class="col-lg-4"><?= $form->field($model, 'provider_id')->dropDownList(User::providerList(true), ['prompt'=>'-- Select Therapist --']) ?></div>
                <div class="col-lg-4"><?= $form->field($model, 'order_id')->widget(DepDrop::class, [
                        'options' => ['id'=>'notedischargesummary-order_id'],
                        'pluginOptions'=>[
                            'depends'=>['notedischargesummary-provider_id'],
                            'placeholder' => '-- Select Service Request --',
                            'url' => Url::to(['order/list-provider-orders'])
                        ]
                    ])?>
                </div>
                <div class="col-lg-4"><?= $form->field($model, 'visit_id')->widget(DepDrop::class, [
                        'options' => ['id'=>'notedischargesummary-visit_id'],
                        'pluginOptions'=>[
                            'depends'=>['notedischargesummary-provider_id', 'notedischargesummary-order_id'],
                            'placeholder' => '-- Select Visit --',
                            'url' => Url::to(['order/list-order-visits'])
                        ]
                    ])?>
                </div>
            </div>
        <?php endif; ?>

        <?php // $form->field($model, 'pt')->textInput(['maxlength' => true]) ?>
        <?php // $form->field($model, 'ot')->textInput(['maxlength' => true]) ?>
        <div class="col-lg-12">
            <div class="col-lg-12">
                <?= $form->field($model, 'diagnosis')->textarea(['rows' => 4]) ?>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <h4>REASON FOR DISCHARGE</h4>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'discharge_reason_no_care_needed')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_admission_hospital')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_admission_snf_icf')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_pt_assumed_responsibility')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_pt_moved_out_service_area')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_lack_of_progress')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_pt_refused_service')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'discharge_reason_transfer_hha')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_transfer_op_rehab')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_death')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_lack_of_funds')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_transfer_hospice')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_transfer_personal_agency')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_reason_md_request')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-4">
                        <?= $form->field($model, 'discharge_reason_other')->textarea(['rows' => 10]) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <h4>OVERALL STATUS OF PATIENT AT DISCHARGE</h4>

                    <div class="col-lg-3">
                        <h4>PHYSICAL, EMOTIONAL, MENTAL</h4>
                        <?= $form->field($model, 'mental_oriented')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'mental_forgetful')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'mental_depressed')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'mental_other')->textInput(['maxlength' => true]) ?>
                    </div>

                    <div class="col-lg-3">
                        <h4>ADL’S</h4>

                        <?= $form->field($model, 'functional_ind')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'functional_sup')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'functional_asst')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'functional_dep')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>

                    <div class="col-lg-3">
                        <h4>MOBILE</h4>
                        <?= $form->field($model, 'mobile_ind')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'mobile_sup')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'mobile_asst')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'mobile_dep')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>

                    <div class="col-lg-3">
                        <h4>ASSISTIVE DEVICES</h4>
                        <?= $form->field($model, 'device_wheelchair')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'device_walker')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'device_crutches')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'device_cane')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'device_other')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-6">
                        <h4>PROBLEMS IDENTIFIED</h4>
                        <?= $form->field($model, 'problem_identified1')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'problem_identified2')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'problem_identified3')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'problem_identified4')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'problem_identified5')->textInput(['maxlength' => true]) ?>
                    </div>

                    <div class="col-lg-6">
                        <h4>STATUS OF PROBLEMS AT DISCHARGE</h4>
                        <?= $form->field($model, 'status_of_problem_at_discharge1')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'status_of_problem_at_discharge2')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'status_of_problem_at_discharge3')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'status_of_problem_at_discharge4')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'status_of_problem_at_discharge5')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'summary_care_provided')->textarea(['rows' => 4]) ?>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-4">
                        <h4>TREATMENT GOALS ATTAINED?</h4>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'goals_attained_yes')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'goals_attained_no')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                        <?= $form->field($model, 'goals_attained_partial')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                    </div>
                    <div class="col-lg-2">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-6">
                        <h4>DISCHARGE PLAN</h4>
                        <?= $form->field($model, 'discharge_plan_with_mid_supervision')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_plan_hha')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>

                        <?= $form->field($model, 'discharge_plan_other')->textInput(['maxlength' => true]) ?>
                    </div>

                    <div class="col-lg-6">
                        <h4>NOTIFICATION OF DISCHARGE</h4>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'notification_of_discharge_tc_to_md')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-8">
                            <?= $form->field($model, 'notification_of_discharge_tc_to_md_date')->widget(MaskedInput::class, [
                                'clientOptions' => [
                                    'alias' => 'mm/dd/yyyy',
                                    "placeholder" => "__/__/____",
                                    "separator" => "/"
                                ]])->textInput() ?>
                        </div>
                        <div class="col-lg-4">
                            <?= $form->field($model, 'notification_of_discharge_tc_to_pt')->checkbox(['value' => 'Y', 'uncheck' => 'N', 'labelOptions' => ['class'=>'checkbox-inline']]) ?>
                        </div>
                        <div class="col-lg-8">
                            <?= $form->field($model, 'notification_of_discharge_tc_to_pt_date')->widget(MaskedInput::class, [
                                'clientOptions' => [
                                    'alias' => 'mm/dd/yyyy',
                                    "placeholder" => "__/__/____",
                                    "separator" => "/"
                                ]])->textInput() ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-8">
                        <?= $form->field($model, 'therapist_signature')->widget(Signature::class, ['save_signature_attribute' => 'save_signature', 'allowed' => true]) ?>
                        <?= $form->field($model, 'save_signature')->hiddenInput()->label(false) ?>
                    </div>

                    <div class="col-lg-4">
                        <?= $form->field($model, 'note_date')->widget(MaskedInput::class, [
                            'clientOptions' => [
                                'alias' => 'mm/dd/yyyy',
                                "placeholder" => "__/__/____",
                                "separator" => "/"
                            ]])->textInput() ?>
                    </div>
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
