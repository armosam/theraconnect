<?php

use yii\helpers\Html;
use common\models\base\User;

/* @var $this yii\web\View */
/* @var $user common\models\User */

if ($user->status === User::USER_STATUS_NOT_ACTIVATED) {
    $resetLink = Yii::$app->urlManagerToFront->createAbsoluteUrl(['site/activate-account-and-set-new-password', 'account_activation_token' => $user->account_activation_token, 'password_reset_token' => $user->password_reset_token]);
} else {
    $resetLink = Yii::$app->urlManagerToFront->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
} ?>

<?= Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'User')]) ?>,<br><br>

<?=Yii::t('app', 'Your account has been created by administration.') ?><br><br>

<?= Yii::t('app', 'Congratulation and thank you for joining our community.') ?><br><br>

<?= Html::a(Yii::t('app', 'Please, click here to set your password'), $resetLink) ?><br><br>
