<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use common\models\User;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var common\models\forms\SubmitOrderForm $model
*/

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-submit box box-primary">

    <div class="order_submit_form">
        <?php $form = ActiveForm::begin([
            'id' => 'order_submit_form',
            'method' => 'post',
            'options' => ['enctype' => 'multipart/form-data']
        ]); ?>
        <div class="row">
            <div class="col-lg-12">
                <div class="col-lg-4">
                    <?= $form->field($model, 'intake_file')
                        ->hint(empty($model->orderIntakeDocument) ? false : Html::a( Yii::t('app', 'Existing Intake Document'), ['order/document', 'id' => $model->orderIntakeDocument->id], ['data-pjax' => '0', 'target' => '_blank']), ['class' => 'small'])
                        ->fileInput(['multiple' => false])
                    ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'form_485_file')
                        ->hint(empty($model->orderForm485Document) ? false : Html::a(Yii::t('app', 'Existing Form-485 Document'), ['order/document', 'id' => $model->orderForm485Document->id], ['data-pjax' => '0', 'target' => '_blank']), ['class' => 'small'])
                        ->fileInput(['multiple' => false])
                    ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'other_file')
                        ->hint(empty($model->orderOtherDocument) ? false : Html::a(Yii::t('app', 'Existing Other Document'), ['order/document', 'id' => $model->orderOtherDocument->id], ['data-pjax' => '0', 'target' => '_blank']), ['class' => 'small'])
                        ->fileInput(['multiple' => false])
                    ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3"><?= $form->field($model, 'certification_start_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput() ?>
            </div>
            <div class="col-lg-3"><?= $form->field($model, 'certification_end_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput() ?>
            </div>
            <div class="col-lg-4"><?= $form->field($model, 'service_frequency')->textInput(['maxlength' => true]) ?></div>
            <div class="col-lg-2"><?= $form->field($model, 'service_rate')->textInput(['maxlength' => true]) ?></div>
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
            <div class="col-lg-6"><?= $form->field($model, 'rpt_provider_id')->dropDownList(User::rptProviderListWithService($model->service_id), ['prompt'=>'-- Not Selected --'])
                    ->hint('Note: If you select RPT Therapist here then service request will be accepted and assigned to the selected RPT therapist automatically.') ?>
            </div>
            <div class="col-lg-6"><?= $form->field($model, 'pta_provider_id')->dropDownList(User::ptaProviderListWithService($model->service_id), ['prompt'=>'-- Not Selected --'])
                    ->hint('Note: If you select PTA Therapist here then after evaluation when RPT therapist transfers service request to PTA it will be assigned to the selected PTA therapist automatically.') ?>
            </div>
            <div class="col-lg-12"><?= $form->field($model, 'comment')->textarea() ?></div>
        </div>
        <br>
        <div class="form-group">
            <p class="hint-block"><?= Yii::t('app', 'By submitting this service request without selecting therapist it will be visible to all therapists to accept this request.') ?></p>
            <?= Html::submitButton(Yii::t('app', 'Submit Service Request'), [
                'class' => 'btn btn-success btn-md',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to submit the service request for this patient?'),
                    'method' => 'post',
                ]
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>