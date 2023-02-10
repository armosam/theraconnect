<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NoteProgress */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Progress Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-progress-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
