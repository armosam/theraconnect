<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\Service;
use common\widgets\grid\GridView;
use common\helpers\ConstHelper;
use common\helpers\CssHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\ServiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-index box box-primary">

    <?php Pjax::begin(); ?>

    <p class="box-header with-border text-right">
        <?= Html::a(Yii::t('app', 'Create Service'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </p>

    <div class="box-body table-responsive no-padding">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        /*'hover' => true,
        'responsive' => true,
        'responsiveWrap' => false,*/
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'options' =>['style' => 'width:5%; white-space: normal;'],
                'contentOptions'=>['class'=>' text-center'],
                'headerOptions' => ['class' => 'small text-center']
            ],
            /*[
                'attribute' => 'id',
                'options' =>['style' => 'width:4%; white-space: normal;'],
                'contentOptions'=>['class'=>' text-center'],
                'headerOptions' => ['class' => 'small text-center']
            ],*/
            [
                'attribute' => 'service_name',
                'format' => 'html',
                'value' => function ($data) {
                    return $data->service_name;
                },
                'options' =>['style' => 'width:75%; white-space: normal;'],
                'headerOptions' => ['class' => 'small text-center']
            ],
            // 'created_by',
            // 'created_at',
            // 'updated_by',
            // 'updated_at',
            /*[
                'attribute' => 'ordering',
                'filter' => range(0,(Service::find()->count()+5)),
                'options' =>['style' => 'width:10%; white-space: normal;'],
                'headerOptions' => ['class' => 'small text-center']
            ],*/
            [
                'attribute'=>'status',
                'filter' =>  ConstHelper::getStatusList(),
                'value' => function ($data) {
                    return ConstHelper::getStatusList($data->status);
                },
                'contentOptions'=>function($model, $key, $index, $column) {
                    return ['class'=>CssHelper::statusCss($model->status) . ' text-center'];
                },
                'options' =>['style' => 'width:10%; white-space: normal;'],
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
                            return Html::a('', Url::to(['/service/disable', 'id' => $model->id]),
                                ['title' => Yii::t('app', 'Disable Service'),
                                    'class'=>'fa fa-ban',
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Are you sure you want to disable this service?'),
                                        'method' => 'post']
                                ]
                            );
                        }
                        return Html::a('', Url::to(['/service/enable', 'id' => $model->id]),
                            ['title' => Yii::t('app', 'Enable Service'),
                                'class'=>'fa fa-check',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to enable this service?'),
                                    'method' => 'post']
                            ]
                        );
                    },
                ],
                'options' =>['style' => 'width:10%; white-space: normal;'],
            ], // ActionColumn
        ],
    ]); ?>
    </div>

    <?php Pjax::end(); ?>
</div>
