<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\dialog\Dialog;

/** @var $this yii\web\View */
/** @var $model common\models\User */
/** @var $form yii\bootstrap\ActiveForm */

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="user-notification-form">

    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'inputOptions' => [
                'class' => 'col-xs-12 col-sm-12 col-lg-12',
            ],
            'labelOptions' => [
                'style' => 'font-size:12px',
            ],
            'errorOptions' => [
                'class' => 'help-block',
                'tag' => 'div'
            ]
        ],
    ]); ?>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <h4 class="top-title"><?= Yii::t('app', 'Receive Email Notifications') ?></h4>
            <div class="switchers">
                <?= $form->field($model, 'note_email_news_and_promotions')->checkbox(['checked' => ($model->note_email_news_and_promotions === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_email_account_updated')->checkbox(['checked' => ($model->note_email_account_updated === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_email_order_submitted')->checkbox(['checked' => ($model->note_email_order_submitted === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_email_order_accepted')->checkbox(['checked' => ($model->note_email_order_accepted === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_email_order_rejected')->checkbox(['checked' => ($model->note_email_order_rejected === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_email_order_canceled')->checkbox(['checked' => ($model->note_email_order_canceled === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_email_rate_service')->checkbox(['checked' => ($model->note_email_rate_service === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_email_order_reminder')->checkbox(['checked' => ($model->note_email_order_reminder === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>
            </div>

        </div>
        <div class="col-sm-6 col-xs-12">
            <h4 class="top-title"><?= Yii::t('app', 'Receive SMS Notifications') ?></h4>
            <div class="switchers">
                <?= $form->field($model, 'note_sms_news_and_promotions')->checkbox(['checked' => ($model->note_sms_news_and_promotions === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_sms_account_updated')->checkbox(['checked' => ($model->note_sms_account_updated === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_sms_order_submitted')->checkbox(['checked' => ($model->note_sms_order_submitted === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_sms_order_accepted')->checkbox(['checked' => ($model->note_sms_order_accepted === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_sms_order_rejected')->checkbox(['checked' => ($model->note_sms_order_rejected === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_sms_order_canceled')->checkbox(['checked' => ($model->note_sms_order_canceled === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_sms_rate_service')->checkbox(['checked' => ($model->note_sms_rate_service === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>

                <?= $form->field($model, 'note_sms_order_reminder')->checkbox(['checked' => ($model->note_sms_order_reminder === 'Y'), 'data-on-text' => Yii::t('app', 'Yes'), 'data-off-text' => Yii::t('app', 'No')], true); ?>
            </div>
        </div>
    </div>

    <br/>
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Save'), [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => Yii::t('app', "Are you sure you want to update your notifications?"),
                        'method' => 'post',
                    ]
                ]) ?>
                <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-warning hidden-xs']) ?>
                <?= Html::a(Yii::t('app', 'Cancel'), '/user-notifications', ['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs("

    $(document).ready(function(){
        $('.switchers :checkbox').bootstrapSwitch({
            size: 'mini',
            onColor: 'success',
            offColor: 'danger',
             onSwitchChange: function(e, state){
                $(this).val('Y');
                $(\"input[name='\" + e.currentTarget.name + \"'][type='hidden']\").val('N');
             },
             onInit: function(e, state){
                $(this).val('Y');
                $(\"input[name='\" + e.currentTarget.name + \"'][type='hidden']\").val('N')
             }
        });
    })
");
