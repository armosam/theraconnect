<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('profile/index')]);
?>

<?= Yii::t('app', 'Dear {name}', ['name' => Html::encode($user->getUserFullName())]) ?>,<br><br>

<?=Yii::t('app', 'Your account has been activated by administration. Please login and fill your profile. {link}', ['link' => Html::a(Yii::t('app', 'Go to my account'), $linkToAccount)])?><br><br>
