<?php

use yii\helpers\Html;
use common\models\ChangeHistory;

/* @var yii\web\View $this */
/* @var ChangeHistory $ChangeHistory */

$activationLink = Yii::$app->urlManagerToFront->createAbsoluteUrl(['site/verify-email-address', 'token' => $ChangeHistory->verification_code]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'User')]) ?>,<br><br>

<?=Yii::t('app', 'Please verify your email address by clicking {link}.', ['link' => Html::a(Yii::t('app', 'this link here'), $activationLink)]) ?><br><br>

<?= Yii::t('app', 'Note: If you received more than one email messages for verification then please use the latest one to verify your email address.')?><br><br>