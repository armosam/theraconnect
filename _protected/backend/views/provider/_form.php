<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\widgets\Select2;
use kartik\widgets\FileInput;
use common\models\User;
use common\models\UsCity;
use common\models\Service;
use common\helpers\ConstHelper;
use common\widgets\ISO639\Language;
use nenad\passwordStrength\PasswordInput;
use borales\extensions\phoneInput\PhoneInput;

/* @var yii\web\View $this */
/* @var yii\bootstrap\ActiveForm $form */
/* @var common\models\User $model */
/* @var common\rbac\models\Role $role */
/* @var common\models\UserAvatar $userAvatar */
/* @var common\models\UserService $userService */
?>
<div class="provider-form box box-primary">

    <?php $form = ActiveForm::begin([
        'id' => 'form-user',
        'options'=>[
            'autocomplete' => 'off',
            'enctype'=>'multipart/form-data'
        ]
    ]); ?>

    <div class="row">
        <div class="col-lg-6">
            <div class="col-lg-12">
                <?= $form->field($userAvatar, 'upload_file')->widget(FileInput::class, [
                    'options' => ['accept' => 'image/*'],
                    'pluginOptions' => [
                        'previewFileType' => 'image',
                        //'uploadUrl' => Url::to(['../../../uploads']), // This is for ajax upload
                        'allowedFileExtensions' => ['jpg', 'jpeg', 'gif', 'png', 'bmp'],
                        'showUpload' => false,
                        'initialPreview' => [Html::img(Yii::$app->urlManagerToFront->createUrl(['/site/avatar', 'id' => $model->id,
                            'w' => Yii::$app->params['avatarImage']['width'],
                            'q' => Yii::$app->params['avatarImage']['quality'],
                            's' => Yii::$app->params['picturePreferredSourceFileSystem']
                        ]), ['width' => 200])],
                    ]])->label(Yii::t('app', 'Upload Therapist Photo')) ?>

                <div class="alert alert-info small" role="alert">
                    <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                    <span class="sr-only">Info:</span>
                    <?= Yii::t('app', 'Please upload images in size {size} or bigger with {ratio} ratio up to {size_mb}Mb. Otherwise it will be cropped automatically.', [
                            'size' => (Yii::$app->params['avatarImage']['width'] . ' X ' . Yii::$app->params['avatarImage']['height']),
                            'size_mb' => Yii::$app->params['avatarImage']['size_mb'],
                            'ratio' => Yii::$app->params['avatarImage']['ratio']]
                    ) ?>
                </div>
            </div>

            <?php if ($model->isNewRecord): ?>
                <div class="col-lg-6"><?= $form->field($model, 'username', ['enableAjaxValidation' => true]) ?></div>
            <?php else: ?>
                <div class="col-lg-6"><?= $form->field($model, 'username', ['enableAjaxValidation' => true])
                        ->textInput(['readonly' => true])->label(Yii::t('app', 'Username')) ?></div>
            <?php endif ?>

            <div class="col-lg-6"><?= $form->field($model, 'email', ['enableAjaxValidation' => true]) ?></div>

            <div class="col-lg-6">
                <?= $form->field($model, 'phone1')->widget(PhoneInput::class, [
                    'jsOptions' => [
                        'allowExtensions' => false,
                        'nationalMode' => true,
                        'preferredCountries' => ['us'],
                        'onlyCountries' => ['us', 'ca'/*, 'fr', 'de', 'mx', 'gb', 'ru'*/],
                    ]
                ]) ?>
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
            </div>
            <div class="col-lg-12">
                <?= $form->field($model, 'password', [
                    'inputOptions' => ['style' => 'z-index: 1;', 'autocomplete' => 'off', 'placeholder' => Yii::t('app', 'New password')]
                ])->widget(PasswordInput::class, ['language' => substr(Yii::$app->language, 0, 2)]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'status')->dropDownList(User::getUserStatusList(), ['prompt'=>'-- Select Status --']) ?>
            </div>
        </div>
        <div class="col-lg-6">

            <div class="col-lg-6">
                <?= $form->field($model, 'first_name')->textInput(['autocomplete'=>'off']) ?>
            </div>

            <div class="col-lg-6">
                <?= $form->field($model, 'last_name')->textInput(['autocomplete'=>'off']) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'gender')->dropDownList($model->getGenderList(), ['prompt'=>'-- Select Gender --']) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'address')->textInput(['autocomplete' => 'off']) ?>
            </div>

            <div class="col-lg-4">
                <?= $form->field($model, 'city')->textInput(['autocomplete' => 'off']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'state')->textInput(['autocomplete' => 'off']) ?>
            </div>
            <div class="col-lg-4">
                <?= $form->field($model, 'zip_code')->textInput(['autocomplete' => 'off']) ?>
            </div>

            <?= $form->field($role, 'item_name')->hiddenInput()->label(false) ?>

            <div class="col-lg-5"><?= $form->field($userService, 'service_id')->dropDownList(Service::serviceList(), ['prompt' => '-- Select Service --']) ?></div>

            <div class="col-lg-4"><?= $form->field($model, 'title')->dropDownList(User::getTitleList(), ['prompt' => '-- Select Title --']) ?></div>

            <div class="col-lg-3"><?= $form->field($model, 'service_rate')->textInput(['maxlength' => true]) ?></div>

            <div class="col-lg-12">
                <?= $form->field($model, 'language')->widget(Select2::class, [
                    'data' => Language::allEnglish(),
                    'size' => Select2::MEDIUM,
                    'showToggleAll' => false,
                    'options' => [
                        //'placeholder' => 'Select your speaking languages',
                        'multiple' => true
                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                        //'tags' => true
                    ],
                ]) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'covered_county')->widget(Select2::class, [
                    'data' => UsCity::getCounties('CA'),
//                        'size' => Select2::MEDIUM,
                    'theme' => Select2::THEME_KRAJEE,
                    'showToggleAll' => false,
                    'options' => [
                        'multiple' => true,
                        //'placeholder' => Yii::t('app', 'Covered County'),
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
                        //'placeholder' => Yii::t('app', 'Covered City'),
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

            <div class="col-lg-4"><?= $form->field($model, 'emergency_contact_name')->textInput(['maxlength' => true]) ?></div>

            <div class="col-lg-4"><?= $form->field($model, 'emergency_contact_number')->widget(MaskedInput::class, [
                    'clientOptions' => [
                        'alias' => '+(999) 999-9999',
                        'removeMaskOnSubmit' => true
                    ]])->textInput(['maxlength' => true]) ?>
            </div>

            <div class="col-lg-4"><?= $form->field($model, 'emergency_contact_relationship')->textInput(['maxlength' => true]) ?></div>

            <div class="col-lg-6">
                <?= $form->field($model, 'website_address')->textInput(['autocomplete' => 'off']) ?>
            </div>

            <div class="col-lg-6">
                <?= $form->field($model, 'timezone')->dropDownList(ConstHelper::getTimeZoneList()) ?>
            </div>

            <div class="col-lg-12">
                <?= $form->field($model, 'note')->textarea(['rows' => 4]) ?>
            </div>
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-lg-12">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), [
                        'class' => $model->isNewRecord ? 'btn btn-success btn-flat' : 'btn btn-primary btn-flat',
                        'data' => [
                            'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create a new record?") : Yii::t('app', "Are you sure you want to update this record?"),
                            'method' => 'post',
                        ]
                    ]) ?>
                    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning btn-flat hidden-xs']) ?>
                    <?= Html::a(Yii::t('app', 'Close'), ['index'], ['class' => 'btn btn-danger btn-flat']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <br>
</div>
