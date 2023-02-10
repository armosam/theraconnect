<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use common\models\UserCredential;

/* @var $this yii\web\View */
/* @var $model common\models\UserCredential */
/* @var $form yii\widgets\ActiveForm */

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="user-credential-form box box-primary">
    <h3><?= UserCredential::credentialTypes($model->credential_type_id) ?></h3>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="box-body table-responsive">

        <?= $form->field($model, 'assigned_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'expire_date')->widget(MaskedInput::class, [
            'clientOptions' => [
                'alias' => 'mm/dd/yyyy',
                "placeholder" => "__/__/____",
                "separator" => "/"
            ]])->textInput() ?>

        <?= $form->field($model, 'upload_file')->fileInput(['multiple' => false])->hint(
                $model->file_name ? Yii::t('app', 'Already Attached File: ') . Html::a(Html::tag('strong', $model->file_name), ['user-credential/document', 'id' => $model->id], ['class' => 'label label-success']) . '<br><br>' : ''
        ) ?>

    </div>
    <br>
    <div class="box-footer">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'data' => [
                    'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create new record?") : Yii::t('app', "Are you sure you want to change this record? After changing this record it will move to the pending status."),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
