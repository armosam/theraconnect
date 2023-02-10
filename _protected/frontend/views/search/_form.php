<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\widgets\Typeahead;
use common\helpers\ArrayHelper;
use common\helpers\ConstHelper;
use common\models\Country;

/**
 * @var yii\web\View $this
 * @var common\models\searches\RequestSearch $searchModel
 * @var ActiveForm $form
 */
?>

<?php $form = ActiveForm::begin([
    'id' => 'search-provider-form',
    'action' => Url::to(['search/index']),
    'method' => 'get',
    'layout' => 'inline',
    'options' => [
        'class' => 'form-inline',
        'autocomplete' => 'off',
        'autofill' => 'off',
        'autocorrect' => 'off',
        'autocapitalize' => 'none',
        'spellcheck' => 'false'
    ]
]); ?>
    <div class = "text-right hidden-xs">
        <?= $form->field($searchModel, 'patient_ordering')->dropDownList(ConstHelper::getProviderSearchOrderByList(), [
            'class' => 'form-control',
            'onchange'=>'this.form.submit()'
        ])->label(false) ?>
        <br/>
    </div>

    <?= $form->field($searchModel, 'patient_location')->widget(Typeahead::class, [
        'defaultSuggestions' => [Yii::t('app', 'Los Angeles, CA')],
        'options' => [
            'placeholder' => Yii::t('app', 'City or Address'),
            'autocomplete' => 'off',
            'class' => 'form-control'
        ],
        'pluginOptions' => [
            'highlight'=>true
        ],
        'dataset' => [
            [
                'local' => ArrayHelper::getColumn(Country::find()->all(), 'country_name'),
                'limit' => 4
            ]
        ]
    ])->label(false) ?>

    <?= $form->field($searchModel, 'patient_distance')->dropDownList(ConstHelper::getServiceRadiusList(), [
        'class' => 'form-control',
        'autocomplete' => 'off'
    ])->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
