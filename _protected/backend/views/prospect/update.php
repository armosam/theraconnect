<?php

/* @var $this yii\web\View */
/* @var $model common\models\Prospect */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Prospects'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="prospect-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
