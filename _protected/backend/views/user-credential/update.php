<?php

/* @var $this yii\web\View */
/* @var $model common\models\UserCredential */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Therapists'), 'url' => ['provider/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Credentials'), 'url' => ['index', 'uid' => $model->user_id]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-credential-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
