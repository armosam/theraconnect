<?php

/* @var $this yii\web\View */
/* @var $model common\models\Patient */
/* @var $uid int */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="patient-create">

    <?= $this->render('_form', [
        'model' => $model,
        'uid' => $uid
    ]) ?>

</div>
