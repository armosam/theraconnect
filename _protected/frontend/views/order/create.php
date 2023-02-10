<?php

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="order-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
