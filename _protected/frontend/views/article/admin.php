<?php

use yii\helpers\Html;
use kartik\date\DatePicker;
use common\helpers\CssHelper;
use common\widgets\grid\GridView;

/** @var $this yii\web\View */
/** @var $searchModel common\models\searches\ArticleSearch */
/** @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="articles-admin">

    <h1>

    <?= Html::encode($this->title) ?>

    <span class="pull-right">
        <?= Html::a(Yii::t('app', 'Create News'), ['create'], ['class' => 'btn btn-success']) ?>
    </span>  

    </h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'summary' => false,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'options' =>['style' => 'width:3%; white-space: normal;'],
                'contentOptions' => ['class' => 'kv-align-middle text-center'],
                'headerOptions' => ['class' => 'small text-center']
            ],
            //'id',
            // author
            [
                'attribute'=>'user_id',
                /*'value' => function ($data) {
                    $owner = $data->createdByUser ?? $data->updatedBy;
                    return isset($owner) ? $owner->getUserFullName() : null;
                },*/
            ],
            'title',
            [
                'attribute'=>'category',
                'filter' => $searchModel->categoryList,
                /*'value' => function ($data) {
                    return $data->getCategoryName($data->category);
                },
                'contentOptions'=>function($model, $key, $index, $column) {
                    return ['class'=>CssHelper::articleCategoryCss($model->categoryName)];
                }*/
            ],
            [
                'attribute' => 'start_date',
                'format' => 'date',
                //'filterType' => GridView::FILTER_DATE,
                /*'filterWidgetOptions' => [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'disableTouchKeyboard' => true,
                        'weekStart' => 1,
                        'autoclose' => true,
                    ]
                ],*/
            ],
            [
                'attribute' => 'end_date',
                'format' => 'date',
                //'filterType' => GridView::FILTER_DATE,
                /*'filterWidgetOptions' => [
                    'type' => DatePicker::TYPE_COMPONENT_APPEND,
                    'removeButton' => false,
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'todayHighlight' => true,
                        'disableTouchKeyboard' => true,
                        'weekStart' => 1,
                        'autoclose' => true,
                    ]
                ],*/
            ],
            [
                'attribute'=>'status',
                'filter' => $searchModel->articleStatusList,
                /*'value' => function ($data) {
                    return $data->getArticleStatusName($data->status);
                },
                'contentOptions'=>function($model, $key, $index, $column) {
                    return ['class'=>CssHelper::articleStatusCss($model->articleStatusName)];
                }*/
            ],

            [ // buttons
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Menu'),
                'options'=>['width'=>'80'],
                'visibleButtons' => [
                    'update' => function($model, $key, $index){
                        return Yii::$app->user->can('updateArticle', ['model' => $model]);
                    },
                    'delete' => function($model, $key, $index){
                        return Yii::$app->user->can('deleteArticle', ['model' => $model]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
