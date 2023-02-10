<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use common\models\User;
use common\models\Service;
use common\widgets\ISO639\Language;

/* @var $this yii\web\View */
/* @var $model common\models\Patient */
/* @var $form yii\widgets\ActiveForm */
/* @var $uid int */
?>

<div class="patient-form box box-primary">
    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">
        <?php if($model->isNewRecord): ?>
            <div class="col-lg-12">
                <div class="col-lg-3">
                    <?= $form->field($model, 'service_id')->dropDownList(Service::serviceList(), ['multiple' => true, 'size' => 3])->hint('Please hold [ctrl] button to select multiple services.')?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'physician_name')->textInput(['maxlength' => true])?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'physician_address')->textInput(['maxlength' => true])?>
                </div>
                <div class="col-lg-2">
                    <?= $form->field($model, 'physician_phone_number')->widget(MaskedInput::class, [
                        'clientOptions' => [
                            'alias' => '+(999) 999-9999',
                            'removeMaskOnSubmit' => true
                        ]])->textInput(['maxlength' => true]) ?>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="col-lg-4">
                    <?= $form->field($model, 'intake_file')->fileInput(['multiple' => false])?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'form_485_file')->fileInput(['multiple' => false])?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'other_file')->fileInput(['multiple' => false])?>
                </div>
            </div>
        <?php endif; ?>
        <div class="col-lg-12">
            <div class="col-lg-3"><?= $form->field($model, 'customer_id')->dropDownList(User::customerList(true, false, true), ['prompt'=>'Select Agency', 'readonly' => !$model->isNewRecord]) ?></div>
            <div class="col-lg-3"><?= $form->field($model, 'patient_number')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-3">
                <?= $form->field($model, 'start_of_care')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput() ?>
            </div>
            <div class="col-lg-3"><?= $form->field($model, 'birth_date')->widget(MaskedInput::class, [
                'clientOptions' => [
                    'alias' => 'mm/dd/yyyy',
                    "placeholder" => "__/__/____",
                    "separator" => "/",
                ]])->textInput() ?>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="col-lg-3"><?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-3"><?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-3"><?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-3"><?= $form->field($model, 'gender')->dropDownList(User::getGenderList(), ['prompt' => Yii::t('app', 'Not Selected')]) ?></div>
        </div>
        <div class="col-lg-12">
            <div class="col-lg-2">
                <?= $form->field($model, 'phone_number')->widget(MaskedInput::class, [
                'clientOptions' => [
                    'alias' => '+(999) 999-9999',
                    'removeMaskOnSubmit' => true
                ]])->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-lg-4"><?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-3"><?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-1"><?= $form->field($model, 'state')->textInput(['maxlength' => true, 'value' => 'CA']) ?></div>
            <div class="col-lg-1"><?= $form->field($model, 'zip_code')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-1"><?= $form->field($model, 'country')->textInput(['maxlength' => true, 'value' => 'USA']) ?></div>
        </div>
        <div class="col-lg-12">
            <div class="col-lg-1"><?= $form->field($model, 'ssn')->widget(MaskedInput::class, [
                'clientOptions' => [
                    'alias' => '999-99-9999',
                    "placeholder" => "___-__-____",
                    "separator" => "-"
                ]])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-2"><?= $form->field($model, 'preferred_language')->dropDownList(Language::allEnglish(), ['prompt' => Yii::t('app', 'Not Selected')]) ?></div>
            <div class="col-lg-2"><?= $form->field($model, 'preferred_gender')->dropDownList(User::getGenderList(), ['prompt' => Yii::t('app', 'Not Selected')]) ?></div>
            <div class="col-lg-3"><?= $form->field($model, 'emergency_contact_name')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-2"><?= $form->field($model, 'emergency_contact_number')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => '+(999) 999-9999',
                        'removeMaskOnSubmit' => true
                    ]])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-2"><?= $form->field($model, 'emergency_contact_relationship')->textInput(['maxlength' => true]) ?></div>
        </div>
        <?php // echo $form->field($model, 'status')->dropDownList($model->getStatusList()) ?>

    </div>
    <div class="box-footer">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), [
                'class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat',
                'data' => [
                    'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create new record?") : Yii::t('app', "Are you sure you want to change this record?"),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning btn-flat hidden-xs']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index', 'uid' => $uid], ['class' => 'btn btn-danger btn-flat']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
