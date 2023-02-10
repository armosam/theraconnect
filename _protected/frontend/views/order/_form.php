<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use common\models\Service;

/**
 * @var yii\web\View $this
 * @var common\models\Order $model
 * @var ActiveForm $form
 */

?>

<div class="order-form">
    <?php $form = ActiveForm::begin([
        'id' => 'service_request_form',
        'method' => 'post',
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
        <div class="col-lg-12">
            <div class="col-lg-4"><?= $form->field($model, 'service_id')->dropDownList(Service::serviceList(), ['prompt'=>'-- Select Service --']) ?></div>
            <div class="col-lg-2"><?= $form->field($model, 'certification_start_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput() ?>
            </div>
            <div class="col-lg-2"><?= $form->field($model, 'certification_end_date')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        "placeholder" => "__/__/____",
                        "separator" => "/"
                    ]])->textInput() ?>
            </div>
            <div class="col-lg-4"><?= $form->field($model, 'service_frequency') ?></div>
        </div>
        <div class="col-lg-12"><?= $form->field($model, 'comment')->textarea() ?></div>
    </div>
    <br>
    <div class="form-group">

        <?= Html::submitButton(Yii::t('app', '{action}', ['action' => (($model->isNewRecord) ? 'Request a Service' : 'Update Service Request')]), [
            'class' => 'btn btn-success btn-md',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to {action} service request for this patient?', ['action' => (($model->isNewRecord) ? 'create' : 'update')]),
                'method' => 'post',
            ]]) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
        <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>