<?php

use common\models\CredentialType;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\helpers\ConstHelper;

/* @var $this yii\web\View */
/* @var $model common\models\base\CredentialType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credential-type-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'credential_type_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'assigned_number_label')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'icon_class')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ordering')->dropDownList(range(0,(CredentialType::find()->count()+5))) ?>

        <?= $form->field($model, 'status')->dropDownList(ConstHelper::getStatusList()) ?>

    </div>
    <br>
    <div class="box-footer">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), [
                'class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat',
                'data' => [
                    'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create a new record?") : Yii::t('app', "Are you sure you want to update this record?"),
                    'method' => 'post',
                ]
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Close'), ['index'], ['class' => 'btn btn-danger btn-flat']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
