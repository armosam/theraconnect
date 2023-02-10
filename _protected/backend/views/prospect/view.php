<?php

use yii\widgets\DetailView;
use common\models\Prospect;
use common\helpers\CssHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Prospect */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Prospects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prospect-view box box-primary">
    <div class="box-header">
        <?php //Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
        <?php  /*Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])*/ ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'attribute' => 'full_name',
                    'label' => Yii::t('app', 'Applicant Name'),
                    'value' => function($model){
                        /** @var Prospect $model */
                        return $model->getProspectFullName();
                    },
                ],
                [
                    'attribute' => 'full_address',
                    'label' => Yii::t('app', 'Applicant address'),
                    'value' => function($model){
                        /** @var Prospect $model */
                        return $model->getProspectAddress();
                    },
                ],
                'email:email',
                'phone_number',
                [
                    'attribute' => 'service_id',
                    'value' => function($model){
                        return $model->service->service_name;
                    }
                ],
                'license_type',
                'license_number',
                'license_expiration_date:date',
                [
                    'attribute' => 'language',
                    'value' => function($model){
                        /** @var Prospect $model */
                        return $model->getProspectLanguage();
                    },
                ],
                [
                    'attribute' => 'covered_county',
                    'value' => function($model){
                        /** @var Prospect $model */
                        return $model->getProspectCoveredCounty();
                    },
                ],
                [
                    'attribute' => 'covered_city',
                    'value' => function($model){
                        /** @var Prospect $model */
                        return $model->getProspectCoveredCity();
                    },
                ],
                'note',
                [
                    'attribute' => 'status',
                    'value' => function($model){
                        return Prospect::getStatusList($model->status);
                    },
                    'contentOptions' => ['class' => CssHelper::statusCss($model->status)],
                ],
                'created_at:dateTime',
                [
                    'attribute' => 'updated_by',
                    'type' => 'raw',
                    'value' => isset($model->updatedBy) ? $model->updatedBy->getUserFullName() : null
                ],
                'updated_at:dateTime',
                [
                    'attribute' => 'accepted_by',
                    'type' => 'raw',
                    'value' => isset($model->acceptedBy) ? $model->acceptedBy->getUserFullName() : null
                ],
                'accepted_at:dateTime',
                [
                    'attribute' => 'rejected_by',
                    'type' => 'raw',
                    'value' => isset($model->rejectedBy) ? $model->rejectedBy->getUserFullName() : null
                ],
                'rejected_at:dateTime',

                //'id',
                //'first_name',
                //'last_name',
                //'address',
                //'city',
                //'state',
                //'zip_code',
                //'country',
                //'language',
                //'covered_county',
                //'covered_city',
                //'status',
                //'created_by',
                //'service_id',
                //'ip_address',
                /*'rejected_by',
                'rejected_at',
                'rejection_reason',*/
            ],
        ]) ?>
    </div>
</div>
