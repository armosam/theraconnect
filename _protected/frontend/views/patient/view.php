<?php

use yii\helpers\Html;
use kartik\dialog\Dialog;
use common\models\User;
use common\models\Patient;
use common\helpers\CssHelper;
use common\helpers\ConstHelper;
use common\widgets\ISO639\Language;
use common\widgets\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Patient */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="patient-view box box-primary">
    <div class="box-header">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-warning']) ?>
            <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php if(empty($model->activeOrders)): ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
            <?php endif; ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'patient_number',
                'start_of_care:date',
                [
                    'label' => 'Full Name',
                    'value' => function($model){
                        return $model->patientFullName;
                    }
                ],
                [
                    'attribute' => 'gender',
                    'type' => 'raw',
                    'value' => User::getGenderList($model->gender)
                ],
                'birth_date:date',
                'phone_number:phone',
                [
                    'label' => 'Patient Address',
                    'value' => function($model){
                        return $model->patientAddress;
                    }
                ],
                [
                    'attribute' => 'preferred_language',
                    'value' => function($model){
                        return empty($model->preferred_language) ? null : Language::englishNameByCode($model->preferred_language);
                    }
                ],
                [
                    'attribute' => 'preferred_gender',
                    'value' => function($model){
                        return empty($model->preferred_gender) ? null : User::getGenderList($model->preferred_gender);
                    }
                ],
                'emergency_contact_name',
                'emergency_contact_number:phone',
                'emergency_contact_relationship',
                [
                    'attribute' => 'status',
                    'value' => function($model){
                        return ConstHelper::getStatusList($model->status);
                    },
                    'contentOptions' => ['class' => CssHelper::statusCss($model->status)],
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
