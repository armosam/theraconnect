<?php

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $role common\rbac\models\Role */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-create">

    <?= $this->render('_form', [
        'model' => $model,
        'role' => $role,
    ]) ?>

</div>

