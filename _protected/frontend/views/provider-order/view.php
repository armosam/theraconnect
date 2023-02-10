<?php

use yii\helpers\Html;
use yii\jui\Accordion;
use common\models\User;
use common\models\Order;
use common\helpers\CssHelper;
use common\helpers\ArrayHelper;
use common\widgets\ISO639\Language;
use common\widgets\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="patient-view box box-primary">
    <div class="box-header">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'View Scheduled Visits'), ['provider-calendar/index'], ['class' => 'btn btn-primary']) ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">
        <section class="table-responsive hidden-xs">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                //'order_number',
                'patient_number',
                'patient_name',
                [
                    'label' => 'Patient Gender',
                    'type' => 'raw',
                    'value' => function($model) {
                        return User::getGenderList($model->patient->gender);
                    }
                ],
                [
                    'label' => 'Patient Address',
                    'type' => 'raw',
                    'value' => function($model){
                        return $model->patient->patientAddress ?: null;
                    },
                ],
                [
                    'label' => 'Patient Phone Number',
                    'format' => 'html',
                    'value' => function($model){
                        return empty($model->patient->phone_number) ? null : Html::tag('a', $model->patient->phone_number, ['href' => 'tel:'.$model->patient->phone_number]);
                    },
                ],
                [
                    'label' => 'Patient Preferences',
                    'type' => 'row',
                    'format' => 'html',
                    'value' => function ($model) {
                        return $model->patient->patientPreferences ?: null;
                    }
                ],
                [
                    'attribute' => 'start_of_care',
                    'type' => 'raw',
                    'format' => 'date',
                    'value' => function($model) {
                        return $model->patient->start_of_care ?: null;
                    }
                ],
                'certification_start_date:date',
                'certification_end_date:date',
                [
                    'label' => 'Documents',
                    'type' => 'raw',
                    'format' => 'html',
                    'value' => function($model){
                        return empty($model->getDocumentList('provider-order')) ? null : implode(' ', $model->getDocumentList('provider-order'));
                    }
                ],
                'service_name',
                [
                    'attribute' => 'service_frequency',
                    'type' => 'raw',
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
                    'type' => 'raw',
                    'format' => 'currency'
                ],
                'physician_name',
                'physician_address',
                [
                    'attribute' => 'physician_phone_number',
                    'type' => 'raw',
                    'format' => 'html',
                    'value' => function($model){
                        return empty($model->physician_phone_number) ? null : Html::tag('a', $model->physician_phone_number, ['href' => 'tel:'.$model->physician_phone_number]);
                    },
                ],
                [
                    'label' => 'Assigned Therapists',
                    'type' => 'raw',
                    'format' => 'html',
                    'value' => function($model) {
                        return implode('  ', ArrayHelper::getColumn($model->providers, function($item) { return Html::tag('span', $item->userFullName, ['class' => 'label label-info label-sm']); }));
                    }
                ],
                'comment',
                [
                    'attribute' => 'submitted_by',
                    'type' => 'raw',
                    'value' => function($model) {
                        return isset($model->submittedBy) ? $model->submittedBy->userFullName : null;
                    }
                ],
                'submitted_at:dateTime',
                [
                    'attribute' => 'accepted_by',
                    'type' => 'raw',
                    'value' => function($model) {
                        return isset($model->acceptedBy) ? $model->acceptedBy->userFullName : null;
                    }
                ],
                'accepted_at:dateTime',
                [
                    'attribute' => 'completed_by',
                    'type' => 'raw',
                    'value' => function($model) {
                        return isset($model->completedBy) ? $model->completedBy->userFullName : null;
                    }
                ],
                'completed_at:dateTime',
                [
                    'attribute' => 'created_by',
                    'type' => 'raw',
                    'value' => function($model) {
                        return isset($model->createdBy) ? $model->createdBy->getUserFullName(true) : null;
                    }
                ],
                'created_at:dateTime',
                [
                    'attribute' => 'updated_by',
                    'type' => 'raw',
                    'value' => function($model) {
                        isset($model->updatedBy) ? $model->updatedBy->getUserFullName(true) : null;
                    }
                ],
                'updated_at:dateTime',
                [
                    'attribute' => 'status',
                    'type' => 'raw',
                    'value' => function($model){
                        return Order::getStatusList($model->status);
                    },
                    'contentOptions' => ['class' => CssHelper::orderStatusCss($model->status)],
                ],
            ],
        ]) ?>
        </section>
        <div class="visible-xs">
            <?= Accordion::widget([
                'items' => [
                    [
                        'header' => 'Patient Information',
                        'content' => Html::decode(Html::ul([
                            'MR #: ' . $model->patient->patient_number,
                            'Patient Name: ' . $model->patient->patientFullName,
                            'Patient Gender: ' . (empty($model->patient->gender) ? 'Not Set' : User::getGenderList($model->patient->gender)),
                            'Patient Address: ' . (empty($model->patient->patientAddress) ? 'Not Set' : $model->patient->patientAddress),
                            'Patient Phone: ' . (empty($model->patient->phone_number) ? 'Not Set' : Html::tag('a', $model->patient->phone_number, ['href' => 'tel:' . $model->patient->phone_number])),
                            'Patient Preferences: ' . (empty($model->patient->patientPreferences) ? 'Not Set' : $model->patient->patientPreferences),
                            'Start Of Care: ' . (empty($model->patient->start_of_care) ? 'Not Set' : Yii::$app->formatter->asDate($model->patient->start_of_care)),
                            'Certification Start: ' . (empty($model->certification_start_date) ? 'Not Set' : Yii::$app->formatter->asDate($model->certification_start_date)),
                            'Certification End: ' . (empty($model->certification_end_date) ? 'Not Set' : Yii::$app->formatter->asDate($model->certification_end_date)),
                            'Service Name: ' . (empty($model->service) ? 'Not Set' : $model->service->service_name),
                            'Service Frequency: ' . (empty($model->service_frequency) ? 'Not Set' : $model->service_frequency),
                            'Service Rate($): ' . (empty($model->service_rate) ? 'Not Set' : Yii::$app->formatter->asCurrency($model->service_rate)),
                            'Status: ' . Order::getStatusList($model->status)
                        ], ['style' => 'padding: 5px 20px'])),
                    ],
                    [
                        'header' => 'Physician Information',
                        'content' => Html::decode(Html::ul([
                            'Physician Name: ' . (empty($model->physician_name) ? 'Not Set' : $model->physician_name),
                            'Physician Address: ' . (empty($model->physician_address) ?  'Not Set' : $model->physician_address),
                            'Physician Phone: ' . (empty($model->physician_phone_number) ? 'Not Set' : Html::tag('a', $model->physician_phone_number, ['href' => 'tel:'.$model->physician_phone_number])),
                        ], ['style' => 'padding: 5px 20px'])),
                        'options' => ['tag' => 'div'],
                    ],
                    [
                        'header' => 'Documents',
                        'content' => Html::decode(Html::ul( $model->getDocumentList('provider-order'), ['style' => 'padding: 5px 20px'] )),
                        'options' => ['tag' => 'div'],
                    ],
                    [
                        'header' => 'Assigned Therapists',
                        'content' => Html::decode(Html::ul( ArrayHelper::getColumn($model->providers, function($item) { return Html::tag('span', $item->userFullName, ['class' => 'label label-info label-sm']); }), ['style' => 'padding: 5px 20px'] )),
                        'options' => ['tag' => 'div'],
                    ],
                    [
                        'header' => 'Submission Information',
                        'content' => Html::decode(Html::ul([
                            'Submitted By: ' . (empty($model->submittedBy) ? 'Not Set' : $model->submittedBy->getUserFullName()),
                            'Submitted At: ' . (empty($model->submitted_at) ? 'Not Set' : Yii::$app->formatter->asDatetime($model->submitted_at)),
                            'Created By: ' . (empty($model->createdBy) ? 'Not Set' : $model->createdBy->getUserFullName()),
                            'Created At: ' . (empty($model->created_at) ? 'Not Set' : Yii::$app->formatter->asDatetime($model->created_at)),
                            'Updated By: ' . (empty($model->updatedBy) ? 'Not Set' : $model->updatedBy->getUserFullName()),
                            'Updated At: ' . (empty($model->updated_at) ? 'Not Set' : Yii::$app->formatter->asDatetime($model->updated_at))
                        ], ['style' => 'padding: 5px 20px'])),
                        'options' => ['tag' => 'div'],
                    ],
                    [
                        'header' => 'Comments',
                        'content' => $model->comment ?: '',
                        'options' => ['tag' => 'div', 'style' => 'padding: 15px'],
                    ],
                ],
                'options' => ['tag' => 'div'],
                'itemOptions' => ['tag' => 'div', 'style' => 'padding: 5px 5px 5px 10px;'],
                'headerOptions' => ['tag' => 'h3'],
                'clientOptions' => ['collapsible' => true],
            ]) ?>
        </div>
    </div>
</div>
