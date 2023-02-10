<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$activation_link = Yii::$app->urlManagerToFront->createAbsoluteUrl(['site/activate-account', 'token' => $user->account_activation_token]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'User')]) ?>,<br><br>

<?=Yii::t('app', 'Follow this link to activate your account') ?>: <?= Html::a(Yii::t('app', 'Please, click here to activate your account.'), $activation_link) ?><br><br>

<?= Yii::t('app', 'Note: If you received more than one activation email messages then please use the latest one to activate your account.')?><br><br>