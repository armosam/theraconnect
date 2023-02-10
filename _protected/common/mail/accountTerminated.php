<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>

<?=Yii::t('app', 'Dear {name}', ['name' => Html::encode($user->getUserFullName())]) ?>,<br><br>

<?=Yii::t('app', 'You have terminated your account permanently.') ?>.<br><br>
