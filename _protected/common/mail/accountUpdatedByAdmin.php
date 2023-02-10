<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('profile/index')]);
?>

<?= Yii::t('app', 'Dear {name}', ['name' => Html::encode($user->getUserFullName())]) ?>,<br><br>

<?=Yii::t('app', 'Your account has been updated by administration.') ?><br><br>
