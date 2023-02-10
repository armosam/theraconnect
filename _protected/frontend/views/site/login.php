<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use common\models\forms\LoginForm;
use common\models\LoginAttempt;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model LoginForm */

//$this->params['breadcrumbs'][] = $this->title;

?>

<div class="site-login">

    <div class="container">
        <div class="row">
            <div style="max-width: 368px; margin: 30px auto">
                <div class="well panel panel-default">
                    <div class="panel panel-heading text-center" style="background-color: #e4e4e4;color: #005d73; font-size: large;">
                        <strong><?= $this->title ?></strong>
                    </div>
                    <div class="panel-body">
                        <?php $form = ActiveForm::begin([
                            'id' => 'login-form',
                            'options' => ['role'=>'form'],
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'fieldConfig' => [
                                'options' => [
                                    'tag' => 'span',
                                ],
                            ],
                        ]); ?>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <?= $form->field($model, 'username',[
                                        'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-user emerald"></span></span>{input}</div>{error}',
                                        'inputOptions' => [
                                            'placeholder' => Yii::t('app', 'User'),
                                            'class'=>'form-control data-hj-whitelist',
                                            'autocomplete'=>'off',
                                            //'autofocus' => true
                                        ]])
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'password', [
                                        'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-key emerald"></span></span>{input}</div>{error}',
                                        'inputOptions' => [
                                            'placeholder' => Yii::t('app', 'Password'),
                                            'class'=>'form-control',
                                            'autocomplete'=>'off',
                                        ]])->input('password')
                                    ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                                </div>
                                <?php if (LoginAttempt::doesExitAcceptableLimit()): ?>
                                    <div class="form-group">
                                        <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                                            'template' => '<div class="row">
                                                    <div class="col-md-5"><a href="#">{image}</a></div>
                                                    <div class="col-md-7">{input}</div><div class="clearfix"></div>
                                                    <div class="col-md-12 hint-block">' . Yii::t('app', 'Please click on the code to change it.') .'</div>
                                                </div>',
                                            'options' => [
                                                'placeholder' => Yii::t('app', 'Enter verification code'),
                                                'class' => 'form-control',
                                                'autocomplete' => 'off',
                                                'autofill' => 'off',
                                                'autocorrect' => 'off',
                                                'autocapitalize' => 'none',
                                                'spellcheck' => 'false'
                                            ],
                                        ]) ?>
                                    </div>
                                <?php endif ?>
                                <div class="form-group col-xs-10 col-xs-offset-1">
                                    <?= Html::submitButton(Yii::t('app', 'Sign In'), ['class' => 'btn btn-lg btn-primary btn-block', 'name' => 'login-button']) ?>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12 col-md-10  col-md-offset-1 hidden">
                                <?= Html::a('<span class="fa fa-facebook"></Span> '.Yii::t('app', 'Sign in with Facebook'), ['/login/facebook'], ['class' => 'btn btn-block btn-default btn-facebook']) ?>
                                <?= Html::a('<span class="fa fa-google"></Span> '.Yii::t('app', 'Sign in with Google'), ['/login/google'], ['class' => 'btn btn-block btn-default btn-google']) ?>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                    <div class="hint-block small">
                        <div><?= Yii::t('app', 'Are you Home Health Care Agency? {sign-up-as-customer}', ['sign-up-as-customer' => Html::a(Yii::t('app', 'Sign up'), ['site/sign-up-customer'])]) ?></div>
                        <div><?= Yii::t('app', 'Are you therapist? {join-as-provider}', ['join-as-provider' => Html::a(Yii::t('app', 'Join as Therapist'), ['join/application-form'])]) ?></div>
                        <div><?= Yii::t('app', 'Forgot your password? {reset-it}', ['reset-it' => Html::a(Yii::t('app', 'Reset Password'), ['site/request-password-reset'])]) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>