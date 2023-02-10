<?php

use yii\helpers\Html;
use common\models\User;
use common\models\UsCity;
use common\models\Service;
use common\helpers\CssHelper;
use common\helpers\ConstHelper;
use common\widgets\grid\GridView;
use common\widgets\ISO639\Language;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="provider-index box box-primary">
    <div class="box-header with-border">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Create Therapist'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
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
                if (!$model->isProviderCredentialsApproved()) {
                    return ['class' => 'danger'];
                } elseif (!$model->hasProviderService()) {
                    return ['class' => 'warning'];
                }
                return [];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:5%; white-space: normal;'],
                    'contentOptions' => ['class' => 'text-center vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'full_name',
                    'label' => Yii::t('app', 'Full Name'),
                    'format' => 'raw',
                    'value' => function ($data) {
                        return ConstHelper::showShortString($data->getUserFullName(), 0, 50, '...');
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'username',
                    'value' => function ($data) {
                        return ConstHelper::showShortString($data->username, 0, 20, '...');
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'language',
                    'filter' => Language::allEnglish(),
                    'value' => function ($model) {
                        /** @var User $model */
                        return $model->getUserLanguage();
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'covered_county',
                    'filter' => UsCity::getCounties('CA'),
                    'value' => function ($model) {
                        /** @var User $model */
                        return $model->getUserCoveredCounty();
                    },
                    'options' =>['style' => 'width:15%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'title',
                    'label' => 'Therapist Title',
                    'filter' => User::getTitleList(),
                    'value' => function ($data) {
                        return empty($data->title) ? null : $data->title;
                    },
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'contentOptions' => ['class' => 'text-center vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'service_id',
                    'label' => 'Therapist Service',
                    'filter' => Service::serviceList(),
                    'value' => function ($data) {
                        return $data->service ? $data->service->service_name : null;
                    },
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'credential_status',
                    'label' => 'Credential Status',
                    'format' => 'html',
                    'filter' => [ConstHelper::FLAG_YES => 'Approved', ConstHelper::FLAG_NO => 'Not Approved'],
                    'value' => function ($model) {
                        /** @var User $model */
                        return Yii::t('app', '{status}', ['status' => ($model->isProviderCredentialsApproved() ? 'Approved' : 'Not Approved')]);
                    },
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'contentOptions'=>function($model) {
                        return ['class' => ($model->isProviderCredentialsApproved() ? 'status green' : 'not-set') .' text-center vertical-middle'];
                    },
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
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['class' => 'small text-center'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'options' => ['style' => 'width:8%; white-space: normal;'],
                    'template' => '{view}{update}{set-service}{user-credential/index}',
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
                        'set-service' => function ($url, $model, $key) {
                            /** @var $model User */
                            if ($model->role->item_name === User::USER_PROVIDER) {

                                $items = '';
                                foreach (Service::serviceList() as $service_id => $service_name) {
                                    $items .= Html::tag('li', Html::a($service_name, ['user/set-service', 'uid' => $key, 'id' => $service_id], [
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure you want to set this service?'),
                                            'method' => 'post'
                                        ]
                                    ]));
                                }

                                return Html::tag('div', Html::a('<i class="fa fa-flash fa-fw" aria-hidden="true"></i>', '', [
                                        'title'=>Yii::t('app', 'Set Therapist Service'), 'data-toggle' => 'dropdown', 'style' => 'margin: 0 3px',
                                    ]).Html::tag('ui', $items, ['class'=>'dropdown-menu dropdown-menu-right']),
                                    ['class'=>'btn-group', 'style'=>'display: inline-block']
                                );
                            }
                            return '';
                        },
                        'user-credential/index' => function ($url, $model, $key) {
                            /** @var $model User */
                            if ($model->role->item_name === User::USER_PROVIDER) {
                                return Html::a('<i class="fa fa-lock fa-fw" aria-hidden="true"></i>', ['user-credential/index', 'uid' => $key], [
                                    'title'=>Yii::t('app', 'Manage Therapist Credentials'), 'style'=>'margin: 0 3px']);
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