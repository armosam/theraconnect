<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\CssHelper;
use common\helpers\ConstHelper;

/* @var $this yii\web\View */
/* @var $model common\models\NoteDischargeSummary */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discharge Summaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-discharge-summary-view box box-primary">
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
                'patient_name',
                'mrn',
                'pt',
                'ot',
                'diagnosis',
                'discharge_reason_no_care_needed',
                'discharge_reason_admission_hospital',
                'discharge_reason_admission_snf_icf',
                'discharge_reason_pt_assumed_responsibility',
                'discharge_reason_pt_moved_out_service_area',
                'discharge_reason_lack_of_progress',
                'discharge_reason_pt_refused_service',
                'discharge_reason_transfer_hha',
                'discharge_reason_transfer_op_rehab',
                'discharge_reason_death',
                'discharge_reason_lack_of_funds',
                'discharge_reason_transfer_hospice',
                'discharge_reason_transfer_personal_agency',
                'discharge_reason_md_request',
                'discharge_reason_other',
                'mental_oriented',
                'mental_forgetful',
                'mental_depressed',
                'mental_other',
                'functional_ind',
                'functional_sup',
                'functional_asst',
                'functional_dep',
                'mobile_ind',
                'mobile_sup',
                'mobile_asst',
                'mobile_dep',
                'device_wheelchair',
                'device_walker',
                'device_crutches',
                'device_cane',
                'device_other',
                'problem_identified1',
                'problem_identified2',
                'problem_identified3',
                'problem_identified4',
                'problem_identified5',
                'status_of_problem_at_discharge1',
                'status_of_problem_at_discharge2',
                'status_of_problem_at_discharge3',
                'status_of_problem_at_discharge4',
                'status_of_problem_at_discharge5',
                'summary_care_provided',
                'goals_attained_yes',
                'goals_attained_no',
                'goals_attained_partial',
                'discharge_plan_with_mid_supervision',
                'discharge_plan_hha',
                'discharge_plan_other',
                'notification_of_discharge_tc_to_md',
                'notification_of_discharge_tc_to_md_date',
                'notification_of_discharge_tc_to_pt',
                'notification_of_discharge_tc_to_pt_date',
                'physician_name',
                'therapist_name',
                'therapist_title',
                //'therapist_signature',
                'note_date:date',
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
