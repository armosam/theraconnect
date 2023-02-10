<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nenad\passwordStrength\PasswordInput;
use frontend\models\forms\ResetPasswordForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model ResetPasswordForm */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-reset-password">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-lg-5 well bs-component">

        <p><?= Yii::t('app', 'Please choose your new password') ?></p>

        <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

            <?= $form->field($model, 'password')->widget(PasswordInput::class, ['language' => substr(Yii::$app->language, 0, 2)]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-primary']) ?>
            </div>
            
        <?php ActiveForm::end(); ?>

    </div>

</div>
