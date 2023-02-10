<?php

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $userService common\models\UserService */
/* @var $role common\rbac\models\Role */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->username, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="user-update">
    <?= $this->render('_form', [
        'model' => $model,
        'role' => $role,
        'userService' => $userService
    ]) ?>
</div>
