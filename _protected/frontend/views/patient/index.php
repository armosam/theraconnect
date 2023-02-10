<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use kartik\grid\GridView;
use common\models\User;
use common\models\Patient;
use common\helpers\CssHelper;
use common\helpers\ConstHelper;
use common\widgets\ajax\modal\ModalAjaxWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\PatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="patient-index box box-primary">
    <?php if(User::currentLoggedUser()->isActiveAccount()): ?>
    <div class="box-header with-border">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Add New Patient'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
        </p>
    </div>
    <?php endif; ?>
    <div class="box-body table-responsive no-padding">

    <?= GridView::widget([
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
                'contentOptions' => ['class' => 'kv-align-middle text-center'],
                'headerOptions' => ['class' => 'small text-center']
            ],
            [
                'attribute'=>'patient_number',
                'value' => function ($model) {
                    /** @var Patient $model */
                    return $model->patient_number;
                },
                'options' =>['style' => 'width:10%; white-space: normal;'],
                'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                'headerOptions' => ['class' => 'small text-center hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs']
            ],
            [
                'attribute'=>'full_name',
                'label' => 'Patient Name',
                'value' => function ($model) {
                    /** @var Patient $model */
                    return $model->getPatientFullName();
                },
                'options' =>['style' => 'width:15%; white-space: normal;'],
                'contentOptions' => ['class' => 'kv-align-middle'],
                'headerOptions' => ['class' => 'small text-center']
            ],
            [
                'attribute'=>'address',
                'label' => 'Patient Address',
                'value' => function ($model) {
                    /** @var Patient $model */
                    return $model->patientAddress;
                },
                'options' =>['style' => 'width:35%; white-space: normal;'],
                'contentOptions' => ['class' => 'kv-align-middle'],
                'headerOptions' => ['class' => 'small text-center']
            ],
            [
                'attribute' => 'birth_date',
                'format' => 'date',
                'filter' => MaskedInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'birth_date',
                    'mask' => '99/99/9999',
                    'clientOptions' => [
                        'alias' => 'mm/dd/yyyy',
                        'placeholder' => '__/__/____',
                        'separator' => '/'
                    ]
                ]),
                'options' =>['style' => 'width:10%; white-space: normal;'],
                'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                'headerOptions' => ['class' => 'small text-center hidden-xs'],
                'filterOptions' => ['class' => 'hidden-xs']
            ],
            [
                'attribute'=>'status',
                'filter' => ConstHelper::getStatusList(),
                'value' => function ($model) {
                    /** @var Patient $model */
                    return ConstHelper::getStatusList($model->status);
                },
                'contentOptions'=>function($model, $key, $index, $column) {
                    return ['class'=>CssHelper::statusCss($model->status). ' kv-align-middle text-center'];
                },
                'options' =>['style' => 'width:10%; white-space: normal;'],
                'headerOptions' => ['class' => 'small text-center']
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'expandTitle' => Yii::t('app', 'Show Service requests'),
                'collapseTitle' => Yii::t('app', 'Hide Service requests'),
                'expandIcon' => '<span class="glyphicon glyphicon-menu-right"></span>',
                'collapseIcon' => '<span class="glyphicon glyphicon-menu-down"></span>',
                'header' => '',
                'expandOneOnly' => true,
                'enableRowClick' => false,
                'allowBatchToggle' => false,
                'detailUrl' => Url::to(['order/index']),
                'detailAnimationDuration' => 'fast',
                'options' =>['style' => 'width:2%; white-space: normal;'],
                'contentOptions' => ['class' => 'kv-align-middle'],
                'headerOptions' => ['class' => 'kartik-sheet-style'],
            ],

            [ // buttons
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Actions'),
                'template' => '{view}&nbsp;{update}&nbsp;{delete}&nbsp;{orders}&nbsp;{order-create}&nbsp;{patient-calendar}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                            'title'=>Yii::t('app', 'View Record'), 'style'=>'margin: 0 3px']);
                    },
                    'update' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
                            'title'=>Yii::t('app', 'Manage Record'), 'style'=>'margin: 0 3px']);
                    },
                    'delete' => function ($url, $model, $key) {
                        if ($model->status === ConstHelper::STATUS_ACTIVE) {
                            return Html::a('<i class="fa fa-ban fa-fw" aria-hidden="true"></i>', ['disable', 'id' => $model->id],
                                ['title' => Yii::t('app', 'Disable Patient'), 'style' => 'margin: 0 3px',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to disable this patient?'),
                                        'method' => 'post'
                                    ]
                                ]
                            );
                        }
                        return Html::a('<i class="fa fa-check fa-fw" aria-hidden="true"></i>', ['enable', 'id' => $model->id],
                            ['title' => Yii::t('app', 'Enable Patient'), 'style' => 'margin: 0 3px',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to enable this patient?'),
                                    'method' => 'post'
                                ]
                            ]
                        );
                    },
                    'order-create' => function ($url, $model, $key) {
                        return Html::a('<i class="fa fa-plus fa-fw" aria-hidden="true"></i>', ['order/create', 'pid' => $model->id],
                            ['title' => Yii::t('app', 'Request New Service'),
                                'style' => 'margin: 0 3px',
                                'data' => [
                                    'toggle' => 'modal',
                                    'target' => '#patient_order_modal_window',
                                ],
                            ]
                        );
                    },
                    'patient-calendar' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>', ['patient-calendar/view', 'id' => $model->id],
                            ['title'=>Yii::t('app', 'Scheduled Visits'), 'style'=>'margin: 0 3px']
                        );
                    },
                ],
                'options' => ['style' => 'width:15%; white-space: normal;'],
                'contentOptions' => ['class' => 'kv-align-middle text-center'],
                'headerOptions' => ['class' => 'small text-center'],
            ], // ActionColumn
        ],
    ]) ?>
    </div>

</div>

<?= ModalAjaxWidget::widget([
    'showBtn' => false,
    'modalClass' => 'ajax-modal-wrap-1100',
    'targetId' => 'patient_order_modal_window'
])?>

<?php
$script = <<< JS
$(function() {
    $('body').on('beforeSubmit', 'form#service_request_form', function () {
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
                    $('#alert_container').append('<div id="order_alert" class="alert ' +  (response.success ? 'alert-success' : 'alert-danger') + '"><a class="close" data-dismiss="alert">×</a><span>'+response.message+'</span></div>')
                    $("#order_alert").fadeTo(5000, 500).slideUp(500, function(){
                        $("#order_alert").slideUp(500).remove();
                    });
                }
    
                // Clear the form
                form.yiiActiveForm('resetForm');  // Clear the form with the validation messages
                form[0].reset();                  // Remove the values from the form inputs
    
                $('#patient_order_modal_window').modal('hide');  // close modal
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
    }).on('submit', 'form#service_request_form', function(e){
        console.log('Submitted')
        e.preventDefault();
    });
});
JS;
$this->registerJS($script, View::POS_END);
?>
