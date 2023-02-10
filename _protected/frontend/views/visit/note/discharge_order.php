<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\dialog\Dialog;
use yii\widgets\MaskedInput;
use common\widgets\signature\Signature;

/* @var $this yii\web\View */
/* @var $model common\models\NoteDischargeOrder */
/* @var $form yii\widgets\ActiveForm */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['provider-order/index']];
$this->params['breadcrumbs'][] = $this->title;

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="note-discharge-order-manage">

    <div class="note-discharge-order-form box box-primary">
        <?php $form = ActiveForm::begin(['id' => 'discharge-order-form']); ?>
        <div class="box-body table-responsive">

            <div class="col-lg-12">
                <?= $form->field($model, 'patient_status_findings')->textarea(['rows' => 4]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'physician_orders')->textarea(['rows' => 4]) ?>
            </div>

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
            <div class="col-lg-6 well well-sm">
                <?= $form->field($model, 'therapist_signature')->widget(Signature::class, ['save_signature_attribute' => 'save_signature', 'allowed' => true]) ?>
                <?= $form->field($model, 'save_signature')->hiddenInput()->label(false) ?>
                <div class="text-muted"><?= $model->therapistNameTitle() ?></div>
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