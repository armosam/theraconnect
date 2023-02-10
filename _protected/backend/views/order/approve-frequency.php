<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var common\models\forms\ApproveFrequencyForm $model
 */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-frequency-approve box box-primary">

    <div class="service_frequency_approve_form">
        <?php $form = ActiveForm::begin([
            'id' => 'service_frequency_approve_form',
            'method' => 'post',
        ]); ?>
        <div class="row">
            <div class="col-lg-12"><?= $form->field($model, 'service_frequency')->textInput() ?></div>
            <div class="col-lg-12"><?= $form->field($model, 'comment')->textarea() ?></div>
        </div>
        <br>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Approve Frequency'), [
                'class' => 'btn btn-success btn-md',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to approve the service frequency for this patient?'),
                    'method' => 'post',
                ]
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>