<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Visit;

/* @var $this yii\web\View */
/* @var $order common\models\Order */
/* @var $dataProvider yii\data\ActiveDataProvider */

$gridId = Yii::$app->controller->id . '-subgrid-' . $order->order_number;
?>

<div class="visit-index box box-primary">
    <h4><?= Yii::t('app', 'Therapist Visits') ?> </h4>
    <div class="box-header with-border">
        <div id="alert_container"></div>
        <p class="text-right">
            <?php if(Yii::$app->user->can('manageVisit', ['model' => $order])): ?>
                <?= Html::a(Yii::t('app', 'Schedule A Visit'), ['create', 'oid' => $order->id], [
                    'title'=>Yii::t('app', 'New Visit Scheduling'), 'class' => 'btn btn-success btn-sm',
                    'data' => [
                        'toggle' => 'modal',
                        'target' => '#provider_order_modal_window',
                    ],
                ]) ?>
            <?php else: ?>
                <?= Html::button('Schedule A Visit', ['class' => 'btn btn-success btn-sm', 'disabled' => true]) ?>
            <?php endif; ?>
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
                    'options' => ['style' => 'width:5%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle kv-align-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'visited_at',
                    'label' => 'Visit Date',
                    'format' => 'datetime',
                    'options' =>['style' => 'width:20%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'created_by',
                    'label' => 'Visited By',
                    'value' => function($model) {
                        /** @var Visit $model */
                        return $model->createdBy->getUserFullName();
                    },
                    'options' =>['style' => 'width:20%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'comment',
                    'options' =>['style' => 'width:35%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],

                [ // Buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'template' => '{update}{note-route-sheet}{note-progress}{note-supplemental}{note-eval}{note-discharge-order}{note-discharge-summary}{note-communication}',
                    'options' => ['style' => 'width:20%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'small text-center'],
                    'buttons' => [
                        'update' => function ($url, $model, $key) use($order) {
                            if(!$model->isVisitStarted() && $model->isVisitOwner()) {
                                return Html::a('<span class="glyphicon glyphicon-transfer" aria-hidden="true"></span>', ['update', 'oid' => $order->id, 'id' => $model->id], [
                                    'title' => Yii::t('app', 'Change Visit Time'),
                                    'style' => 'margin: 0 7px;',
                                    'data' => [
                                        'toggle' => 'modal',
                                        'target' => '#provider_order_modal_window',
                                    ],
                                ]);
                            }
                            return '';
                        },
                        'note-route-sheet' => function ($url, $model, $key) use($order) {
                            /** @var Visit $model */
                            if (!empty($model->routeSheetNote) && !$model->routeSheetNote->isPending()) {
                                return Html::a('<span class="fa fa-pencil-square-o" aria-hidden="true"></span>', ['document/note-route-sheet', 'id' => $model->routeSheetNote->id], [
                                    'title' => Yii::t('app', 'Print Route Sheet'),
                                    'style' => 'margin: 0 7px;' . 'color: #4cae4c', 'target' => '_blank',
                                    'data' => [
                                        'pjax' => 0
                                    ]
                                ]);
                            } else {
                                if ($model->isVisitOwner()) {
                                    return Html::a('<span class="fa fa-pencil-square-o" aria-hidden="true"></span>', ['note-route-sheet', 'oid' => $order->id, 'id' => $model->id], [
                                        'title' => Yii::t('app', 'Add Route Sheet'),
                                        'style' => 'margin: 0 7px;' . 'color: #ff3131',
                                        'data' => [
                                            'toggle' => 'modal',
                                            'target' => '#provider_order_modal_window',
                                        ]
                                    ]);
                                }
                                return '';
                            }
                        },
                        'note-progress' => function ($url, $model, $key) use($order) {
                            /** @var Visit $model */
                            if (!empty($model->progressNote) && ((!$model->progressNote->isPending() && ($model->order->orderPTA->id === Yii::$app->user->id)) || (!$model->progressNote->isPending() && !$model->progressNote->isSubmittedByPTA() && ($model->order->orderRPT->id === Yii::$app->user->id)))) {
                                return Html::a('<span class="fa fa-file-powerpoint-o" aria-hidden="true"></span>', ['document/note-progress', 'id' => $model->progressNote->id], [
                                    'title' => Yii::t('app', 'Print Progress Note'),
                                    'style' => 'margin: 0 7px;' . 'color: #4cae4c', 'target' => '_blank',
                                    'data' => [
                                        'pjax' => 0
                                    ]
                                ]);
                            } else {
                                if ($model->isVisitOwner() || ($model->order->orderRPT->id === Yii::$app->user->id)) {
                                    return Html::a('<span class="fa fa-file-powerpoint-o" aria-hidden="true"></span>', ['note-progress', 'oid' => $order->id, 'id' => $model->id], [
                                        'title' => Yii::t('app', 'Manage Progress Note'),
                                        'style' => 'margin: 0 7px;' . 'color: #ff3131',
                                        'data' => [
                                            'toggle' => 'modal',
                                            'target' => '#provider_order_modal_window',
                                        ]
                                    ]);
                                }
                                return '';
                            }
                        },
                        'note-supplemental' => function ($url, $model, $key) use($order) {
                            /** @var Visit $model */
                            if($model->isFirstVisit()) {
                                if (!empty($model->supplementalNote) && !$model->supplementalNote->isPending()) {
                                    return Html::a('<span class="fa fa-user-md" aria-hidden="true"></span>', ['document/note-supplemental', 'id' => $model->supplementalNote->id], [
                                        'title' => Yii::t('app', 'Print Physician Order'),
                                        'style' => 'margin: 0 7px;' . 'color: #4cae4c', 'target' => '_blank',
                                        'data' => [
                                            'pjax' => 0
                                        ]
                                    ]);
                                } else {
                                    if ($model->isVisitOwner()) {
                                        return Html::a('<span class="fa fa-user-md" aria-hidden="true"></span>', ['note-supplemental', 'oid' => $order->id, 'id' => $model->id], [
                                            'title' => Yii::t('app', 'Manage Physician Order'),
                                            'style' => 'margin: 0 7px;' . 'color: #ff3131',
                                            'data' => [
                                                'toggle' => 'modal',
                                                'target' => '#provider_order_modal_window',
                                            ]
                                        ]);
                                    }
                                    return '';
                                }
                            }
                            return '';
                        },
                        'note-eval' => function ($url, $model, $key) use($order) {
                            /** @var Visit $model */
                            if($model->isFirstVisit()) {
                                if (!empty($model->evalNote) && !$model->evalNote->isPending()) {
                                    return Html::a('<span class="fa fa-file-text-o" aria-hidden="true"></span>', ['document/note-eval', 'id' => $model->evalNote->id], [
                                        'title' => Yii::t('app', 'Print Eval Note'),
                                        'style' => 'margin: 0 7px;' . 'color: #4cae4c', 'target' => '_blank',
                                        'data' => [
                                            'pjax' => 0
                                        ]
                                    ]);
                                } else {
                                    if ($model->isVisitOwner()) {
                                        return Html::a('<span class="fa fa-file-text-o" aria-hidden="true"></span>', ['note-eval', 'oid' => $order->id, 'id' => $model->id], [
                                            'title' => Yii::t('app', 'Manage Eval Note'),
                                            'style' => 'margin: 0 7px;' . 'color: #ff3131',
                                            'data' => [
                                                'toggle' => 'modal',
                                                'target' => '#provider_order_modal_window',
                                            ]
                                        ]);
                                    }
                                    return '';
                                }
                            }
                            return '';
                        },
                        'note-discharge-order' => function ($url, $model, $key) use($order) {
                            /** @var Visit $model */
                            if($model->isLastVisit() && !$model->isFirstVisit()) {
                                if (!empty($model->dischargeOrderNote) && !$model->dischargeOrderNote->isPending()) {
                                    return Html::a('<span class="fa fa-check-square-o" aria-hidden="true"></span>', ['document/note-discharge-order', 'id' => $model->dischargeOrderNote->id], [
                                        'title' => Yii::t('app', 'Print Discharge Order'),
                                        'style' => 'margin: 0 7px;' . 'color: #4cae4c', 'target' => '_blank',
                                        'data' => [
                                            'pjax' => 0
                                        ]
                                    ]);
                                } else {
                                    if ($model->isVisitOwner()) {
                                        return Html::a('<span class="fa fa-check-square-o" aria-hidden="true"></span>', ['note-discharge-order', 'oid' => $order->id, 'id' => $model->id], [
                                            'title' => Yii::t('app', 'Manage Discharge Order'),
                                            'style' => 'margin: 0 7px;' . 'color: #ff3131',
                                            'data' => [
                                                'toggle' => 'modal',
                                                'target' => '#provider_order_modal_window',
                                            ]
                                        ]);
                                    }
                                    return '';
                                }
                            }
                            return '';
                        },
                        'note-discharge-summary' => function ($url, $model, $key) use($order) {
                            /** @var Visit $model */
                            if($model->isLastVisit() && !$model->isFirstVisit()) {
                                if (!empty($model->dischargeSummaryNote) && !$model->dischargeSummaryNote->isPending()) {
                                    return Html::a('<span class="fa fa-file-archive-o" aria-hidden="true"></span>', ['document/note-discharge-summary', 'id' => $model->dischargeSummaryNote->id], [
                                        'title' => Yii::t('app', 'Print Discharge Summary'),
                                        'style' => 'margin: 0 7px;' . 'color: #4cae4c', 'target' => '_blank',
                                        'data' => [
                                            'pjax' => 0
                                        ]
                                    ]);
                                } else {
                                    if ($model->isVisitOwner()) {
                                        return Html::a('<span class="fa fa-file-archive-o" aria-hidden="true"></span>', ['note-discharge-summary', 'oid' => $order->id, 'id' => $model->id], [
                                            'title' => Yii::t('app', 'Manage Discharge Summary'),
                                            'style' => 'margin: 0 7px;' . 'color: #ff3131',
                                            'data' => [
                                                'toggle' => 'modal',
                                                'target' => '#provider_order_modal_window',
                                            ]
                                        ]);
                                    }
                                    return '';
                                }
                            }
                            return '';
                        },
                        'note-communication' => function ($url, $model, $key) use ($order) {
                            /** @var Visit $model */
                            if (!empty($model->communicationNote) && !$model->communicationNote->isPending()) {
                                return Html::a('<span class="fa fa-comments-o" aria-hidden="true"></span>', ['document/note-communication', 'id' => $model->communicationNote->id], [
                                    'title' => Yii::t('app', 'Print Communication Note'),
                                    'style' => 'margin: 0 7px;' . 'color: #4cae4c', 'target' => '_blank',
                                    'data' => [
                                        'pjax' => 0
                                    ]
                                ]);
                            } else {
                                if ($model->isVisitOwner()) {
                                    return Html::a('<span class="fa fa-comments-o" aria-hidden="true"></span>', ['note-communication', 'oid' => $order->id, 'id' => $model->id], [
                                        'title' => Yii::t('app', 'Manage Communication Note'),
                                        'style' => 'margin: 0 7px;' . 'color: #ff3131',
                                        'data' => [
                                            'toggle' => 'modal',
                                            'target' => '#provider_order_modal_window',
                                        ]
                                    ]);
                                }
                                return '';
                            }
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>
</div>