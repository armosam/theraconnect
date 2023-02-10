<?php
/** @var $this yii\web\View */
/** @var $model common\models\User */

use yii\helpers\Html;
use kartik\dialog\Dialog;
use common\models\User;
use common\helpers\ConstHelper;
use common\models\Language;
use common\widgets\detail\DetailView;
use common\widgets\ajax\modal\ModalAjaxWidget;

$this->params['breadcrumbs'][] = Yii::t('app', $this->title);

Dialog::widget(['overrideYiiConfirm' => true]);
?>

    <p class="text-right">
        <?= Html::a(Html::tag('span', '', ['class'=>'glyphicon glyphicon-home']).' '.Yii::t('app', 'Home'), ['site/index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a(Html::tag('span', '', ['class'=>'glyphicon glyphicon-edit']).' '.Yii::t('app', 'Edit'), ['update'], ['class' => 'btn btn-primary']) ?>
    </p>

    <div class="col-lg-3" style="margin-top: 20px">
        <?= Html::img('/site/avatar?id=' . $model->id . '&s=' . Yii::$app->params['picturePreferredSourceFileSystem'], ['style' => 'width:100%', 'class' => 'img-thumbnail img-rounded img-responsive']) ?>
        <div class="alert <?= $model->status == User::USER_STATUS_ACTIVE ? 'alert-success' : 'alert-danger' ?> text-center">
            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
            <label class="small"><?= Yii::t('app', 'Status') ?>: <strong class="small"><?= $model->getUserStatusName($model->status) ?> <?= $model::getRoleList($model->role->item_name) ?></strong></label>
        </div>
    </div>
    <br>
    <div class="col-lg-9">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'attribute' => 'item_name',
                    'value' => $model->getRoleName(),
                ],
                [
                    'attribute' => Yii::$app->user->identity->isProvider ? 'title' : 'agency_name',
                    'visible' => in_array(User::currentLoggedUser()->role->item_name, [User::USER_CUSTOMER, User::USER_PROVIDER])
                ],
                [
                    'attribute' => 'rep_position',
                    'visible' => in_array(User::currentLoggedUser()->role->item_name, [User::USER_CUSTOMER])
                ],
                'first_name:text',
                'last_name:text',
                'username:text',
                [
                    'attribute' => 'email',
                    'format' => 'raw',
                    'value' => $model->verificationCheck('email')
                ],
                [
                    'attribute' => 'phone1',
                    'format' => 'raw',
                    'value' => $model->verificationCheck('phone1')
                ],
                [
                    'attribute' => 'phone2',
                    'format' => 'raw',
                    'value' => $model->verificationCheck('phone2')
                ]
            ],
        ]) ?>
    </div>

    <div class="col-lg-12">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                [
                    'label' => Yii::t('app', 'Providing Service'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        /** @var User $model */
                        return empty($model->service) ? null : $model->service->service_name;
                    },
                    'visible' => ($model->role->item_name === User::USER_PROVIDER)
                ],
                [
                    'attribute' => 'service_rate',
                    'format' => 'currency',
                    'value' => function ($model) {
                        return empty($model->service_rate) ? null : $model->service_rate;
                    },
                    'visible' => ($model->role->item_name === User::USER_PROVIDER)
                ],
                [
                    'attribute' => 'gender',
                    'value' => empty($model->gender) ? null : User::getGenderList($model->gender),
                ],
                [
                    'attribute' => 'full_address',
                    'label' => Yii::t('app', 'Address'),
                    'value' => function ($model) {
                        /** @var User $model */
                        return $model->getUserAddress();
                    },
                ],
                [
                    'attribute' => 'language',
                    'value' => function ($model) {
                        /** @var User $model */
                        return $model->getUserLanguage();
                    },
                    'visible' => ($model->role->item_name === User::USER_PROVIDER)
                ],
                [
                    'attribute' => 'covered_county',
                    'value' => function ($model) {
                        /** @var user $model */
                        return $model->getUserCoveredCounty();
                    },
                    'visible' => ($model->role->item_name === User::USER_PROVIDER)
                ],
                [
                    'attribute' => 'covered_city',
                    'value' => function ($model) {
                        /** @var User $model */
                        return $model->getUserCoveredCity();
                    },
                    'visible' => ($model->role->item_name === User::USER_PROVIDER)
                ],
                'emergency_contact_name',
                'emergency_contact_number',
                'emergency_contact_relationship',
                'website_address:url',
                'note:text',
                'created_at:datetime',
                'updated_at:datetime',

                //'address',
                //'city',
                //'state',
                //'zip_code',
                //'timezone',
                /*[
                    'attribute'=>'userRating.current_rating',
                    'format' => 'raw',
                    'value' => empty($model->userRating->current_rating) ? null : User::getRatingList(true, $model->userRating->current_rating)
                ],*/
                /*[
                    'attribute'=>'suspended_by',
                    'value' => $model->getUserFullName(true, false, true, $model->suspended_by)
                ],*/
                //'suspended_at:datetime',
                //'suspension_reason',
                /*[
                    'attribute' => 'terminated_by',
                    'value' => $model->getUserFullName(true, false, true, $model->terminated_by)
                ],
                'terminated_at:datetime',
                'termination_reason',*/
                /*[
                    'attribute'=>'created_by',
                    'value' => $model->getUserFullName(true, false, true, $model->created_by),
                ],*/
                /*[
                    'attribute'=>'updated_by',
                    'value' => $model->getUserFullName(true, false, true, $model->updated_by),
                ],*/
            ],
        ]) ?>
        <div class="pull-left">
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-home']) . ' ' . Yii::t('app', 'Home'), ['site/index'], ['class' => 'btn btn-warning']) ?>
            <?= Html::a(Html::tag('span', '', ['class' => 'glyphicon glyphicon-edit']) . ' ' . Yii::t('app', 'Edit'), ['update'], ['class' => 'btn btn-primary']) ?>
        </div>
    </div>

<?= ModalAjaxWidget::widget([
    'showBtn' => false,
    'modalTitle' => Yii::t('app', 'Phone Number Verification'),
    'targetId' => 'phone_verification_modal_window'
])?>