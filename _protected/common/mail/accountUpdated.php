<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('profile/index')]);
?>

<?=Yii::t('app', 'Dear {name}', ['name' => Html::encode($user->getUserFullName())]) ?>,<br><br>

<?=Yii::t('app', 'Your account has been updated.') ?>.<br><br>

<?=Yii::t('app', 'Please disregard this email if you made changes in your account; otherwise, please login to your account and check your details. {link}', ['link' => Html::a(Yii::t('app', 'Go to my account'), $linkToAccount)]) ?><br><br>
