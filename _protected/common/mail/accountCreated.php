<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('profile/index')]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'User')]) ?>,<br><br>

<?=Yii::t('app', 'Your account has been created.') ?><br><br>

<?= Yii::t('app', 'Congratulation and thank you for joining our community.') ?><br><br>

<?=Yii::t('app', 'Please disregard this email if you did not create account, otherwise please activate and login to your account. {link}', ['link' => Html::a(Yii::t('app', 'Go to my account'), $linkToAccount)]) ?><br><br>