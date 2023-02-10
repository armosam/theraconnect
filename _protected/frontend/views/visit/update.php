<?php

/* @var $this yii\web\View */
/* @var $model common\models\Visit */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['provider-order/index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="visit-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
