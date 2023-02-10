<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use common\models\Order;
use common\helpers\CssHelper;
use common\widgets\ajax\modal\ModalAjaxWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="provider-order-index box box-primary">
    <div class="box-header with-border">

    </div>
    <div class="box-body table-responsive no-padding">

        <?= GridView::widget([
            'id' => 'order-grid',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'resizableColumns' => false,
            'responsiveWrap' => false,
            'responsive' => true,
            'export' => false,
            'pjax' => false,
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:3%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                /*[
                    'attribute' => 'order_number',
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center hidden-xs'],
                    'headerOptions' => ['class' => 'small text-center kv-align-middle hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs'],
                ],*/
                [
                    'attribute' => 'patient_name',
                    'label' => 'MR#, Patient Name, Birth Date, Address',
                    'value' => function($model) {
                        /** @var Order $model */
                        $result = '#'.$model->patient->patient_number . ' ';
                        $result .= ($model->patient->patientFullName ?? '')  . ' ';
                        $result .= Html::BeginTag('span', ['class' => 'small']).  ($model->patient->birth_date ?? '') ."<br>";
                        $result .= $model->patient->patientAddress . Html::endTag('span');
                        return $result;
                    },
                    'format' => 'html',
                    'options' =>['style' => 'width:30%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'small kv-align-middle text-center']
                ],
                [
                    'attribute' => 'certification_start_date',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'certification_start_date',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:9%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'certification_end_date',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'certification_end_date',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:9%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'service_frequency',
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::orderFrequencyCss($model->frequency_status) . ' kv-align-middle text-center hidden-xs'];
                    },
                    'headerOptions' => ['class' => 'small kv-align-middle text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'submitted_at',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'submitted_at',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'accepted_at',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'accepted_at',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute'=>'status',
                    'filter' => Order::getStatusList(),
                    'value' => function ($model) {
                        /** @var Order $model */
                        return Order::getStatusList($model->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::orderStatusCss($model->status) . ' kv-align-middle kv-align-center hidden-xs'];
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small kv-align-middle text-center hidden-xs']
                ],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'value' => function ($model, $key, $index, $column) {
                        return GridView::ROW_COLLAPSED;
                    },
                    'expandTitle' => Yii::t('app', 'Show Scheduled Visits'),
                    'collapseTitle' => Yii::t('app', 'Hide Scheduled Visits'),
                    'expandIcon' => '<span class="glyphicon glyphicon-menu-right"></span>',
                    'collapseIcon' => '<span class="glyphicon glyphicon-menu-down"></span>',
                    'header' => '',
                    'expandOneOnly' => true,
                    'enableRowClick' => false,
                    'allowBatchToggle' => false,
                    'detailUrl' => Url::to(['visit/index', '_' => time()]),
                    'detailAnimationDuration' => 'slow',
                    'options' =>['style' => 'width:2%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'kartik-sheet-style'],
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'template' => '{view}&nbsp;{provider-calendar}&nbsp;{allow-transfer}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                                'title'=>Yii::t('app', 'Patient Details'), 'style'=>'margin: 0 3px',
                                'data' => [
                                    'toggle' => 'modal',
                                    'target' => '#provider_order_modal_window',
                                ],
                            ]);
                        },
                        'provider-calendar' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>', ['provider-calendar/index'], [
                                'title'=>Yii::t('app', 'Schedule Calendar'), 'style'=>'margin: 0 3px']);
                        },
                        'allow-transfer' => function ($url, $model, $key) {
                            /** @var $model Order */
                            if($model->status === Order::ORDER_STATUS_ACCEPTED && empty($model->allow_transfer_to) && !empty($model->orderRPT) && $model->orderRPT->id === Yii::$app->user->id) {
                                return Html::a('<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>', ['provider-order/allow-provider-transfer', 'id' => $key], [
                                    'title' => Yii::t('app', 'Transfer Order to another Therapist'), 'style' => 'margin: 0 3px',
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#provider_order_modal_window',
                                    ],
                                ]);
                            }
                            return '';
                        },
                    ],
                    'options' => ['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center'],
                    'headerOptions' => ['class' => 'small kv-align-middle text-center'],
                ], // ActionColumn
            ],
        ]) ?>
    </div>
</div>

<?= ModalAjaxWidget::widget([
    'showBtn' => false,
    'modalClass' => 'ajax-modal-wrap-1100',
    'targetId' => 'provider_order_modal_window',
])?>

<?php
$script = <<< JS
$(function() {
    $('body').on('beforeSubmit', 'form#visit-form, form#supplemental-form, form#route-sheet-form, form#progress-note-form, form#eval-note-form, form#discharge-summary-form, form#discharge-order-form, form#communication-note-form', function () {
        var form = $(this);
        var formData = new FormData(form[0]);
        // return false if form still have some validation errors
        if (form.find('.has-error').length) {
            return false;
        }
        // submit form
        $.ajax({
            url: form.attr('action'),
            type: 'post',
            data: formData,
            success: function (response) {
                if($('#alert_container').length) {
                    // Reload given content
                    $.pjax.reload(response.container, {url: response.url, replace: false, push: false});
                    $('#alert_container').append('<div id="visit_alert" class="alert ' +  (response.success ? 'alert-success' : 'alert-danger') + '"><a class="close" data-dismiss="alert">×</a><span>'+response.message+'</span></div>')
                    $("#visit_alert").fadeTo(5000, 500).slideUp(500, function(){
                        $("#visit_alert").slideUp(500).remove();
                    });
                }
    
                // Clear the form
                form.yiiActiveForm('resetForm');  // Clear the form with the validation messages
                form[0].reset();                  // Remove the values from the form inputs
    
                $('#provider_order_modal_window').modal('hide');  // close modal
                $(".modal-backdrop.in").hide();     // hide modal background that likes to linger after AJAX submission   
            },
            fail: function() {
                console.log('AJAX call failed.');
            },
            cache: false,
            contentType: false,
            processData: false
        });
        return false;
    }).on('submit', 'form#visit-form', function(e){
        console.log('Submitted')
        e.preventDefault();
    });
});
JS;
$this->registerJS($script, View::POS_END);
?>