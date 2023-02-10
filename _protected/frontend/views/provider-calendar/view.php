<?php

use common\widgets\detail\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Visit */

?>

<div class="visit-view box box-primary">
    <div class="box-header">
        <h4><?= $model->order->patient->patientFullName ?></h4>
        <h4 class="text-danger"><?= Yii::$app->formatter->asDatetime($model->visited_at) ?></h4>
    </div>
    <div class="box-body no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'label' => 'MR#',
                    'format' => 'html',
                    'value' => function($model) {
                        return $model->order->patient->patient_number ;
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Start of care (SOC)',
                    'type' => 'raw',
                    'format' => 'html',
                    'value' => function($model) {
                        return Yii::$app->formatter->asDate($model->order->patient->start_of_care);
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Certification Start Date',
                    'type' => 'raw',
                    'format' => 'html',
                    'value' => function($model) {
                        return Yii::$app->formatter->asDate($model->order->certification_start_date);
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Certification End Date',
                    'type' => 'raw',
                    'format' => 'html',
                    'value' => function($model) {
                        return Yii::$app->formatter->asDate($model->order->certification_end_date);
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Patient Name',
                    'type' => 'raw',
                    'value' => function($model) {
                        return $model->order->patient->patientFullName;
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Patient BirthDate',
                    'type' => 'raw',
                    'value' => function($model) {
                        return Yii::$app->formatter->asDate($model->order->patient->birth_date);
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Patient Address',
                    'format' => 'html',
                    'value' => function($model) {
                        return $model->order->patient->patientAddress ;
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Patient Phone Number',
                    'format' => 'html',
                    'value' => function($model) {
                        return Yii::$app->formatter->asPhone($model->order->patient->phone_number);
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Service Name',
                    'type' => 'raw',
                    'value' => function($model) {
                        return $model->order->service->service_name;
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'label' => 'Service Rate($)',
                    'format' => 'currency',
                    'value' => function($model) {
                        return $model->order->service_rate;
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'attribute' => 'visited_at',
                    'format' => 'dateTime',
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'attribute' => 'comment',
                    'format' => 'text',
                    'captionOptions' => ['class' => 'small']
                ],
                /*[
                    'label' => 'Service Request #',
                    'format' => 'html',
                    'value' => function($model) {
                        return $model->order->order_number ;
                    },
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'attribute' => 'created_by',
                    'type' => 'raw',
                    'value' => isset($model->createdBy) ? $model->createdBy->getUserFullName(true) : null,
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'dateTime',
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'attribute' => 'updated_by',
                    'type' => 'raw',
                    'value' => isset($model->updatedBy) ? $model->updatedBy->getUserFullName(true) : null,
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'dateTime',
                    'captionOptions' => ['class' => 'small']
                ],
                [
                    'attribute' => 'status',
                    'value' => function($model){
                        return Order::getStatusList($model->status);
                    },
                    'contentOptions' => ['class' => CssHelper::orderStatusCss($model->status)],
                ],*/
            ],
        ]) ?>
    </div>

    <h3><i class="fa fa-ambulance"></i> <?= Yii::t('app', 'Visit Notes') ?></h3>
    <div class="row">
        <div class="col-lg-12">
            <?php if (empty($model->notes)): ?>
                <span class="text text-danger" aria-colspan="true"><i class="fa fa-ban"></i> <?= Yii::t('app', 'There are no any notes for this visit') ?></span>
            <?php else: ?>
                <ul class="list-group">
                    <li class="list-group-item">
                        <?php foreach ($model->notes as $num => $note): ?>
                            <?php if(Yii::$app->user->can('manageNote', ['model' => $note])): ?>
                                <?php $status = $note->isPending() ? 'danger' : ($note->isAccepted() ? 'success' : 'warning'); ?>
                                <?= Html::a($note->name, [$note->docRoute, 'id' => $note->id], ['class' => 'label label-' . $status, 'target' => '_blank']) ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="hint-block">
        Color Info: <span class="label label-danger">Pending</span> <span class="label label-warning">Submitted</span> <span class="label label-success">Approved</span>
    </div>
</div>