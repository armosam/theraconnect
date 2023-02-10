<?php

use yii\helpers\Html;
use common\widgets\detail\DetailView;
use common\helpers\ConstHelper;

/* @var $this yii\web\View */
/* @var $model common\models\base\CredentialType */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Credential Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="credential-type-view box box-primary">
    <div class="box-header">
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
    </div>
    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'credential_type_name',
                'assigned_number_label',
                [
                    'attribute' => 'icon_class',
                    'format' => 'raw',
                    'value' => isset($model->icon_class) ? html::tag('i', ' '.$model->icon_class, ['class' => $model->icon_class]) : null
                ],
                [
                    'attribute' => 'created_by',
                    'value' => isset($model->createdBy) ? $model->createdBy->getUserFullName(true) : null
                ],
                'created_at:datetime',
                [
                    'attribute' => 'updated_by',
                    'value' => isset($model->updatedBy) ? $model->updatedBy->getUserFullName(true) : null
                ],
                'updated_at:datetime',
                'ordering:integer',
                [
                    'attribute'=>'status',
                    'value' => ConstHelper::getStatusList($model->status),
                ]
            ],
        ]) ?>
    </div>
</div>
