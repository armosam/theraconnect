<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Service;
use common\helpers\ConstHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Service */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="service-form box box-primary">

    <?php $form = ActiveForm::begin(); ?>

    <div class="box-body table-responsive">

    <?= $form->field($model, 'service_name')->textInput(['maxlength' => true]) ?>

    <?php // echo $form->field($model, 'created_by')->textInput() ?>

    <?php // echo $form->field($model, 'created_at')->textInput() ?>

    <?php // echo $form->field($model, 'updated_by')->textInput() ?>

    <?php // echo $form->field($model, 'updated_at')->textInput() ?>

    <?php echo $form->field($model, 'ordering')->dropDownList(range(0,(Service::find()->count()+3))); ?>

    <?php echo $form->field($model, 'status')->dropDownList(ConstHelper::getStatusList()) ?>

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
