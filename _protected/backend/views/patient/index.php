<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use kartik\grid\GridView;
use yii\widgets\MaskedInput;
use common\models\User;
use common\models\Patient;
use common\helpers\CssHelper;
use common\helpers\ConstHelper;
use common\widgets\ajax\modal\ModalAjaxWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\PatientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'List Patients');
$this->params['breadcrumbs'][] = $this->title;

$gridId = Yii::$app->controller->id . '-subgrid-' . $searchModel->id;
?>

<div class="patient-index box box-primary">
    <div class="box-header with-border">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Create New Patient'), ['create', 'uid' => $searchModel->customer_id], ['class' => 'btn btn-success btn-flat']) ?>
        </p>
    </div>

    <div class="box-body no-padding">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'responsiveWrap' => false,
            'pjax' => false,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => $gridId,
                    'enablePushState' => false
                ]
            ],
            'rowOptions' => function ($model) {
                if (!empty($model->pendingOrders)) {
                    return ['class' => 'warning'];
                }
                return [];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:3%; white-space: normal;'],
                    'contentOptions'=>['class'=>' text-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'customer_id',
                    'filter' => User::customerList(true, true, true),
                    'value' => function ($model) {
                        /** @var Patient $model */
                        return $model->customer->agency_name;
                    },
                    'options' =>['style' => 'width:12%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'patient_number',
                    'value' => function ($model) {
                        /** @var Patient $model */
                        return $model->patient_number;
                    },
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'start_of_care',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'start_of_care',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'full_name',
                    'label' => 'Full Name',
                    'value' => function ($model) {
                        /** @var Patient $model */
                        return $model->getPatientFullName();
                    },
                    'options' =>['style' => 'width:12%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'address',
                    'value' => function ($model) {
                        /** @var Patient $model */
                        return $model->patientAddress;
                    },
                    'options' =>['style' => 'width:20%; white-space: normal;'],
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
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'status',
                    'filter' => ConstHelper::getStatusList(),
                    'value' => function ($model) {
                        /** @var Patient $model */
                        return ConstHelper::getStatusList($model->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::statusCss($model->status) . ' text-center'];
                    },
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'created_at',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:8%; white-space: normal;'],
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
                    'detailUrl' => Url::to(['order/index', '_' => time()]),
                    'detailAnimationDuration' => 'fast',
                    'options' =>['style' => 'width:2%; white-space: normal;'],
                    'headerOptions' => ['class' => 'kartik-sheet-style'],
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['class' => 'small text-center'],
                    'options' => ['style' => 'width:13%; white-space: normal;'],
                    'template' => '{view}&nbsp;{update}&nbsp;{delete}&nbsp;{order-create}&nbsp;{patient-calendar}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) use($searchModel) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['patient/view', 'id' => $model->id, 'uid' => $searchModel->customer_id], [
                                'title'=>Yii::t('app', 'View Record'), 'style'=>'margin: 0 3px']);
                        },
                        'update' => function ($url, $model, $key) use($searchModel) {
                            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', ['patient/update', 'id' => $model->id, 'uid' => $searchModel->customer_id], [
                                'title'=>Yii::t('app', 'Manage Record'), 'style'=>'margin: 0 3px']);
                        },
                        /*'delete' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                                'title'=>Yii::t('app', 'Delete Record'), 'style'=>'margin: 0 3px',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this record?'),
                                    'method' => 'post'
                                ]
                            ]);
                        },*/
                        'delete' => function ($url, $model, $key) use($searchModel) {
                            if ($model->status === ConstHelper::STATUS_ACTIVE) {
                                return Html::a('<i class="fa fa-ban fa-fw" aria-hidden="true"></i>', ['patient/disable', 'id' => $model->id, 'uid' => $searchModel->customer_id],
                                    ['title' => Yii::t('app', 'Disable Patient'), 'style' => 'margin: 0 3px',
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure you want to disable this patient?'),
                                            'method' => 'post'
                                        ]
                                    ]
                                );
                            }
                            return Html::a('<i class="fa fa-check fa-fw" aria-hidden="true"></i>', ['patient/enable', 'id' => $model->id, 'uid' => $searchModel->customer_id],
                                ['title' => Yii::t('app', 'Accept Patient'), 'style' => 'margin: 0 3px',
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
                                ['title'=>Yii::t('app', 'Patient Calendar'), 'style'=>'margin: 0 3px']
                            );
                        },
                    ],
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
    $('body').on('beforeSubmit', 'form#service_change_provider_form, form#service_frequency_approve_form, form#order_complete_form, form#order_create_form, form#order_submit_form', function () {
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
                    $("#order_alert").fadeTo(3000, 500).slideUp(500, function(){
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
    }).on('submit', 'form#service_frequency_approve_form', function(e){
        console.log('Submitted')
        e.preventDefault();
    });
});
JS;
$this->registerJS($script, View::POS_END);
?>