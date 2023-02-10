<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use kartik\widgets\Select2;
use kartik\widgets\FileInput;
use nenad\passwordStrength\PasswordInput;
use borales\extensions\phoneInput\PhoneInput;
use common\models\UsCity;
use common\models\User;
use common\widgets\ISO639\Language;

/** @var $this yii\web\View */
/** @var $model common\models\User */
/** @var $form yii\widgets\ActiveForm */
/** @var $terminationModel User */

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="profile-update-form">

    <?php $form = ActiveForm::begin([
        'id' => 'user_profile_form',
        'options'=>[
            'autocomplete' => 'off',
            'enctype'=>'multipart/form-data'
        ]
    ]); ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="col-lg-12">
                <?= $form->field($model->avatar, 'upload_file')->widget(
                    FileInput::class, [
                    'options' => ['accept' => 'image/*'],
                    'pluginOptions' => [
                        'previewFileType' => 'image',
                        //    'uploadUrl' => Url::to(['../../../uploads']), // This is for ajax apload
                        'allowedFileExtensions'=>['jpg', 'gif', 'png', 'jpeg', 'bmp'],
                        'showUpload' => false,
                        'initialPreview' => [ Html::img('/site/avatar?id='.$model->id, ['width'=>200]) ],
                        'maxFileSize' => 10000,
                        'showCaption' => false,
                        'showCancel' => false,
                        'showRemove' => false,
                        //'msgSizeTooLarge' => Yii::t('app', 'File "{name}" ({size} KB) exceeds maximum allowed upload size of {maxSize} KB.'),
                        //'dropZoneTitle' => Yii::t('app', 'Drag & drop files here ...'),
                        //'cancelLabel' =>  Yii::t('app', 'Cancel'),
                        //'removeLabel' =>  Yii::t('app', 'Delete'),
                        //'browseLabel' =>  Yii::t('app', 'Browse')
                    ]
                ])->label(($model->role->item_name === User::USER_CUSTOMER) ? Yii::t('app', 'Upload your agency logo.') : Yii::t('app', 'Upload your best photo as avatar.')) ?>

                <div class="alert alert-info small" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Info:</span>
                    <?= Yii::t('app', 'Please upload images in size {size} or bigger with {ratio} ratio up to {size_mb}Mb. Otherwise it will be cropped automatically.', [
                        'size' => (Yii::$app->params['avatarImage']['width'].' X '.Yii::$app->params['avatarImage']['height']),
                        'size_mb' => Yii::$app->params['avatarImage']['size_mb'],
                        'ratio' => Yii::$app->params['avatarImage']['ratio']]
                    ) ?>
                </div>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'username')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'email')->textInput(['autocomplete'=>'off']) ?>
                <?= $model->verificationCheck('email', true) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'phone1')->widget(PhoneInput::class, [
                    'jsOptions' => [
                        'allowExtensions' => false,
                        'nationalMode' => true,
                        'preferredCountries' => ['us'],
                        'onlyCountries' => ['us', 'ca'/*, 'fr', 'de', 'mx', 'gb', 'ru'*/],
                    ]
                ]) ?>
                <?= $model->verificationCheck('phone1', true) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'phone2')->widget(PhoneInput::class, [
                    'jsOptions' => [
                        'allowExtensions' => false,
                        'nationalMode' => true,
                        'preferredCountries' => ['us'],
                        'onlyCountries' => ['us', 'ca'/*, 'fr', 'de', 'mx', 'gb', 'ru'*/],
                    ]
                ]) ?>
                <?= $model->verificationCheck('phone2', true) ?>
            </div>
            <div class="col-lg-12">
                <?= $form->field($model, 'password', [
                    'inputOptions' => ['style' => 'z-index: 1;', 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'New password ( if you want to change it )')]
                ])->widget(PasswordInput::class, ['language' => substr(Yii::$app->language, 0, 2)]) ?>
            </div>

        </div>
        <div class="col-lg-6">
            <div class="col-lg-6">
                <?= $form->field($model, 'first_name')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'last_name')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <?php if (!$model->isNewRecord && $model->role->item_name === User::USER_CUSTOMER): ?>
                <div class="col-lg-6"><?= $form->field($model, 'agency_name', ['enableAjaxValidation' => true])->textInput(['autocomplete'=>'off']) ?></div>
                <div class="col-lg-6"><?= $form->field($model, 'rep_position', ['enableAjaxValidation' => true])->textInput(['autocomplete'=>'off']) ?></div>
            <?php endif; ?>
            <div class="col-lg-12">
                <?= $form->field($model, 'gender')->dropDownList(User::getGenderList(), ['prompt' => Yii::t('app', 'Not Selected')]) ?>
            </div>
            <div class="col-lg-12">
                <?= $form->field($model, 'address')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'city')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'state')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <div class="col-lg-3">
                <?= $form->field($model, 'zip_code')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <?php if (!$model->isNewRecord && $model->role->item_name === User::USER_PROVIDER): ?>
                <div class="col-lg-12"><?= $form->field($model, 'service_rate')->textInput(['maxlength' => true]) ?></div>
                <div class="col-lg-12">
                    <?= $form->field($model, 'language')->widget(Select2::class, [
                        'data' => Language::allEnglish(),
                        'size' => Select2::MEDIUM,
                        'showToggleAll' => false,
                        'options' => ['placeholder' => 'Select your speaking languages', 'multiple' => true],
                        'pluginOptions' => [
                            'allowClear' => true,
                            //'tags' => true
                        ],
                    ]) ?>
                </div>
                <div class="col-lg-12">
                    <?= $form->field($model, 'covered_county')->widget(Select2::class, [
                        'data' => UsCity::getCounties('CA'),
                        'size' => Select2::MEDIUM,
                        'theme' => Select2::THEME_KRAJEE,
                        'showToggleAll' => false,
                        'options' => [
                            'multiple' => true,
                            'placeholder' => Yii::t('app', 'Covered County'),
                            'class' => 'form-control'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumResultsForSearch' => 'Infinity'
                        ],
                        /*'pluginEvents' => [
                            'select2:select' => new \yii\web\JsExpression("function(e) { populateCityByCountyIdAjax(e.params.data.id); }"),
                            'select2:unselect' => new \yii\web\JsExpression("function(e) { populateCityByCountyIdAjax(null); }")
                        ],*/
                    ]) ?>
                </div>
                <div class="col-lg-12">
                    <?= $form->field($model, 'covered_city')->widget(Select2::class, [
                        'data' => UsCity::getStateCities('CA'),
                        'size' => Select2::MEDIUM,
                        'theme' => Select2::THEME_KRAJEE,
                        'showToggleAll' => false,
                        'options' => [
                            'multiple' => true,
                            'placeholder' => Yii::t('app', 'Covered City'),
                            'class' => 'form-control'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        /*'pluginEvents' => [
                            'select2:select' => new \yii\web\JsExpression("function(e) { getCountyByCityIdAjax(e.params.data.id); }"),
                        ],*/
                    ]) ?>
                </div>
            <?php endif; ?>

            <div class="col-lg-4">
                <?= $form->field($model, 'emergency_contact_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'emergency_contact_number')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => '+(999) 999-9999',
                        'removeMaskOnSubmit' => true
                    ]])->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'emergency_contact_relationship')->textInput(['maxlength' => true]) ?>
            </div>

            <!--<div class="col-lg-6">
                <?/*= $form->field($model, 'lat')->textInput(['autocomplete'=>'off']) */?>
            </div>
            <div class="col-lg-6">
                <?/*= $form->field($model, 'lng')->textInput(['autocomplete'=>'off']) */?>
            </div>-->
            <!--<div class="col-lg-12">
                <?/*= $form->field($model, 'timezone')->textInput(['autocomplete'=>'off']) */?>
            </div>-->

            <div class="col-lg-12">
                <?= $form->field($model, 'website_address')->textInput(['autocomplete' => 'off']) ?>
            </div>
            <div class="col-lg-12">
                <?= $form->field($model, 'note')->textarea(['maxLength' => true, 'rows' => 4]) ?>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), [
                    'name' =>'update',
                    'class' => 'btn btn-success btn-save',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to change your data?').Html::tag('div', Yii::t('app', 'Please be informed that when you change Email Address, Primary or Secondary Phone Number, then you need to verify a new entered data.'), ['class' => 'text text-danger']),
                        'method' => 'post',
                    ],
                ]) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

    <?php $termination_form = ActiveForm::begin([
        //'id' => 'user-termination',
        //'action' => \yii\helpers\Url::to(['/profile/terminate']),
        'enableClientValidation' => true,
        'options' => [],
    ]); ?>

    <div class="text-right pull-right invisible">
        <?= $termination_form->field($terminationModel, 'termination_reason')
            ->dropDownList($terminationModel::getUserTerminationReasonList(), [
                    'options' => ['placeholder' => Yii::t('app', 'Select Reason')]
            ])->label(false) ?>
        <?= Html::submitButton(Yii::t('app', 'Terminate My Account'), [
            'name' =>'terminate',
            'class' => 'btn btn-link btn-sm',
            'data-confirm' => Yii::t('app', 'After termination you will be logged out automatically and you will not be able to log in and use your account.'),
        ]) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- profile-update -->
