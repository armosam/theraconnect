<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var common\models\Order $model
 */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-allow-transfer box box-primary">

    <div class="order_allow_transfer_form">
        <?php $form = ActiveForm::begin([
            'id' => 'order_allow_transfer_form',
            'method' => 'post',
        ]); ?>
        <div class="row">
            <div class="col-lg-12"><?= $form->field($model, 'allow_transfer_to')->checkbox(['value' => 'Y'])
                    ->hint('To transfer order to another therapist please check this checkbox and press Allow Transfer button.') ?>
            </div>
        </div>
        <br>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Allow Transfer'), [
                'class' => 'btn btn-success btn-md',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to transfer patient to another therapist?'),
                    'method' => 'post',
                ]
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>