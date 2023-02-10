<?php

use yii\helpers\Html;
use kartik\dialog\Dialog;
use common\models\UserCredential;
use common\widgets\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserCredential */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Credentials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="user-credential-view box box-primary">
    <div class="box-header">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-warning']) ?>
            <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
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
                    'attribute' => 'approved_by',
                    'type' => 'raw',
                    'value' => isset($model->approvedBy) ? $model->approvedBy->getUserFullName() : null
                ],
                'approved_at:dateTime',
                [
                    'attribute' => 'file_name',
                    'format' => 'html',
                    'value' => function($model){
                        return $model->file_name ? Html::a(UserCredential::credentialTypes($model->credential_type_id), ['user-credential/document', 'id' => $model->id], ['class' => 'label label-success']) : null;
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
            ]
        ]) ?>
    </div>
</div>
