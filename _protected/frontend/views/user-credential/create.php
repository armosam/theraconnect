<?php

/* @var $this yii\web\View */
/* @var $model common\models\UserCredential */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Credentials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-credential-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
