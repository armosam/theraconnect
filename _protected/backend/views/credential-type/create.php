<?php

/* @var $this yii\web\View */
/* @var $model common\models\base\CredentialType */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Credential Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="credential-type-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
