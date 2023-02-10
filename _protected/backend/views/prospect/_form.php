<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Prospect */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="prospect-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">

        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'service_id')->textInput() ?>

        <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'zip_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'country')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'license_type')->dropDownList(User::getTitleList(), ['prompt'=>'-- Select Title --']) ?>

        <?= $form->field($model, 'license_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'license_expiration_date')->textInput() ?>

        <?= $form->field($model, 'language')->textInput() ?>

        <?= $form->field($model, 'covered_county')->textInput() ?>

        <?= $form->field($model, 'covered_city')->textInput() ?>

        <?= $form->field($model, 'ip_address')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rejected_by')->textInput() ?>

        <?= $form->field($model, 'rejected_at')->textInput() ?>

        <?= $form->field($model, 'rejection_reason')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'status')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'created_by')->textInput() ?>

        <?= $form->field($model, 'created_at')->textInput() ?>

        <?= $form->field($model, 'updated_by')->textInput() ?>

        <?= $form->field($model, 'updated_at')->textInput() ?>

    </div>
    <div class="box-footer">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
