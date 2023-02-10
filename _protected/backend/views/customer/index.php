<?php

use yii\helpers\Html;
use common\models\User;
use common\helpers\CssHelper;
use common\helpers\ConstHelper;
use common\widgets\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="customer-index box box-primary">
    <div class="box-header with-border">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Create Agency'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'summary' => false,
            'rowOptions' => function ($model) {
                /** @var User $model */
                if (!$model->isActiveAccount()) {
                    return ['class' => 'danger'];
                }
                return [];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' => ['style' => 'width:5%; white-space: normal;'],
                    'contentOptions' => ['class' => 'text-center vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'agency_name',
                    'value' => function ($data) {
                        return empty($data->agency_name) ? null : $data->agency_name;
                    },
                    'options' => ['style' => 'width:20%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'full_name',
                    'label' => Yii::t('app', 'Full Name'),
                    'format' => 'raw',
                    'value' => function ($data) {
                        return ConstHelper::showShortString($data->getUserFullName(), 0, 50, '...');
                    },
                    'options' => ['style' => 'width:15%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'username',
                    'value' => function ($data) {
                        return ConstHelper::showShortString($data->username, 0, 20, '...');
                    },
                    'options' => ['style' => 'width:15%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'options' => ['style' => 'width:15%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'rep_position',
                    'value' => function ($data) {
                        return empty($data->rep_position) ? null : $data->rep_position;
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'text-center vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'status',
                    'filter' => User::getUserStatusList(),
                    'value' => function ($data) {
                        return $data->getUserStatusName($data->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::userStatusCss($model->status). ' text-center vertical-middle'];
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['class' => 'small text-center'],
                    'contentOptions' => ['class' => 'text-center vertical-middle'],
                    'options' => ['style' => 'width:10%; white-space: normal;'],
                    'template' => '{view}{update}{patient/index}{activate}{deactivate}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>', $url, [
                                'title'=>Yii::t('app', 'View Record'), 'style'=>'margin: 0 3px']);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>', $url, [
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
                        'patient/index' => function ($url, $model, $key) {
                            /** @var $model User */
                            if ($model->role->item_name === User::USER_CUSTOMER) {
                                return Html::a('<i class="fa fa-wheelchair fa-fw" aria-hidden="true"></i>', ['patient/index', 'uid' => $key], [
                                    'title'=>Yii::t('app', 'Manage Agency Patients'), 'style'=>'margin: 0 3px']);
                            }
                            return '';
                        },
                        'activate' => function ($url, $model, $key) {
                            /** @var $model User */
                            if ($model->status !== User::USER_STATUS_ACTIVE) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Activate Agency Account'),
                                    'class' => 'glyphicon glyphicon-ok', 'style' => 'margin:0 5px;color:green',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to activate this record?'),
                                        'method' => 'post'
                                    ]
                                ]);
                            }
                            return '';
                        },
                        'deactivate' => function ($url, $model, $key) {
                            if ($model->status === User::USER_STATUS_ACTIVE) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Deactivate Agency Account'),
                                    'class' => 'glyphicon glyphicon-ban-circle', 'style' => 'margin:0 5px;color:red',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to deactivate this record?'),
                                        'method' => 'post'
                                    ]
                                ]);
                            }
                            return '';
                        },

                        /*'user-notification/view' => function($url, $model, $key){
                            return Html::a('<i class="fa fa-envelope-o fa-fw" aria-hidden="true"></i>', $url, [
                                'title'=>Yii::t('app', 'Manage User Notifications'), 'style'=>'margin: 0 3px']);
                        },*/
                    ],
                ], // ActionColumn
            ], // columns
        ]); ?>
    </div>
    <?php //Pjax::end(); ?>
</div>