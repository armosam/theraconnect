<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NoteEval */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Eval Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-eval-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
