<?php

use yii\helpers\Html;
use common\models\User;
use common\helpers\ConstHelper;
use common\widgets\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view box box-primary">
    <div class="box-header">
        <p class="text-right">
            <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-warning btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-flat',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this record?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">
        <div class="col-lg-3">
            <?= Html::img('/site/avatar?id='.$model->id.'&s='.Yii::$app->params['picturePreferredSourceFileSystem'], ['style'=>'width:99%', 'class' => 'img-thumbnail img-rounded img-responsive']) ?>
            <div class="well well-sm <?= $model->status == User::USER_STATUS_ACTIVE ? 'alert-success' : 'alert-danger' ?> text-center">
                <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                <label class="small"><?=Yii::t('app', 'Status')?>: <strong class="small"><?= $model->getUserStatusName($model->status) ?> <?= $model::getRoleList($model->role->item_name) ?></strong></label>
            </div>
        </div>
        <div class="col-lg-9">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    [
                        'attribute'=>'item_name',
                        'value' => $model->getRoleName(),
                    ],
                    [
                        'attribute' => $model->role->item_name === User::USER_PROVIDER ? 'title' : 'agency_name',
                        'visible' => in_array($model->role->item_name, [User::USER_CUSTOMER, User::USER_PROVIDER])

                    ],
                    [
                        'attribute' => 'rep_position',
                        'visible' => in_array($model->role->item_name, [User::USER_CUSTOMER])

                    ],
                    'first_name:text',
                    'last_name:text',
                    'username:text',
                    'email:email',
                    [
                        'attribute'=>'phone1',
                        'value' =>  !empty($model->phone1) ? Yii::$app->formatter->asPhone($model->phone1) : null,
                    ],
                    [
                        'attribute'=>'phone2',
                        'value' => !empty($model->phone2) ? Yii::$app->formatter->asPhone($model->phone2) : null,
                    ],
                    /*'address',
                    'city',
                    'state',
                    'zip_code'*/
                ],
            ]) ?>
        </div>

        <div class="col-lg-12">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'label' => 'Full Address',
                    'value' => $model->getUserAddress(),
                ],
                [
                    'attribute' => 'gender',
                    'value' => $model->getGenderList($model->gender),
                ],
                [
                    'label' => Yii::t('app', 'Therapist Service'),
                    'type' => 'raw',
                    'value' => function($model) {
                        return $model->service ? $model->service->service_name : null;
                    },
                    'visible' => ($model->role->item_name === User::USER_PROVIDER)
                ],
                [
                    'attribute' => 'service_rate',
                    'format' => 'currency',
                    'value' => function($model) {
                        return empty($model->service_rate) ? null : $model->service_rate;
                    },
                    'visible' => ($model->role->item_name === User::USER_PROVIDER)
                ],
                [
                    'attribute' => 'language',
                    'value' => function($model){
                        /** @var User $model */
                        return $model->getUserLanguage();
                    },
                    'visible' => in_array($model->role->item_name, [User::USER_PROVIDER])
                ],
                [
                    'attribute' => 'covered_county',
                    'value' => function($model){
                        /** @var user $model */
                        return $model->getUserCoveredCounty();
                    },
                    'visible' => in_array($model->role->item_name, [User::USER_PROVIDER])
                ],
                [
                    'attribute' => 'covered_city',
                    'value' => function($model){
                        /** @var User $model */
                        return $model->getUserCoveredCity();
                    },
                    'visible' => in_array($model->role->item_name, [User::USER_PROVIDER])
                ],
                'emergency_contact_name',
                'emergency_contact_number',
                'emergency_contact_relationship',
                'website_address:url',
                'note',
                'created_at:datetime',
                [
                    'attribute'=>'updated_by',
                    'value' => $model->getUserFullName(true, false, true, $model->updated_by),
                ],
                'updated_at:datetime',
                /*[
                    'attribute'=>'timezone',
                    'value' => ConstHelper::getTimeZoneList($model->timezone),
                ],*/
                //'lat',
                //'lng',
                /*[
                    'attribute'=>'userRating.current_rating',
                    'format' => 'raw',
                    'value' => User::getRatingList(false, $model->userRating->current_rating) ?: null
                ],*/
                /*[
                    'attribute'=>'suspended_by',
                    'value' => $model->getUserFullName(true, false, true, $model->suspended_by)
                ],
                'suspended_at:datetime',
                'suspension_reason',
                [
                    'attribute' => 'terminated_by',
                    'value' => $model->getUserFullName(true, false, true, $model->terminated_by)
                ],
                'terminated_at:datetime',
                'termination_reason',
                [
                    'attribute'=>'created_by',
                    'value' => $model->getUserFullName(true, false, true, $model->created_by),
                ],*/
            ],
        ]) ?>

        <p class="text-left">
            <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-warning btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
            <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-flat',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this record?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
        </div>
    </div>

</div>
