<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\CssHelper;
use common\helpers\ConstHelper;

/* @var $this yii\web\View */
/* @var $model common\models\NoteRouteSheet */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Route Sheets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-route-sheet-view box box-primary">
    <div class="box-header">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-warning btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-flat hidden',
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
                //'id',
                //'order_id',
                //'visit_id',
                //'provider_id',
                [
                    'attribute' => 'status',
                    'value' => function($model){
                        return ConstHelper::getNoteStatusList($model->status);
                    },
                    'contentOptions' => ['class' => CssHelper::orderStatusCss($model->status)],
                ],
                'health_agency',
                'patient_name',
                'mrn',
                'visit_code',
                'note_date:date',
                'time_in:time',
                'time_out:time',
                'visit_total_time:duration',
                //'patient_signature',
                'therapist_name',
                'therapist_title',
                //'therapist_signature',
                [
                    'attribute' => 'created_by',
                    'type' => 'raw',
                    'value' => isset($model->createdBy) ? $model->createdBy->getUserFullName(true) : null
                ],
                'created_at:dateTime',
                [
                    'attribute' => 'updated_by',
                    'type' => 'raw',
                    'value' => isset($model->updatedBy) ? $model->updatedBy->getUserFullName(true) : null
                ],
                'updated_at:dateTime',
            ],
        ]) ?>
    </div>
</div>
