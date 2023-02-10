<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\helpers\CssHelper;

/** @var $this yii\web\View */
/** @var $model common\models\User */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = ['label' => $this->title];

?>

<div class="user-notification-view box box-primary">

    <p class="text-right">
        <?= Html::a('<span class="glyphicon glyphicon-user"></span> ' . Yii::t('app', 'Users'), ['user/index'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('<span class="glyphicon glyphicon-edit"></span> ' . Yii::t('app', 'Edit'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <h5><?= $model->getUserFullName() ?><?php echo(!empty($model->email) ? (' (' . $model->email . ') ') : '') ?></h5>
    <h6><?= !empty($model->phone1) ? (Yii::t('app', 'Phone: {phone_number}', ['phone_number' => Yii::$app->formatter->asPhone($model->phone1)])) : '' ?></h6>
    <h6><?= !empty($model->phone2) ? (Yii::t('app', 'Phone: {phone_number}', ['phone_number' => Yii::$app->formatter->asPhone($model->phone2)])) : '' ?></h6>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'id',
                'captionOptions' => ['style' => 'font-size:large;text-align: center;'],
                'label' => Yii::t('app', 'Receive Email Notifications'),
                'value' => '',
            ],
            [
                'attribute' => 'note_email_news_and_promotions',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_news_and_promotions) . ' text-center']
            ],
            [
                'attribute' => 'note_email_account_updated',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_account_updated) . ' text-center']
            ],
            [
                'attribute' => 'note_email_order_submitted',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_order_submitted) . ' text-center'],
                //'visible' => ($model->role->item_name == \common\models\User::USER_PROVIDER)
            ],
            [
                'attribute' => 'note_email_order_accepted',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_order_accepted) . ' text-center'],
                //'visible' => ($model->role->item_name == \common\models\User::USER_CUSTOMER)
            ],
            [
                'attribute' => 'note_email_order_rejected',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_order_rejected) . ' text-center']
            ],
            [
                'attribute' => 'note_email_order_canceled',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_order_canceled) . ' text-center']
            ],
            [
                'attribute' => 'note_email_rate_service',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_rate_service) . ' text-center']
            ],
            [
                'attribute' => 'note_email_order_reminder',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_email_order_reminder) . ' text-center']
            ],
            [
                'attribute' => 'id',
                'captionOptions' => ['style' => 'font-size:large;text-align: center;'],
                'label' => Yii::t('app', 'Receive SMS Notifications'),
                'value' => '',
            ],
            [
                'attribute' => 'note_sms_news_and_promotions',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_news_and_promotions) . ' text-center']
            ],
            [
                'attribute' => 'note_sms_account_updated',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_account_updated) . ' text-center']
            ],
            [
                'attribute' => 'note_sms_order_submitted',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_order_submitted) . ' text-center'],
                //'visible' => ($model->role->item_name == \common\models\User::USER_PROVIDER)
            ],
            [
                'attribute' => 'note_sms_order_accepted',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_order_accepted) . ' text-center'],
                //'visible' => ($model->role->item_name == \common\models\User::USER_CUSTOMER)
            ],
            [
                'attribute' => 'note_sms_order_rejected',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_order_rejected) . ' text-center']
            ],
            [
                'attribute' => 'note_sms_order_canceled',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_order_canceled) . ' text-center']
            ],
            [
                'attribute' => 'note_sms_rate_service',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_rate_service) . ' text-center']
            ],
            [
                'attribute' => 'note_sms_order_reminder',
                'format' => function ($value) {
                    return \common\helpers\ConstHelper::getYesNoList($value);
                },
                'contentOptions' => ['class' => CssHelper::yesNoCss($model->note_sms_order_reminder) . ' text-center']
            ],
            'created_at:dateTime',
            'updated_at:dateTime',
        ],
    ]) ?>

</div>