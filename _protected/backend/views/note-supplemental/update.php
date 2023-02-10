<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NoteSupplemental */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Physician Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-supplemental-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
