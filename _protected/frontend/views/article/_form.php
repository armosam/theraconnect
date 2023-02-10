<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\dialog\Dialog;
use kartik\date\DatePicker;
use mihaildev\ckeditor\CKEditor;
use common\helpers\ArrayHelper;
use common\models\Language;

/* @var $this yii\web\View */
/* @var $model common\models\Article */
/* @var $form yii\widgets\ActiveForm */

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="article-form">

    <?php $form = ActiveForm::begin(['id' => 'form-article', 'options'=>['enctype'=>'multipart/form-data']]) ?>

    <?= $form->field($model, 'embed_content')->textarea(['col' => 10, 'rows' => 5]) ?>
    <div class="alert alert-info small" role="alert">
        <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
        <span class="sr-only">Info:</span>
        <?= Yii::t('app', 'You can put in the embedded content field any media content for example IFRAME content from youtube.') ?>
    </div>

    <?= $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'summary')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'content')->widget(CKEditor::class,
        ['editorOptions' => [ 'preset' => 'full', 'inline' => false]]) ?>

    <div class="col-xs-12 col-sm-6">
    <?= $form->field($model, 'start_date')->widget(
        DatePicker::class, [
        'language' => 'en',
        'name' => 'start_date',
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'options' => [
            'autocomplete' => 'off',
        ],
        'pluginOptions' => [
            'maxViewMode' => 1,
            'todayHighlight' => true,
            'autoclose'=>true,
            'startDate' => 'new Date()',
        ]
    ]) ?>
    </div>
    <div class="col-xs-12 col-sm-6">
    <?= $form->field($model, 'end_date')->widget(
        DatePicker::class, [
        'language' => 'en',
        'name' => 'end_date',
        'type' => DatePicker::TYPE_COMPONENT_APPEND,
        'pluginOptions' => [
            'maxViewMode' => 1,
            'todayHighlight' => true,
            'autoclose'=>true,
            'startDate' => 'new Date()',
        ]
    ]) ?>
    </div>

    <div class="row">
    <div class="col-lg-6">
        <div class="switchers">
            <?= $form->field($model, 'send_email')->checkbox(['data-size' => 'small', 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No'), 'disabled' => !Yii::$app->user->can(\common\models\User::USER_ADMIN)], true) ?>
        </div>
        <?= $form->field($model, 'status')->dropDownList($model->articleStatusList) ?>

        <?= $form->field($model, 'category')->dropDownList($model->articleCategoryList) ?>
    </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'data' => [
                    'confirm' => $model->isNewRecord ? Yii::t('app', "Are you sure you want to create new record?") : Yii::t('app', "Are you sure you want to change this record?"),
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['article/index'], ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("

    $(document).ready(function(){
        $('.switchers :checkbox').bootstrapSwitch();
    })
");
?>
