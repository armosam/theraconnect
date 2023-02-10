<?php

use common\helpers\CssHelper;
use common\models\Service;
use common\models\UsCity;
use common\widgets\ISO639\Language;
use yii\helpers\Html;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;
use common\models\Prospect;
use common\widgets\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\ProspectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prospect-index box box-primary">
    <?php Pjax::begin(); ?>
    <div class="box-header with-border">
        <p class="text-right">
            <?php // Html::a(Yii::t('app', 'Add New Application'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
        </p>
    </div>

    <div class="box-body table-responsive no-padding">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            //'layout' => "{items}\n{summary}\n{pager}",
            'rowOptions' => function ($model) {
                /** @var Prospect $model */
                if ($model->status === Prospect::PROSPECTIVE_STATUS_PENDING) {
                    return ['class' => 'warning'];
                } elseif ($model->status === Prospect::PROSPECTIVE_STATUS_REJECTED) {
                    return ['class' => 'danger'];
                }

                return [];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:3%; white-space: normal;'],
                    'contentOptions' => ['class' => 'text-center vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'full_name',
                    'label' => 'Full Name',
                    'value' => function ($model) {
                        /** @var Prospect $model */
                        return $model->prospectFullName;
                    },
                    'options' =>['style' => 'width:11%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'email',
                    'format' => 'email',
                    'options' =>['style' => 'width:11%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'phone_number',
                    'format' => 'phone',
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
//                [
//                    'attribute'=>'full_address',
//                    'label' => 'Address',
//                    'value' => function ($model) {
//                        /** @var Prospect $model */
//                        return $model->getProspectAddress();
//                    },
//                    'options' =>['style' => 'width:10%; white-space: normal;'],
//                    'headerOptions' => ['class' => 'small text-center']
//                ],
                [
                    'attribute' => 'service_id',
                    //'label' => 'Therapist Service',
                    'filter' => Service::serviceList(),
                    'value' => function ($model) {
                        return $model->service ? $model->service->service_name : null;
                    },
                    'options' =>['style' => 'width:9%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'license_type',
                    //'format' => 'email',
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'contentOptions' => ['class' => 'text-center vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                /*[
                    'attribute' => 'license_number',
                    //'format' => 'email',
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'license_expiration_date',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'license_expiration_date',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],*/
                [
                    'attribute'=>'language',
                    'filter' => Language::allEnglish(),
                    'value' => function ($model) {
                        /** @var Prospect $model */
                        return $model->getProspectLanguage();
                    },
                    'options' =>['style' => 'width:18%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'covered_county',
                    'filter' => UsCity::getCounties('CA'),
                    'value' => function ($model) {
                        /** @var Prospect $model */
                        return $model->getProspectCoveredCounty();
                    },
                    'options' =>['style' => 'width:18%; white-space: normal;'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'status',
                    'filter' => Prospect::getStatusList(),
                    'value' => function ($model) {
                        return Prospect::getStatusList($model->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::userStatusCss($model->status). ' text-center vertical-middle'];
                    },
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],

                // 'address',
                // 'city',
                // 'state',
                // 'zip_code',
                // 'country',
                //'language',
                // 'covered_county',
                // 'covered_city',
                // 'ip_address',
                // 'note',
                // 'rejected_by',
                // 'rejected_at',
                // 'rejection_reason',
                // 'created_by',
                // 'created_at',
                // 'updated_by',
                // 'updated_at',

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'headerOptions' => ['class' => 'small text-center'],
                    'contentOptions' => ['class' => 'vertical-middle'],
                    'options' => ['style' => 'width:8%; white-space: normal;'],
                    'template' => '{view}{accept}{reject}',
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
                            return Html::a('<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>', $url, [
                                'title'=>Yii::t('app', 'Delete Record'), 'style'=>'margin: 0 3px',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this record?'),
                                    'method' => 'post'
                                ]
                            ]);
                        },
                        'accept' => function ($url, $model, $key) {
                            if ($model->isPending() || $model->isRejected()) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Accept Application'),
                                    'class' => 'glyphicon glyphicon-ok', 'style' => 'margin:0 5px;color:green',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to accept this application and create an account?'),
                                        'method' => 'post'
                                    ]
                                ]);
                            }
                            return '';
                        },
                        'reject' => function ($url, $model, $key) {
                            if ($model->isPending()) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Reject Application'),
                                    'class' => 'glyphicon glyphicon-ban-circle', 'style' => 'margin:0 5px;color:red',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to reject this application?'),
                                        'method' => 'post'
                                    ]
                                ]);
                            }
                            return '';
                        },
                    ]
                ]
            ],
        ]); ?>
    </div>
    <?php Pjax::end(); ?>
</div>
