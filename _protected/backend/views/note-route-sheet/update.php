<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NoteRouteSheet */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Route Sheets'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-route-sheet-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
