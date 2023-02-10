<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('profile/index')]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'User')]) ?>,<br><br>

<?=Yii::t('app', 'Your account has been activated successfully. Please login and fill your profile.') ?><br><br>

<?=Yii::t('app', 'You can use your email address or username to login your account.') ?> <?= Html::a(Yii::t('app', 'Go to my account'), $linkToAccount)?><br><br>
