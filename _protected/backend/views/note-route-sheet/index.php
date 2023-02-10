<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\MaskedInput;
use common\helpers\ConstHelper;
use common\helpers\CssHelper;
use common\models\NoteRouteSheet;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\NoteRouteSheetSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-route-sheet-index box box-primary">
    <div class="box-header with-border">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Add Route Sheet'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'resizableColumns' => false,
            'responsiveWrap' => false,
            'responsive' => true,
            'export' => false,
            'pjax' => false,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => Yii::$app->controller->id . '-index',
                    'enablePushState' => false,
                ]
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:5%; white-space: normal;'],
                    'contentOptions'=>['class'=>' text-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'visit_code',
                    'label' => 'Visit Code',
                    'filter' => NoteRouteSheet::getVisitCode(),
                    'value' => function($model) {
                        return NoteRouteSheet::getVisitCode($model->visit_code);
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'mrn',
                    'label' => 'MR#',
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'order_number',
                    'label' => 'Request#',
                    'value' => 'order.order_number',
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                /*[
                    'attribute'=>'dob',
                    'label' => 'DOB',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'dob',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],*/
                [
                    'attribute'=>'patient_name',
                    'label' => 'Patient Name',
                    'options' =>['style' => 'width:15%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'therapist_name',
                    'label' => 'Therapist Name',
                    'options' =>['style' => 'width:15%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'visited_at',
                    'label' => 'Visit Date',
                    'value' => 'visit.visited_at',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'visited_at',
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
                    'attribute'=>'submitted_at',
                    'label' => 'Submitted At',
                    'format' => 'datetime',
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
                    'options' =>['style' => 'width:13%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'status',
                    'filter' => ConstHelper::getNoteStatusList(),
                    'value' => function ($model) {
                        return ConstHelper::getNoteStatusList($model->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::noteStatusCss($model->status) . ' text-center'];
                    },
                    'options' =>['style' => 'width:9%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['class' => 'small text-center'],
                    'options' => ['style' => 'width:11%; white-space: normal;'],
                    'template' => '{view}{update}{accept}{reject}{document/note-route-sheet}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('', $url, ['title'=>Yii::t('app', 'View Record'),
                                'class'=>'glyphicon glyphicon-eye-open', 'style' => 'margin:0 5px']);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('', $url, ['title'=>Yii::t('app', 'Manage Record'),
                                'class'=>'glyphicon glyphicon-pencil', 'style' => 'margin:0 5px']);
                        },
                        'accept' => function ($url, $model, $key) {
                            if ($model->isSubmitted()) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Accept Note'),
                                    'class' => 'glyphicon glyphicon-ok', 'style' => 'margin:0 5px;color:green',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to accept this note?'),
                                        'method' => 'post'
                                    ]
                                ]);
                            }
                            return '';
                        },
                        'reject' => function ($url, $model, $key) {
                            if (!$model->isPending()) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Reject Note'),
                                    'class' => 'glyphicon glyphicon-ban-circle', 'style' => 'margin:0 5px;color:red',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to reject this note?'),
                                        'method' => 'post'
                                    ]
                                ]);
                            }
                            return '';
                        },
                        'document/note-route-sheet' => function ($url, $model, $key) {
                            if (!$model->isPending()) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Print Note'),
                                    'class' => 'glyphicon glyphicon-print', 'style' => 'margin:0 5px;color:#3c8dbc', 'target' => '_blank',
                                    'data' => [
                                        'pjax' => 0
                                    ]
                                ]);
                            }
                            return '';
                        },
                    ],
                ],
            ],
        ]) ?>
    </div>

</div>
