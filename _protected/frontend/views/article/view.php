<?php

use kartik\dialog\Dialog;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var $this yii\web\View */
/** @var $model common\models\Article */


$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->title;

Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="article-view">

    <h1><?= Html::encode($model->title) ?>

    <div class="pull-right">

    <?php if (Yii::$app->user->can('adminArticle')): ?>

        <?= Html::a(Yii::t('app', 'Back'), ['admin'], ['class' => 'btn btn-warning']) ?>

    <?php endif ?>

    <?php if (Yii::$app->user->can('updateArticle', ['model' => $model])): ?>

        <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>

    <?php endif ?>

    <?php if (Yii::$app->user->can('deleteArticle')): ?>

        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this article?'),
                'method' => 'post',
            ],
        ]) ?>

    <?php endif ?>
    
    </div>

    </h1>
    <div class="col-lg-12">

    <?= DetailView::widget([
        'model' => $model,
        'template' => '<tr><th>{label}</th><td style="width:80%;">{value}</td></tr>',
        'attributes' => [
            'id',
            [
                'label' => Yii::t('app', 'Photo') . ' / ' . Yii::t('app', 'Embedded Content'),
                'format' => 'raw',
                'value' => function($data){
                    if(!empty($data->embed_content)){
                        return Html::decode($data->embed_content);
                    }elseif(!empty($data->file_name) && is_file(Yii::getAlias(Yii::$app->params['articleImage']['destination_prefix'].$data->file_name))){
                        return Html::img(Yii::getAlias(Yii::$app->params['articleImage']['relative_prefix'].$data->file_name), ['width' => Yii::$app->params['articleImage']['width']]);
                    }
                },
            ],
            'start_date:date',
            'end_date:date',
            'title',
            'summary:text',
            'content:html',
            [
                'attribute' => 'created_by',
                'type' => 'raw',
                'value' => isset($model->createdByUser) ? $model->createdByUser->getUserFullName() : null
            ],
            'created_at:dateTime',
            [
                'attribute' => 'updated_by',
                'type' => 'raw',
                'value' => isset($model->updatedBy) ? $model->updatedBy->getUserFullName() : null
            ],
            'updated_at:datetime',
            'email_sent_by',
            'email_sent_at:datetime'
        ],
    ]) ?>
    </div>
</div>
