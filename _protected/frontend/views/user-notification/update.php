<?php

/** @var $this yii\web\View */
/** @var $model common\models\User */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Notifications'), 'url' => ['/user-notification/index']];
$this->params['breadcrumbs'][] = Yii::t('app', $this->title);

?>

<div class="user-notification֊update">

    <div class="col-lg-12 well">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>

</div>
