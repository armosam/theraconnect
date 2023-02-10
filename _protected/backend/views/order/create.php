<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\widgets\MaskedInput;
use common\models\Service;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var common\models\forms\CreateOrderForm $model
 */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-create box box-primary">

    <div class="order_create_form">
        <?php $form = ActiveForm::begin([
            'id' => 'order_create_form',
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="col-lg-4">
                    <?= $form->field($model, 'intake_file')->fileInput(['multiple' => false]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'form_485_file')->fileInput(['multiple' => false]) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'other_file')->fileInput(['multiple' => false]) ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4"><?= $form->field($model, 'service_id')->dropDownList(Service::serviceList(), ['prompt'=>'-- Select Service --']) ?></div>
            <div class="col-lg-4"><?= $form->field($model, 'rpt_provider_id')->widget(DepDrop::class, [
                    'options' => ['id'=>'createorderform-rpt_provider_id'],
                    'pluginOptions'=>[
                        'depends'=>['createorderform-service_id'],
                        'placeholder' => '-- Select RPT Therapist --',
                        'url' => Url::to(['order/list-rpt-providers'])
                    ]
                ])?>
            </div>
            <div class="col-lg-4"><?= $form->field($model, 'pta_provider_id')->widget(DepDrop::class, [
                    'options' => ['id'=>'createorderform-pta_provider_id'],
                    'pluginOptions'=>[
                        'depends'=>['createorderform-service_id'],
                        'placeholder' => '-- Select PTA Therapist --',
                        'url' => Url::to(['order/list-pta-providers'])
                    ]
                ])?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4"><?= $form->field($model, 'physician_name')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-5"><?= $form->field($model, 'physician_address')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-3"><?= $form->field($model, 'physician_phone_number')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => '+(999) 999-9999',
                        'removeMaskOnSubmit' => true
                    ]])->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-2"><?= $form->field($model, 'certification_start_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput()->label('Certification Start') ?>
            </div>
            <div class="col-lg-2"><?= $form->field($model, 'certification_end_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput()->label('Certification End') ?>
            </div>
            <div class="col-lg-8"><?= $form->field($model, 'service_frequency')->label('Frequency') ?></div>
            <div class="col-lg-12"><?= $form->field($model, 'comment')->textarea() ?></div>
        </div>
        <br>
        <div class="form-group">

            <?= Html::submitButton(Yii::t('app', 'Create Service Request'), [
                'class' => 'btn btn-success btn-md',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to request a service for this patient?'),
                    'method' => 'post',
                ]
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>
