<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\User;

/**
 * @var yii\web\View $this
 * @var ActiveForm $form
 * @var common\models\forms\ChangeProviderForm $model
 */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-change-provider box box-primary">

    <div class="service_change_provider_form">
        <?php $form = ActiveForm::begin([
            'id' => 'service_change_provider_form',
            'method' => 'post',
        ]); ?>
        <div class="row">
            <div class="col-lg-6"><?= $form->field($model, 'rpt_provider_id')->dropDownList(User::rptProviderListWithService($model->service_id), ['prompt'=>'-- Not Selected --'])
                    ->hint('Note: If you select RPT Therapist here then this new therapist will be assigned to the patient and will have access to all patient\'s data.') ?>
            </div>
            <div class="col-lg-6"><?= $form->field($model, 'pta_provider_id')->dropDownList(User::ptaProviderListWithService($model->service_id), ['prompt'=>'-- Not Selected --'])
                    ->hint('Note: If you select PTA Therapist here then this new therapist will be assigned to the patient and have access to all patient\'s data.') ?>
            </div>
            <div class="col-lg-12"><?= $form->field($model, 'comment')->textarea() ?></div>
        </div>
        <br>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Change Therapist'), [
                'class' => 'btn btn-success btn-md',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to change the therapist for this patient?'),
                    'method' => 'post',
                ]
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::button(Yii::t('app', 'Cancel'), ['data' => ['dismiss' => 'modal'], 'class' => 'btn btn-danger']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

</div>