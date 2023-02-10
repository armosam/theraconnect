<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\NoteCommunication */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Communication Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="note-communication-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
