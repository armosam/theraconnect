<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\MaskedInput;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use kartik\dialog\Dialog;
use common\models\User;
use common\models\Service;
use common\models\UsCity;
use common\widgets\ISO639\Language;

/* @var $this yii\web\View */
/* @var $model common\models\Prospect */
/* @var $form ActiveForm */

Dialog::widget(['overrideYiiConfirm' => true]);

$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?=$this->title ?></h1>

<div class="join-form box box-primary">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <div class="box-body table-responsive">
        <div class="col-lg-6"><?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-6"><?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-6"><?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-6"><?= $form->field($model, 'phone_number')->widget(MaskedInput::class, [
                'clientOptions' => [
                    'alias' => '+(999) 999-9999',
                    'removeMaskOnSubmit' => true
                ]])->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-12"><?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-6"><?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-2"><?= $form->field($model, 'state')->textInput(['maxlength' => true, 'value' => 'CA']) ?></div>
        <div class="col-lg-2"><?= $form->field($model, 'zip_code')->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-2"><?= $form->field($model, 'country')->textInput(['maxlength' => true, 'value' => 'USA']) ?></div>
        <div class="col-lg-12">
            <?= $form->field($model, 'service_id')->dropDownList(Service::serviceList(), ['multiple' => false, 'size' => 1, 'prompt'=>'-- Select Service --']) ?>
        </div>
        <div class="col-lg-4"><?= $form->field($model, 'license_type')->dropDownList(User::getTitleList(), ['prompt'=>'-- Select License Type --']) ?></div>
        <div class="col-lg-4"><?= $form->field($model, 'license_number')->textInput(['maxlength' => true]) ?></div>
        <div class="col-lg-4"><?= $form->field($model, 'license_expiration_date')->widget(MaskedInput::class, [
                'clientOptions' => [
                    'alias' => 'mm/dd/yyyy',
                    "placeholder" => "__/__/____",
                    "separator" => "/"
                ]])->textInput() ?>
        </div>
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
        <div class="col-lg-12"><?= $form->field($model, 'note')->textarea(['rows' => 5])->hint('Please give more detail about your coverage, language or preferences.')  ?></div>
        <div class="col-lg-12">
            <div class="text-left">
                <?= Html::a(Yii::t('app', 'Terms of Service'), ['site/terms-of-service'], ['target' => '_blank']) ?>,
                <?= Html::a(Yii::t('app', 'Privacy Policy'), ['site/privacy-policy'], ['target' => '_blank']) ?>
            </div>
            <br>
            <?= $form->field($model, 'agreed', [
                'template' => '{input}{label}{error}{hint}'
            ])->widget(SwitchInput::class, [
                'type' => SwitchInput::CHECKBOX,
                'inlineLabel' => false,
                'pluginOptions' => [
                    //'size' => 'normal',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                    'onText' => Yii::t('app', 'Yes'),
                    'offText' => Yii::t('app', 'No'),
                ]
            ])->label(Yii::t('app', 'I have read and agree with Terms of Service and Privacy Policy.'), ['style'=>'font-size:14px']) ?>
        </div>
    </div>
    <br>
    <div class="box-footer">
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app', 'Submit Application'), [
                'class' => 'btn btn-success',
                'data' => [
                    'confirm' => Yii::t('app', "Are you sure you want to submit your application?"),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-danger']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div><!-- join-form -->


<?php


$_js = <<< INITJS

/**
 * This method used on join application form to
 * get list of cities by selecting counties
 */
function populateCityByCountyIdAjax(county_code) 
{    
console.log(county_code);
    var select = $('#prospect-county_code');
    if(select.length) {
        var city_code = select.val() || null;
        select.empty().trigger('change');
        $.ajax({
            type: 'POST',
            url: '/join/json-get-cities-by-county',
            data: {
                id: county_code
            },
            dataType: 'json',
            success: function (data) {
                var select2Options = {
                    size: "md",
                    theme: "krajee",
                    width: "100%",
                    showToggleAll: false,
                    allowClear: true,
                    minimumResultsForSearch: "Infinity",
                    multiple: false,
                    placeholder: data.placeholder,
                    class: "form-control"
                };
                select2Options.data = data.results;
                select.select2(select2Options);
                select.val(city_code).trigger('change');
            }
        });
    }
}

/**
 * This method not used yet
 * it was for back loading of counties by selecting cities
 */
function getCountyByCityIdAjax(city_code) {
    var select = $('#prospect-county_code');
    if(select.length) {
        var selected_county = select.val();
        $.ajax({
            type: 'POST',
            url: '/site/json-get-service-category-by-service-id',
            data: {
                id: city_code
            },
            dataType: 'json',
            success: function (county_code) {
                if (selected_county != county_code) {
                    select.val(county_code).trigger('change');
                    //populateCityByCountyIdAjax(county_code, city_code);
                }
            }
        });
    }
}

INITJS;

$this->registerJs(
    new \yii\db\Expression($_js),
    View::POS_READY,
    "init_dep_drop"
);

?>
