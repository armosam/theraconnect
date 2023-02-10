<?php

/* @var yii\web\View $this */
/* @var common\models\User $model */
/* @var common\rbac\models\Role $role */
/* @var common\models\UserAvatar $userAvatar */
/* @var common\models\UserService $userService */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Therapists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="provider-create">

    <?= $this->render('_form', [
        'model' => $model,
        'role' => $role,
        'userService' => $userService,
        'userAvatar' => $userAvatar
    ]) ?>

</div>

