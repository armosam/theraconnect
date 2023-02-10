<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$reset_link = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'User')]) ?>,<br><br>

<?=Yii::t('app', 'You requested to change your account password.') ?><br><br>

<?=Yii::t('app', 'Follow this link to reset your password') ?>: <?= Html::a(Yii::t('app', 'Please, click here to set your new password.'), $reset_link) ?><br><br>
