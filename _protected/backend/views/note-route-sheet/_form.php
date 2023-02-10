<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use kartik\widgets\DepDrop;
use common\models\User;
use common\models\NoteRouteSheet;
use common\widgets\signature\Signature;

/* @var $this yii\web\View */
/* @var $model common\models\NoteRouteSheet */
/* @var $form yii\widgets\ActiveForm */

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="note-route-sheet-form box box-primary">
    <?php $form = ActiveForm::begin(['id' => 'route-sheet-form']); ?>
    <div class="box-body table-responsive">

        <?php if($model->isNewRecord): ?>
            <div class="col-lg-12">
                <div class="col-lg-4"><?= $form->field($model, 'provider_id')->dropDownList(User::providerList(true), ['prompt'=>'-- Select Therapist --']) ?></div>
                <div class="col-lg-4"><?= $form->field($model, 'order_id')->widget(DepDrop::class, [
                        'options' => ['id'=>'noteroutesheet-order_id'],
                        'pluginOptions'=>[
                            'depends'=>['noteroutesheet-provider_id'],
                            'placeholder' => '-- Select Service Request --',
                            'url' => Url::to(['order/list-provider-orders'])
                        ]
                    ])?>
                </div>
                <div class="col-lg-4"><?= $form->field($model, 'visit_id')->widget(DepDrop::class, [
                        'options' => ['id'=>'noteroutesheet-visit_id'],
                        'pluginOptions'=>[
                            'depends'=>['noteroutesheet-provider_id', 'noteroutesheet-order_id'],
                            'placeholder' => '-- Select Visit --',
                            'url' => Url::to(['order/list-order-visits'])
                        ]
                    ])?>
                </div>
            </div>
        <?php endif; ?>

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
            <?/*= $form->field($model, 'visit_total_time')->textInput(['maxlength' => true]) */?>
        </div>-->
        <div class="col-lg-4">
            <?= $form->field($model, 'patient_signature')->widget(Signature::class) ?>
        </div>

        <div class="col-lg-12">
            <?= $form->field($model, 'therapist_signature')->widget(Signature::class, ['save_signature_attribute' => 'save_signature', 'allowed' => true]) ?>
            <?= $form->field($model, 'save_signature')->hiddenInput()->label(false) ?>
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