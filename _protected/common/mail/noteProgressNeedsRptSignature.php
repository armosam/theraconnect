<?php

use yii\helpers\Html;
use common\models\NoteProgress;

/* @var $this yii\web\View */
/* @var $model NoteProgress */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('provider-order/index')]);
?>

<?= Yii::t('app', 'Dear {rpt}', ['rpt'=> $model->order->orderRPT->getUserFullName()]) ?>,<br><br>

<?=Yii::t('app', 'We need your approval and signature for progress note submitted by PTA {pta}.', ['pta' => $model->provider->getUserFullName()]) ?><br><br>

<?=Yii::t('app', 'Please {go_to_your_account} and sign progress note for patient {patient} by request #{order_number} visited at {visited_at}.', [
    'go_to_your_account' => Html::a(Yii::t('app', 'Go to your account'), $linkToAccount),
    'patient' => $model->order->patient->getPatientFullName(true),
    'order_number' => $model->order->order_number,
    'visited_at' => Yii::$app->formatter->asDatetime($model->visit->visited_at)
]) ?><br><br>

