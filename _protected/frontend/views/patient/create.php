<?php

/* @var $this yii\web\View */
/* @var $model common\models\Patient */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="patient-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
