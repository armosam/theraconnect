<?php

/** @var View $this */

use yii\helpers\Url;
use yii\web\View;
use common\models\User;

?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <?php if(!Yii::$app->user->isGuest): ?>
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= Yii::$app->urlManagerToFront->createUrl(['site/avatar', 'id' => Yii::$app->user->id]) ?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= User::currentLoggedUser()->userFullName ?></p>

                <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <?php endif; ?>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                    <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => [
                    ['label' => 'Main Menu', 'options' => ['class' => 'header']],
                    ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],
                    ['label' => 'New Applicants', 'icon' => 'list', 'url' => ['prospect/index'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Therapists', 'icon' => 'user', 'url' => ['provider/index'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Agencies', 'icon' => 'user', 'url' => ['customer/index'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Patients', 'icon' => 'home', 'url' => ['patient/index'], 'visible' => !Yii::$app->user->isGuest],
                    [
                        'label' => 'Notes',
                        'icon' => 'comments-o',
                        'url' => '#',
                        'items' => [
                            ['label' => 'Physician Orders', 'icon' => 'comments-o', 'url' => ['note-supplemental/index'], 'active' => (Yii::$app->controller->id === 'note-supplemental'), 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Eval Notes', 'icon' => 'comments-o', 'url' => ['note-eval/index'], 'active' => (Yii::$app->controller->id === 'note-eval'), 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Progress Notes', 'icon' => 'comments-o', 'url' => ['note-progress/index'], 'active' => (Yii::$app->controller->id === 'note-progress'), 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Route Sheets', 'icon' => 'comments-o', 'url' => ['note-route-sheet/index'], 'active' => (Yii::$app->controller->id === 'note-route-sheet'), 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Discharge Orders', 'icon' => 'comments-o', 'url' => ['note-discharge-order/index'], 'active' => (Yii::$app->controller->id === 'note-discharge-order'), 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Discharge Summary', 'icon' => 'comments-o', 'url' => ['note-discharge-summary/index'], 'active' => (Yii::$app->controller->id === 'note-discharge-summary'), 'visible' => !Yii::$app->user->isGuest],
                            ['label' => 'Communication Notes', 'icon' => 'comments-o', 'url' => ['note-communication/index'], 'active' => (Yii::$app->controller->id === 'note-communication'), 'visible' => !Yii::$app->user->isGuest],
                    ]],
                    ['label' => 'All Users', 'icon' => 'user', 'url' => ['user/index'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Credential Types', 'icon' => 'th', 'url' => ['credential-type/index'], 'visible' => !Yii::$app->user->isGuest],
                    ['label' => 'Services', 'icon' => 'th', 'url' => ['service/index'], 'visible' => !Yii::$app->user->isGuest],
                    [
                        'label' => 'Tools',
                        'icon' => 'cog',
                        'url' => '#',
                        'visible' => Yii::$app->user->identity->isSuperAdmin,
                        'items' => [
                            ['label' => 'Log', 'icon' => 'info-circle', 'url' => ['log/index'], 'visible' => Yii::$app->user->identity->isSuperAdmin],
                            ['label' => 'Log Archive', 'icon' => 'floppy-o', 'url' => ['log-archive/index'], 'visible' => Yii::$app->user->identity->isSuperAdmin],
                            ['label' => 'Gii', 'icon' => 'wrench', 'url' => ['/gii'], 'visible' => Yii::$app->user->identity->isSuperAdmin],
                            ['label' => 'Debug', 'icon' => 'bug', 'url' => ['/debug'], 'visible' => Yii::$app->user->identity->isSuperAdmin],
                            [
                                'label' => 'Level One',
                                'icon' => 'circle-o',
                                'url' => '#',
                                'visible' => false,
                                'items' => [
                                    ['label' => 'Level Two', 'icon' => 'circle-o', 'url' => '#',],
                                    [
                                        'label' => 'Level Two',
                                        'icon' => 'circle-o',
                                        'url' => '#',
                                        'items' => [
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                            ['label' => 'Level Three', 'icon' => 'circle-o', 'url' => '#',],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>
