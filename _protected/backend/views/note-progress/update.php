<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NoteProgress */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Progresses Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-progress-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
