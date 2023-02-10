<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use common\models\Service;
use common\models\Order;
use common\models\User;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var common\models\forms\CompleteOrderForm $model
 */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-complete box box-primary">

    <div class="order_complete_form">
        <?php $form = ActiveForm::begin([
            'id' => 'order_complete_form',
            'method' => 'post',
        ]); ?>
        <div class="row">
            <div class="col-lg-4"><?= $form->field($model, 'service_id')->dropDownList(Service::serviceList(), ['prompt'=>'-- Select Service --', 'disabled' => true]) ?></div>
            <div class="col-lg-4"><?= $form->field($model, 'certification_start_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-lg-4"><?= $form->field($model, 'certification_end_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput(['disabled' => true]) ?>
            </div>
            <div class="col-lg-4"><?= $form->field($model, 'service_frequency')->textInput(['disabled' => true]) ?></div>
            <div class="col-lg-12"><?= $form->field($model, 'provider_id')->dropDownList(User::providerList(), ['prompt'=>'-- Select Therapist --', 'disabled' => true]) ?></div>
            <div class="col-lg-12"><?= $form->field($model, 'comment')->textarea(['disabled' => true]) ?></div>
        </div>
        <br>
        <div class="form-group">
            <?= $form->field($model, 'status')->hiddenInput(['value' => Order::ORDER_STATUS_COMPLETED])->label(false)?>
            <?= Html::submitButton(Yii::t('app', 'Complete Service Request'), [
                'class' => 'btn btn-success btn-md',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to complete the service request for this patient?'),
                    'method' => 'post',
                ]
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>