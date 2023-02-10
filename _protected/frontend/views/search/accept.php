<?php

/* @var $this yii\web\View */
/* @var $model common\models\Order */

use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<div class="order-accept">
    <?php $form = ActiveForm::begin([
        'id' => 'accept_service_request_form',
        'method' => 'post',
    ]); ?>
    <div class="text-warning">
        <b><?= Yii::t('app', 'By clicking Accept Service Request button below you will get access to the patient and the patient will be assigned to you.') ?></b>
        <span class="hint-block"><?= Yii::t('app', 'If you change your mind later and need to refuse to provide a service then contact administration.') ?></span>
    </div>
    <?= $form->field($model, 'status')->hiddenInput(['value' => 'A'])->label(false)?>
<br>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Accept Service Request'), [
            'class' => 'btn btn-success btn-md',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to accept this service request for the patient?'),
                'method' => 'post',
            ]]) ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['#'], ['class' => 'btn btn-danger', 'data' => ['dismiss' => 'modal']]) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>