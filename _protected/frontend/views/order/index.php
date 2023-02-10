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

<div class="order-index box box-primary">
    <h4><?= Yii::t('app', 'Service requests for {name}', ['name' => $patient->patientFullName]) ?> </h4>
    <div class="box-header with-border">
        <div id="alert_container"></div>
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'New Service Request'), ['create', 'pid' => $patient->id], [
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
                    'id' => $gridId
                ]
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:3%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center']
                ],
                [
                    'attribute' => 'order_number',
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'service_name',
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center'],
                ],
                [
                    'attribute' => 'certification_start_date',
                    'format' => 'date',
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'certification_end_date',
                    'format' => 'date',
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'service_frequency',
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return ['class' => CssHelper::orderFrequencyCss($model->frequency_status) . ' kv-align-middle text-center hidden-xs'];
                    },
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'label' => 'Assigned Therapists',
                    'value' => function ($model) {
                        return implode(', ', ArrayHelper::getColumn($model->providers, function ($item) {
                            return $item->userFullName;
                        }));
                    },
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center']
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'date',
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'completed_at',
                    'format' => 'date',
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'status',
                    'value' => function ($model) {
                        /** @var Order $model */
                        return Order::getStatusList($model->status);
                    },
                    'contentOptions' => function ($model, $key, $index, $column) {
                        return ['class' => CssHelper::orderStatusCss($model->status) . ' kv-align-middle kv-align-center'];
                    },
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center'],
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['class' => 'small kv-align-middle kv-align-center'],
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'template' => '{view}&nbsp;{update}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) use ($patient) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', ['order/view', 'id' => $model->id, 'pid' => $patient->id], [
                                    'title' => Yii::t('app', 'View Service Request'),
                                    'style' => 'margin: 0 3px',
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#patient_order_modal_window',
                                    ],
                                ]
                            );
                        },
                        'update' => function ($url, $model, $key) use ($patient) {
                            if($model->status === Order::ORDER_STATUS_PENDING) {
                                return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', ['order/update', 'id' => $model->id, 'pid' => $patient->id], [
                                    'title' => Yii::t('app', 'Update Service Request'),
                                    'style' => 'margin: 0 3px',
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#patient_order_modal_window',
                                    ],
                                ]);
                            } else {
                                return '';
                            }
                        },
                    ]
                ]
            ],
        ]) ?>
    </div>

</div>