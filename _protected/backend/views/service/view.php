<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\ConstHelper;

/* @var yii\web\View $this */
/* @var common\models\Service $model */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Services'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-view">

    <p class="text-right">
        <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-warning btn-flat']) ?>
        <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'service_name',
            [
                'attribute' => 'created_by',
                'type' => 'raw',
                'value' => isset($model->createdBy) ? $model->createdBy->getUserFullName(true) : null
            ],
            'created_at:datetime',
            [
                'attribute' => 'updated_by',
                'type' => 'raw',
                'value' => isset($model->updatedBy) ? $model->updatedBy->getUserFullName(true) : null
            ],
            'updated_at:datetime',
            [
                'attribute'=>'status',
                'value' => ConstHelper::getStatusList($model->status),
            ],
        ],
    ]) ?>

</div>
