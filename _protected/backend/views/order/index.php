<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Order;
use common\helpers\CssHelper;
use common\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $patient common\models\Patient */
/* @var $dataProvider yii\data\ActiveDataProvider */

$gridId = Yii::$app->controller->id . '-subgrid-' . $patient->id;
?>

<div class="order-index col-lg-12">

    <h4 class="text-info"><?= Yii::t('app', 'Service requests for {name}', ['name' => Html::tag('b', $patient->patientFullName)]) ?> </h4>

    <div class="box-header with-border">
        <div class="col-lg-8" id="alert_container"></div>
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Request New Service'), ['order/create', 'pid' => $patient->id], [
                'title' => Yii::t('app', 'Request New Service'), 'class' => 'btn btn-success',
                'data' => [
                    'toggle' => 'modal',
                    'target' => '#patient_order_modal_window',
                ],
            ]) ?>
        </p>
    </div>

    <div class="box-body table-responsive no-padding">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}",
            'resizableColumns' => false,
            'responsiveWrap' => false,
            'responsive' => true,
            'export' => false,
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => $gridId,
                    'enablePushState' => false
                ]
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:3%; white-space: normal;'],
                    'contentOptions'=>['class'=>' text-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'service_name',
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'order_number',
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'label' => 'Documents',
                    'format' => 'raw',
                    'value' => function($model) {
                        return empty($model->getDocumentList()) ? null : implode(' ', $model->getDocumentList());
                    },
                    'options' =>['style' => 'width:12%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'certification_start_date',
                    'format' => 'date',
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'certification_end_date',
                    'format' => 'date',
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'service_frequency',
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::orderFrequencyCss($model->frequency_status)];
                    },
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'label' => 'Assigned Therapists',
                    'value' => function($model) {
                        return implode(', ', ArrayHelper::getColumn($model->providers, function($item) { return $item->userFullName; }));
                    },
                    'options' =>['style' => 'width:20%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'accepted_at',
                    'format' => 'date',
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'completed_at',
                    'format' => 'date',
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        /** @var Order $model */
                        return Order::getStatusList($model->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::orderStatusCss($model->status).' text-center'];
                    },
                    'options' =>['style' => 'width:6%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['class' => 'small text-center'],
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'template' => '{view}&nbsp;{status}&nbsp;{approve-frequency}&nbsp;{change-provider}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) use($patient) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['order/view', 'id' => $model->id, 'pid' => $patient->id], [
                                'title'=>Yii::t('app', 'View Service Request'),
                                'style'=>'margin: 0 3px',
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#patient_order_modal_window',
                                    ],
                                ]
                            );
                        },
                        'status' => function ($url, $model, $key) use($patient) {
                            if ($model->status === Order::ORDER_STATUS_PENDING) {
                                return Html::a('<span class="glyphicon glyphicon-saved" aria-hidden="true"></span>', ['order/submit', 'id' => $model->id, 'pid' => $patient->id], [
                                        'title'=>Yii::t('app', 'Submit Service Request'),
                                        'style'=>'margin: 0 3px',
                                        'data' => [
                                            'toggle' => 'modal',
                                            'target' => '#patient_order_modal_window',
                                        ],
                                    ]
                                );
                            }

                            if ($model->status === Order::ORDER_STATUS_SUBMITTED) {
                                return Html::a('<span class="glyphicon glyphicon-saved" aria-hidden="true"></span>', ['order/submit', 'id' => $model->id, 'pid' => $patient->id], [
                                        'title'=>Yii::t('app', 'Submit Service Request'),
                                        'style'=>'margin: 0 3px',
                                        'data' => [
                                            'toggle' => 'modal',
                                            'target' => '#patient_order_modal_window',
                                        ],
                                    ]
                                );
                            }

                            if($model->status === Order::ORDER_STATUS_ACCEPTED) {
                                return Html::a('<span class="glyphicon glyphicon-save" aria-hidden="true"></span>', ['order/complete', 'id' => $model->id, 'pid' => $patient->id], [
                                        'title'=>Yii::t('app', 'Complete Service Request'),
                                        'style'=>'margin: 0 3px',
                                        'data' => [
                                            'toggle' => 'modal',
                                            'target' => '#patient_order_modal_window',
                                        ],
                                    ]
                                );
                            }

                            return '';
                        },
                        'approve-frequency' => function($url, $model, $key) use($patient) {
                            if(!empty($model->service_frequency) && in_array($model->frequency_status, [Order::ORDER_FREQUENCY_STATUS_SUBMITTED, Order::ORDER_FREQUENCY_STATUS_APPROVED])) {
                                return Html::a('<span class="glyphicon glyphicon-check" aria-hidden="true"></span>', ['order/approve-frequency', 'id' => $model->id, 'pid' => $patient->id], [
                                    'title' => Yii::t('app', 'Approve Frequency'),
                                    'style'=>'margin: 0 3px',
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#patient_order_modal_window',
                                    ],
                                ]);
                            }
                            return '';
                        },
                        'change-provider' => function ($url, $model, $key) use ($patient) {
                            if($model->status === Order::ORDER_STATUS_ACCEPTED) {
                                return Html::a('<span class="glyphicon glyphicon-refresh" aria-hidden="true"></span>', ['order/change-provider', 'id' => $model->id, 'pid' => $patient->id], [
                                    'title' => Yii::t('app', 'Change Therapist'),
                                    'style' => 'margin: 0 3px',
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#patient_order_modal_window',
                                    ],
                                ]);
                            }
                            return '';
                        },
                    ]
                ]
            ],
        ]) ?>
    </div>

</div>