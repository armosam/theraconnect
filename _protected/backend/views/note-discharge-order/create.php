<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\NoteDischargeOrder */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Discharge Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-discharge-order-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>