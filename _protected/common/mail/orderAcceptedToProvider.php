<?php

use yii\helpers\Html;
use common\models\Order;

/* @var $this yii\web\View */
/* @var $model Order */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('order/index')]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> $model->provider->getUserFullName()]) ?>,<br><br>

<?=Yii::t('app', 'You accepted the service request from {customer}.', ['customer' => $model->customer->getUserFullName()]) ?><br><br>

<?=Yii::t('app', 'Please {go_to_your_account} and find details of accepted order to contact customer directly.', ['go_to_your_account' => Html::a(Yii::t('app', 'Go to your account'), $linkToAccount)]) ?><br><br>

