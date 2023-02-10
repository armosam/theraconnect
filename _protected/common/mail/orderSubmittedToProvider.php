<?php

use yii\helpers\Html;
use common\models\Order;

/* @var $this yii\web\View */
/* @var $model Order */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('order/index')]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> $model->provider->getUserFullName()]) ?>,<br><br>

<?= Yii::t('app', 'My name is {name}.', ['name' => $model->customer->getUserFullName()]) ?><br><br>

<?= Yii::t('app', 'I found your details from THERA Connect web site and I contact you for your services.') ?><br><br>

<?= Yii::t('app', 'Please review my data and confirm your availability for my due dates.') ?><br><br>

<?= Yii::t('app', 'Please {go_to_your_account} and find details of submitted order.', ['go_to_your_account' => Html::a(Yii::t('app', 'Go to your account'), $linkToAccount)]) ?><br><br>

