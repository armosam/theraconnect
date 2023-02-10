<?php

use yii\helpers\Html;
use common\widgets\detail\DetailView;
use common\models\UserCredential;

/* @var $this yii\web\View */
/* @var $model common\models\UserCredential */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Therapists'), 'url' => ['provider/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Credentials'), 'url' => ['index', 'uid' => $model->user_id]];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-credential-view box box-primary">
    <div class="box-header">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Back'), ['index', 'uid' => $model->user_id], ['class' => 'btn btn-warning btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Edit'), ['update', 'uid' => $model->user_id, 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
        </p>
    </div>
    <div class="box-body table-responsive ">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'credential_type_id',
                    'type' => 'raw',
                    'value' => function($model){
                        return UserCredential::credentialTypes($model->credential_type_id);
                    }
                ],
                [
                    'attribute' => 'assigned_number',
                    'value' => function($model){
                        return empty($model->assigned_number) ? null : '****'.substr($model->assigned_number, -4);
                    }
                ],
                'expire_date:date',
                [
                    'attribute' => 'status',
                    'type' => 'raw',
                    'value' => function($model){
                        return UserCredential::credentialStatuses($model->status);
                    }
                ],
                [
                    'attribute' => 'file_name',
                    'format' => 'html',
                    'value' => function($model){
                        return $model->file_name ? Html::a(UserCredential::credentialTypes($model->credential_type_id), ['user-credential/document', 'uid' => $model->user_id, 'id' => $model->id], ['class' => 'label label-success']) : null;
                    }
                ],
                [
                    'attribute' => 'file_size',
                    'type' => 'raw',
                    'value' => isset($model->file_size) ? round($model->file_size / 1024, 0) .' KB' : null
                ],
                [
                    'attribute' => 'created_by',
                    'type' => 'raw',
                    'value' => isset($model->createdBy) ? $model->createdBy->getUserFullName() : null
                ],
                'created_at:dateTime',
                [
                    'attribute' => 'updated_by',
                    'type' => 'raw',
                    'value' => isset($model->updatedBy) ? $model->updatedBy->getUserFullName() : null
                ],
                'updated_at:dateTime',
            ],
        ]) ?>
    </div>
</div>
