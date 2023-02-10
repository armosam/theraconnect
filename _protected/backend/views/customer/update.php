<?php

/* @var yii\web\View $this */
/* @var common\models\User $model */
/* @var common\rbac\models\Role $role */
/* @var common\models\UserAvatar $userAvatar */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Agencies'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app','View agency {name}', ['name' => $model->agency_name]), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-update">
    <?= $this->render('_form', [
        'model' => $model,
        'role' => $role,
        'userAvatar' => $userAvatar
    ]) ?>
</div>
