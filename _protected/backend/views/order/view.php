<?php

use yii\helpers\Html;
use common\models\User;
use common\models\Order;
use common\helpers\CssHelper;
use common\helpers\ArrayHelper;
use common\widgets\ISO639\Language;
use common\widgets\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-view box box-primary">

    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'order_number',
                [
                    'attribute' => 'agency_name',
                    'type' => 'raw',
                    'value' => $model->patient->customer->agency_name
                ],
                'patient_number',
                [
                    'attribute' => 'patient_name',
                    'type' => 'raw',
                    'value' => $model->patient->patientFullName
                ],
                [
                    'attribute' => 'patient.start_of_care',
                    'format' => 'date',
                    'value' => $model->patient->start_of_care
                ],
                [
                    'label' => 'Documents',
                    'format' => 'raw',
                    'value' => function($model){
                        return empty($model->getDocumentList()) ? null : implode(' ', $model->getDocumentList());
                    }
                ],
                'service_name',
                'certification_start_date:date',
                'certification_end_date:date',
                [
                    'attribute' => 'service_frequency',
                    'format' => 'html',
                    'value' => function($model){
                        $service_frequency = empty($model->service_frequency) ? null : $model->service_frequency;
                        if(!empty($service_frequency)){
                            $service_frequency .= Html::tag('span', $model->frequency_status === Order::ORDER_FREQUENCY_STATUS_APPROVED ? ' (Approved)' : ' (Pending)', ['class'=>'small'] );
                        }
                        return $service_frequency;
                    },
                    'contentOptions' => ['class' => CssHelper::orderFrequencyCss($model->frequency_status)],
                ],
                [
                    'attribute' => 'service_rate',
                    'format' => 'currency',
                ],
                [
                    'attribute' => 'patient.gender',
                    'type' => 'raw',
                    'value' => User::getGenderList($model->patient->gender)
                ],
                'physician_name',
                'physician_address',
                'physician_phone_number:phone',
                [
                    'label' => 'Patient Preferences',
                    'type' => 'row',
                    'format' => 'html',
                    'value' => function ($model) {
                        return $model->patient->patientPreferences;
                    }
                ],
                [
                    'label' => 'Assigned Therapists',
                    'format' => 'html',
                    'value' => function($model) {
                        return empty($model->providers) ? null : implode('  ', ArrayHelper::getColumn($model->providers, function($item) { return Html::tag('span', $item->userFullName, ['class' => 'label label-info label-sm']); }));
                    }
                ],
                'comment',
                [
                    'attribute' => 'submitted_by',
                    'type' => 'raw',
                    'value' => isset($model->submittedBy) ? $model->submittedBy->userFullName : null
                ],
                'submitted_at:dateTime',
                [
                    'attribute' => 'accepted_by',
                    'type' => 'raw',
                    'value' => isset($model->acceptedBy) ? $model->acceptedBy->userFullName : null
                ],
                'accepted_at:dateTime',
                [
                    'attribute' => 'completed_by',
                    'type' => 'raw',
                    'value' => isset($model->completedBy) ? $model->completedBy->userFullName : null
                ],
                'completed_at:dateTime',
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
                [
                    'attribute' => 'status',
                    'value' => function($model){
                        return Order::getStatusList($model->status);
                    },
                    'contentOptions' => ['class' => CssHelper::orderStatusCss($model->status)],
                ],
            ],
        ]) ?>
    </div>

    <h3><i class="fa fa-ambulance"></i> <?= Yii::t('app', 'Therapist Visits') ?></h3>
    <div class="row">
        <div class="col-lg-12">
            <?php if(empty($model->visits)): ?>
                <span class="text text-danger" aria-colspan="true"><i class="fa fa-ban"></i> <?= Yii::t('app', 'There are no visits scheduled')?></span>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach($model->visits as $num => $visit):?>
                        <li class="list-group-item">
                            <h4><?= Yii::$app->formatter->asDatetime($visit->visited_at) ?></h4>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <?php if(empty($visit->notes)): ?>
                                        <span class="text text-danger" aria-colspan="true"><i class="fa fa-ban"></i> <?= Yii::t('app', 'No Notes submitted yet for this visit.') ?></span>
                                    <?php else: ?>
                                        <?php foreach($visit->notes as $note): ?>
                                            <?php $status = $note->isPending() ? 'danger' : ($note->isAccepted() ? 'success' : 'warning'); ?>
                                            <?= Html::a($note->name, [$note->docRoute, 'id' => $note->id], ['class'=>'label label-'.$status, 'target' => '_blank']) ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
