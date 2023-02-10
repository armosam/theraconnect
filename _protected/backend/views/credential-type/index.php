<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;
use common\models\User;
use common\widgets\grid\GridView;
use common\helpers\ArrayHelper;
use common\helpers\ConstHelper;
use common\helpers\CssHelper;
use common\models\CredentialType;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\CredentialTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="credential-type-index box box-primary">
    <?php Pjax::begin(); ?>
    <p class="box-header with-border text-right">
        <?= Html::a(Yii::t('app', 'Create Credential Type'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </p>
    <div class="box-body table-responsive no-padding">

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:3%; white-space: normal;'],
                    'contentOptions'=>['class'=>' text-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'id',
                    'options' =>['style' => 'width:3%; white-space: normal;'],
                    'contentOptions'=>['class'=>' text-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'icon_class',
                    'label' => '',
                    'format' => 'html',
                    'value' => function ($data) {
                        /** @var CredentialType $data */
                        return Html::tag('i', '', ['class' => $data->icon_class]);
                    },
                    'options' =>['style' => 'width:5%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'credential_type_name',
                    'format' => 'html',
                    'value' => function ($data) {
                        /** @var CredentialType $data */
                        return $data->credential_type_name;
                    },
                    'options' =>['style' => 'width:35%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
    //            [
    //                'attribute'=>'created_by',
    //                'format' => 'html',
    //                'value' => function ($data) {
    //                    /** @var CredentialType $data */
    //                    return isset($data->createdBy) ? Html::a($data->createdBy->getUserFullName(true), Url::to(['/user/view', 'id' => $data->created_by])) : null;
    //                },
    //                'options' =>['style' => 'width:12%; white-space: normal;'],
    //                'headerOptions' => ['class' => 'small text-center']
    //            ],
    //            [
    //                'attribute' => 'created_at',
    //                'format' => 'datetime',
    //                'options' =>['style' => 'width:10%; white-space: normal;'],
    //                'headerOptions' => ['class' => 'small text-center']
    //            ],
                [
                    'attribute'=>'updated_by',
                    'filter' => ArrayHelper::map(User::find()->admin()->all(), 'id', 'first_name,last_name'),
                    'format' => 'html',
                    'value' => function ($data) {
                        /** @var CredentialType $data */
                        return isset($data->updatedBy) ? Html::a($data->updatedBy->getUserFullName(true), Url::to(['/user/view', 'id' => $data->updated_by])) : null;
                    },
                    'options' =>['style' => 'width:12%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'datetime',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'updated_at',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                /*[
                    'attribute' => 'ordering',
                    'filter' => range(0,(CredentialType::find()->count()+5)),
                    'options' =>['style' => 'width:5%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],*/
                [
                    'attribute'=>'status',
                    'filter' => ConstHelper::getStatusList(),
                    'value' => function ($data) {
                        return ConstHelper::getStatusList($data->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::statusCss($model->status) . ' text-center'];
                    },
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('', $url, ['title'=>Yii::t('app', 'View Record'),
                                'class'=>'glyphicon glyphicon-eye-open']);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('', $url, ['title'=>Yii::t('app', 'Manage Record'),
                                'class'=>'glyphicon glyphicon-pencil']);
                        },
                        'delete' => function ($url, $model, $key) {
                            $enable = $model->status == ConstHelper::STATUS_ACTIVE ? false : true;
                            if (!$enable) {
                                return Html::a('', Url::to(['/service-category/disable', 'id' => $model->id]),
                                    ['title' => Yii::t('app', 'Disable Service Category'),
                                        'class'=>'fa fa-ban',
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Are you sure you want to disable this category?'),
                                            'method' => 'post']
                                    ]);
                            }
                            return Html::a('', Url::to(['/service-category/enable', 'id' => $model->id]),
                                ['title' => Yii::t('app', 'Enable Service Category'),
                                    'class'=>'fa fa-check',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to enable this category?'),
                                        'method' => 'post'
                                    ]
                                ]);
                        },
                    ],
                    'options' =>['style' => 'width:7%; white-space: normal;'],
                ], // ActionColumn
             ]
        ]) ?>
    </div>
    <?php Pjax::end(); ?>
</div>
