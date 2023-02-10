<?php

use yii\helpers\Html;
use yii\captcha\Captcha;
use yii\bootstrap\ActiveForm;
use common\models\LoginAttempt;
use common\models\forms\LoginForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model LoginForm */

?>
<!--<div class="login-box">-->
<div class="site-login">
    <div class="container">
        <div class="row">
            <div class="col-sm-6 col-md-4 col-md-offset-4">
                <br>
                <div class="well panel panel-default">
                    <div class="panel panel-heading text-center" style="background-color: #e4e4e4;color: #005d73; font-size: large;">
                        <strong><?= $this->title ?></strong>
                    </div>
                    <div class="panel-body">

                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false
                        ]); ?>

                        <?= $form->field($model, 'username')->textInput(['placeholder' => $model->getAttributeLabel('username')])?>
                        <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')]) ?>
                        <?= $form->field($model, 'rememberMe')->checkbox() ?>
                        <?php if (LoginAttempt::doesExitAcceptableLimit()): ?>
                            <div class="form-group">
                                <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                                    'template' => '<div class="row">
                                                       <div class="col-md-5"><a href="#">{image}</a></div>
                                                       <div class="col-md-7">{input}</div>
                                                       <div class="col-md-12 hint-block">' . Yii::t('app', 'Please click on the code to change it.') .'</div>
                                                       <div class="clearfix"></div>
                                                   </div>',
                                    'options' => ['placeholder' => Yii::t('app', 'Enter verification code'), 'class' => 'form-control'],
                                ]) ?>
                            </div>
                        <?php endif ?>

                        <div class="form-group col-xs-10 col-xs-offset-1">
                            <?= Html::submitButton(Yii::t('app', 'Sign In'), ['class' => 'btn btn-lg btn-primary btn-block', 'name' => 'login-button']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
  
</div>
