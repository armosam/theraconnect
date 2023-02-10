<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\dialog\Dialog;
use yii\widgets\MaskedInput;
use common\models\NoteRouteSheet;
use common\widgets\signature\Signature;

/* @var $this yii\web\View */
/* @var $model common\models\NoteRouteSheet */
/* @var $form yii\widgets\ActiveForm */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['provider-order/index']];
$this->params['breadcrumbs'][] = $this->title;

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="note-route-sheet-manage">

    <div class="note-route-sheet-form box box-primary">
        <?php $form = ActiveForm::begin(['id' => 'route-sheet-form']); ?>
        <div class="box-body table-responsive">

            <div class="col-lg-2">
                <?= $form->field($model, 'visit_code')->dropDownList(NoteRouteSheet::getVisitCode(), ['maxlength' => true]) ?>
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
            <!--<div class="col-lg-2">
            <? /*= $form->field($model, 'visit_total_time')->textInput(['maxlength' => true]) */?>
            </div>-->
            <div class="col-lg-4">
                <?= $form->field($model, 'patient_signature')->widget(Signature::class, ['signed_by' => $model->patient_name, 'allowed' => true]) ?>
                <div class="form-inline well well-sm"><strong class=""><?= Yii::t('app', 'Name') ?>: </strong> <?= $model->patient_name ?></div><br>
            </div>

            <div class="col-lg-12 well well-lg">
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
