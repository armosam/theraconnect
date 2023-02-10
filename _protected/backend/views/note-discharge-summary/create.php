<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NoteDischargeSummary */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discharge Summaries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-discharge-summary-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
