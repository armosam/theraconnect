<?php

/** @var View $this */
/** @var \common\models\User $model */

use yii\helpers\Html;
use yii\web\View;

?>

<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <img src="<?= Yii::$app->urlManagerToFront->createUrl(['site/avatar', 'id' => $model->id, 'w' => 160]) ?>" class="user-image" alt="<?= $model->getUserFullName() ?>" />
    <span class="hidden-xs"><?= $model->getUserFullName() ?></span>
</a>
<ul class="dropdown-menu">
    <!-- User image -->
    <li class="user-header">
        <img src="<?= Yii::$app->urlManagerToFront->createUrl(['site/avatar', 'id' => $model->id, 'w' => 160]) ?>" class="img-circle" alt="<?= $model->getUserFullName() ?>" />
        <p>
            <?= $model->getUserFullName() ?>
            <small><?= Yii::t('app', 'Member since {date_started}', ['date_started' => Yii::$app->formatter->asDate($model->created_at, 'medium')]) ?></small>
        </p>
    </li>
    <!-- Menu Body -->
    <li class="user-body">
        <div class="col-xs-3 text-center">
            <?php if($model->role->item_name == \common\models\User::USER_PROVIDER): ?>
            <?= Html::a('', ['user-service/view', 'id' => $model->id], ['title'=>Yii::t('app', 'Manage Services'), 'class' => 'btn btn-default glyphicon glyphicon-flash']) ?>
            <?php endif; ?>
        </div>
        <div class="col-xs-3 text-center">
            <?php if($model->role->item_name == \common\models\User::USER_PROVIDER): ?>
                <?= Html::a('', ['user-qualification/view', 'id' => $model->id], ['title'=>Yii::t('app', 'Manage Qualifications'), 'class' => 'btn btn-default glyphicon glyphicon-book']) ?>
            <?php endif; ?>
        </div>
        <div class="col-xs-3 text-center">
            <?php if($model->role->item_name == \common\models\User::USER_PROVIDER): ?>
                <?= Html::a('', ['user-gallery/view', 'id' => $model->id], ['title'=>Yii::t('app', 'Manage Gallery'), 'class' => 'btn btn-default glyphicon glyphicon-picture']) ?>
            <?php endif; ?>
        </div>
        <div class="col-xs-3 text-center">
            <?= Html::a('', ['user-notification/view', 'id' => $model->id], ['title'=>Yii::t('app', 'Manage Notifications'), 'class' => 'btn btn-default glyphicon glyphicon-envelope']) ?>
        </div>
    </li>
    <!-- Menu Footer-->
    <li class="user-footer">
        <div class="pull-left">
            <?= Html::a(
                Yii::t('app', 'Profile'),
                ['user/view', 'id' => $model->id],
                ['class' => 'btn btn-default btn-flat']
            ) ?>
        </div>
        <div class="pull-right">
            <?= Html::a(
                Yii::t('app', 'Sign Out'),
                ['/site/logout'],
                ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
            ) ?>
        </div>
    </li>
</ul>
