<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\models\forms\PasswordResetRequestForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model PasswordResetRequestForm */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-request-password-reset">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-lg-5 well bs-component">

        <p><?= Yii::t('app', 'A link to reset password will be sent to your email.') ?></p>

        <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

            <?= $form->field($model, 'email')->textInput(['placeholder' => Yii::t('app', 'Please fill out your email.')]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Send'), ['class' => 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
