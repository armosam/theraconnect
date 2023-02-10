<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;

/* @var $this yii\web\View */
/* @var $model common\models\Visit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="visit-form box box-primary">
    <?php $form = ActiveForm::begin(['id' => 'visit-form']); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'visited_at')->widget(DateControl::class, [
              'type' => DateControl::FORMAT_DATETIME
        ]) ?>
        <?= $form->field($model, 'comment')->textarea() ?>
        <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
        <?= $form->field($model, 'order_id')->hiddenInput()->label(false) ?>
    </div>
    <div class="box-footer">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'data' => [
                    'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create new record?") : Yii::t('app', "Are you sure you want to change this record?"),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
