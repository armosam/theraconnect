<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use nenad\passwordStrength\PasswordInput;
use frontend\models\forms\SignUpForm;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model SignUpForm */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-sign-up-provider">

    <div class="pull-right hidden-xs">
        <p class="text-right" style="width: 300px"><?= Yii::t('app', 'Become a member in our system.')?><br><?= Yii::t('app', 'Just sign-up using following button.')?></p>
        <p class="text-right"><?=Html::a(Yii::t('app', 'Sign up as a customer'), ['site/sign-up'], ['class'=>'btn btn-md btn-success']) ?></p>
        <br class="clearfix">
    </div>

    <div class="well col-lg-4 col-lg-offset-4">

        <div class="panel panel-heading text-center" style="background-color: #e4e4e4;color: #005d73; font-size: large;">
            <?= Yii::t('app', 'Sign up as a specialist') ?>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'id' => 'form-sign-up',
                'options' => ['role'=>'form'],
                'enableAjaxValidation' => true,
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
                        <?= $form->field($model, 'email',[
                            'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-envelope emerald"></span></span>{input}</div>{error}',
                            'inputOptions' => [
                                'placeholder' => Yii::t('app', 'Email'),
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
                            ]])->widget(PasswordInput::class, ['language' => substr(Yii::$app->language, 0, 2)])
                        ?>
                    </div>
                    <div class="form-group">
                        <div class="small text-center">
                            <?= Html::a(Yii::t('app', 'Terms of Service'), 'terms-of-service', ['target' => '_blank']) ?>,
                            <?= Html::a(Yii::t('app', 'Privacy Policy'), 'privacy-policy', ['target' => '_blank']) ?>
                        </div>
                        <br>
                        <?= $form->field($model, 'agreed', [
                            'template' => '{input}{label}{error}{hint}'
                        ])->widget(\kartik\widgets\SwitchInput::class, [
                            'type' => SwitchInput::CHECKBOX,
                            'inlineLabel' => false,
                            'pluginOptions' => [
                                'size' => 'mini',
                                'onColor' => 'success',
                                'offColor' => 'danger',
                                'onText' => Yii::t('app', 'Yes'),
                                'offText' => Yii::t('app', 'No'),
                            ]
                        ])->label(Yii::t('app', 'I have read and agree with Terms of Service and Privacy Policy.'), ['style'=>'font-size:11px']) ?>
                    </div>
                    <div class="form-group col-xs-10 col-xs-offset-1">
                        <?= Html::submitButton(Yii::t('app', 'Sign Up'), ['class' => 'btn btn-lg btn-primary btn-block', 'name' => 'sign-up-button']) ?>
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
        <br>
        <div class="small">
            <span><?= Yii::t('app', 'Do you have an account? {sign-in-here}', ['sign-in-here' => Html::a(Yii::t('app', 'Sign In'), ['site/login'])] ) ?></span>

            <?php if ($model->scenario === \common\models\User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION): ?>
                <div style="color:#666;margin:1em 0">
                    <i>*<?= Yii::t('app', 'We will send you an email with account activation link.') ?></i>
                </div>
            <?php endif ?>
        </div>

    </div>
</div>