<?php

use yii\web\View;
use yii\helpers\Html;
use common\models\User;

/** @var View $this */
/** @var string $content */
/** @var string $directoryAsset */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">'.Html::img( $this->theme->getUrl('img/small_logo.png'), ['alt' => 'Logo', 'width' => 42]).'</span><span class="logo-lg" style="text-align: left">' . Html::img( $this->theme->getUrl('img/small_logo.png'), ['alt' => 'Logo', 'width' => 42, 'style' => 'margin:5px']) . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <!-- Messages: style can be found in dropdown.less-->
                <li class="dropdown messages-menu">
                    <?php // echo common\widgets\adminLte\UserRequest::widget() ?>
                </li>

                <!-- Notifications: style can be found in dropdown.less-->
                <li class="dropdown notifications-menu  hidden-xs">
                    <?= $this->render('notifications') ?>
                </li>

                <!-- Tasks: style can be found in dropdown.less -->
                <li class="dropdown tasks-menu hidden">
                    <?php // $this->render('tasks') ?>
                </li>

                <!-- User Account: style can be found in dropdown.less -->
                <li class="dropdown user user-menu">
                    <?= common\widgets\adminLte\UserProfile::widget() ?>
                </li>

                <!-- Logout Item -->
                <?php if(!Yii::$app->user->isGuest): ?>
                <li class="logout">
                    <?= Html::a('<span class="hidden-xs" style="margin-left: 4px">'. Yii::t('app', 'Logout'). ' (' . User::currentLoggedUser()->username . ')' .'</span>',
                        ['/site/logout'], ['data-method' => 'post', 'class' => 'fa fa-power-off btn-flat']) ?>
                </li>
                <?php endif; ?>
                <!-- User Account: style can be found in dropdown.less -->
                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>
