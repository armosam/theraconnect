<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use borales\extensions\phoneInput\PhoneInput;
use nenad\passwordStrength\PasswordInput;
use frontend\models\forms\SignUpForm;
use kartik\switchinput\SwitchInput;
use common\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model SignUpForm */

//$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-sign-up-customer">

    <div class="well panel panel-default" style="margin: 30px auto;max-width: 450px">

        <div class="panel panel-heading text-center" style="background-color: #e4e4e4;color: #005d73; font-size: large;">
            <strong><?= $this->title ?></strong>
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
                        <?= $form->field($model, 'first_name',[
                            'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-user emerald"></span></span>{input}</div>{error}',
                            'inputOptions' => [
                                'placeholder' => Yii::t('app', 'First Name'),
                                'class'=>'form-control data-hj-whitelist',
                                'autocomplete'=>'off',
                                //'autofocus' => true
                            ]])
                        ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'last_name',[
                            'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-user emerald"></span></span>{input}</div>{error}',
                            'inputOptions' => [
                                'placeholder' => Yii::t('app', 'Last Name'),
                                'class'=>'form-control data-hj-whitelist',
                                'autocomplete'=>'off',
                                //'autofocus' => true
                            ]])
                        ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'agency_name',[
                            'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-user emerald"></span></span>{input}</div>{error}',
                            'inputOptions' => [
                                'placeholder' => Yii::t('app', 'Agency Name'),
                                'class'=>'form-control data-hj-whitelist',
                                'autocomplete'=>'off',
                                //'autofocus' => true
                            ]])
                        ?>
                    </div>
                    <div class="form-group">
                        <?= $form->field($model, 'phone1', [
                            'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-phone emerald"></span></span>{input}</div>{error}',
                        ])->widget(PhoneInput::class, [
                            'jsOptions' => [
                                'formatOnDisplay' => false,
                                'autoHideDialCode' => true,
                                'separateDialCode' => false,
                                'nationalMode' => false,
                                'allowDropdown' => false,
                                'placeholderNumberType' => 'FIXED_LINE_OR_MOBILE',
                                'initialCountry' => 'us',
                                'preferredCountries' => ['us'],
                                'onlyCountries' => ['us'/*, 'ca', 'fr', 'de', 'mx', 'gb', 'ru'*/],
                            ]
                        ])->label(false) ?>
                    </div>
                    <hr>
                    <div class="form-group">
                        <?= $form->field($model, 'username',[
                            'template' => '<div class="input-group"><span class="input-group-addon"><span class="fa fa-user emerald"></span></span>{input}</div>{error}',
                            'inputOptions' => [
                                'placeholder' => Yii::t('app', 'Username'),
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
                                'placeholder' => Yii::t('app', 'Email Address'),
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

                    <div class="form-group form-inline ">
                        <?= $form->field($model, 'agreed', [
                            'template' => '{input}{label}{error}{hint}'
                        ])->widget(\kartik\widgets\SwitchInput::class, [
                            'type' => SwitchInput::CHECKBOX,
                            'pluginOptions' => [
                                'size' => 'small',
                                'onColor' => 'success',
                                'offColor' => 'danger',
                                'onText' => Yii::t('app', 'Yes'),
                                'offText' => Yii::t('app', 'No'),
                            ],
                        ])->label(Yii::t('app', 'I have read and agree.'), [
                            'style' => 'font-size: 14px;padding-left:10px'
                        ])->hint(Yii::t('app', 'Please click on the link {terms} and {policy}. We suggest you read and agree by clicking on the switch above.', [
                            'terms' => Html::a(Yii::t('app', 'Terms of Service'), 'terms-of-service', ['target' => '_blank']),
                            'policy' => Html::a(Yii::t('app', 'Privacy Policy'), 'privacy-policy', ['target' => '_blank'])
                        ]), ['class' => 'small hint-block']) ?>
                    </div>
                    <br>
                    <div class="form-group col-xs-10 col-xs-offset-1">
                        <?= Html::submitButton(Yii::t('app', 'Sign Up'), ['class' => 'btn btn-lg btn-primary btn-block', 'name' => 'sign-up-button']) ?>
                    </div>
                </div>
            </div>

            <?php if (1==0): ?>
                <div class="row">
                    <div class="col-sm-12 col-md-10 col-md-offset-1 hidden">
                        <?= Html::a('<span class="fa fa-facebook"></span> '.Yii::t('app', 'Sign in with Facebook'), ['/login/facebook'], ['class' => 'btn btn-block btn-default btn-facebook']) ?>
                        <?= Html::a('<span class="fa fa-google"></span> '.Yii::t('app', 'Sign in with Google'), ['/login/google'], ['class' => 'btn btn-block btn-default btn-google']) ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="hint-block small">
            <div><?= Yii::t('app', 'Do you have an account? {sign-in-here}', ['sign-in-here' => Html::a(Yii::t('app', 'Sign In'), ['site/login'])] ) ?></div>

            <?php if ($model->scenario === User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION): ?>
                <b><?= Yii::t('app', 'We are going to send you an email with an activation link.') ?></b>
            <?php endif ?>
        </div>

    </div>
</div>