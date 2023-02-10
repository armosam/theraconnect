<?php

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use frontend\widgets\Alert;
use backend\assets\AppAsset;
use kartik\dialog\Dialog;
use common\models\User;

/**
 * This is simple layout without adminLte sidebars
 * Could be used for login or home page
 */

/* @var $this View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="57x57" href="<?= $this->theme->getUrl('img/apple-icon-57x57.png') ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= $this->theme->getUrl('img/apple-icon-60x60.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= $this->theme->getUrl('img/apple-icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= $this->theme->getUrl('img/apple-icon-76x76.png') ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= $this->theme->getUrl('img/apple-icon-114x114.png') ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= $this->theme->getUrl('img/apple-icon-120x120.png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= $this->theme->getUrl('img/apple-icon-144x144.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= $this->theme->getUrl('img/apple-icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $this->theme->getUrl('img/apple-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?= $this->theme->getUrl('img/android-icon-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $this->theme->getUrl('img/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $this->theme->getUrl('img/favicon-96x96.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $this->theme->getUrl('img/favicon-16x16.png') ?>">
    <link rel="manifest" href="<?= $this->theme->getUrl('img/manifest.json') ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-144x144.png') ?>">
    <meta name="theme-color" content="#ffffff">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?php
            NavBar::begin([
                'brandLabel' => Html::img( $this->theme->getUrl('img/small_logo.png'), ['alt' => 'Logo', 'width' => 50]) . '<span>' . Yii::t('app', Yii::$app->name) . '</span>',
                'brandUrl' => Yii::$app->homeUrl,
                'options' => [
                    'class' => 'navbar-default navbar-fixed-top',
                ],
            ]);

            // display Account and Users to admin+ roles
            if (Yii::$app->user->can('admin'))
            {
                $menuItems[] = ['label' => Yii::t('app', 'Home'), 'url' => ['site/index']];
                $menuItems[] = ['label' => Yii::t('app', 'New Applicants'), 'url' => ['prospect/index'], 'visible' => !Yii::$app->user->isGuest];
                $menuItems[] = ['label' => Yii::t('app', 'Therapists'), 'url' => ['provider/index'], 'visible' => !Yii::$app->user->isGuest];
                $menuItems[] = ['label' => Yii::t('app', 'Agencies'), 'url' => ['customer/index'], 'visible' => !Yii::$app->user->isGuest];
                $menuItems[] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index'], 'visible' => !Yii::$app->user->isGuest];

                $note[] = ['label' => 'Physician Orders', 'icon' => 'comments-o', 'url' => ['note-supplemental/index'], 'active' => (Yii::$app->controller->id === 'note-supplemental'), 'visible' => !Yii::$app->user->isGuest];
                $note[] = ['label' => 'Eval Notes', 'icon' => 'comments-o', 'url' => ['note-eval/index'], 'active' => (Yii::$app->controller->id === 'note-eval'), 'visible' => !Yii::$app->user->isGuest];
                $note[] = ['label' => 'Progress Notes', 'icon' => 'comments-o', 'url' => ['note-progress/index'], 'active' => (Yii::$app->controller->id === 'note-progress'), 'visible' => !Yii::$app->user->isGuest];
                $note[] = ['label' => 'Route Sheets', 'icon' => 'comments-o', 'url' => ['note-route-sheet/index'], 'active' => (Yii::$app->controller->id === 'note-route-sheet'), 'visible' => !Yii::$app->user->isGuest];
                $note[] = ['label' => 'Discharge Orders', 'icon' => 'comments-o', 'url' => ['note-discharge-order/index'], 'active' => (Yii::$app->controller->id === 'note-discharge-order'), 'visible' => !Yii::$app->user->isGuest];
                $note[] = ['label' => 'Discharge Summary', 'icon' => 'comments-o', 'url' => ['note-discharge-summary/index'], 'active' => (Yii::$app->controller->id === 'note-discharge-summary'), 'visible' => !Yii::$app->user->isGuest];
                $note[] = ['label' => 'Communication Notes', 'icon' => 'comments-o', 'url' => ['note-communication/index'], 'active' => (Yii::$app->controller->id === 'note-communication'), 'visible' => !Yii::$app->user->isGuest];
                $menuItems[] = ['label' => Yii::t('app', 'Notes'), 'url' => '', 'items' => $note, 'visible' => !Yii::$app->user->isGuest];

                $settings[] = ['label' => Yii::t('app', 'All Users'), 'url' => ['user/index'], 'visible' => !Yii::$app->user->isGuest];
                $settings[] = ['label' => Yii::t('app', 'Services'), 'url' => ['service/index'], 'visible' => !Yii::$app->user->isGuest];
                $settings[] = ['label' => Yii::t('app', 'Credential Types'), 'url' => ['credential-type/index'], 'visible' => !Yii::$app->user->isGuest];

                $tools[] = ['label' => Yii::t('app', 'Log'), 'url' => ['/log/index'], 'visible' => Yii::$app->user->identity->isSuperAdmin];
                $tools[] = ['label' => Yii::t('app', 'Log Archive'), 'url' => ['/log-archive/index'], 'visible' => Yii::$app->user->identity->isSuperAdmin];
                $settings[] = ['label' => Yii::t('app', 'Tools'), 'url' => '', 'items' => $tools, 'visible' => Yii::$app->user->identity->isSuperAdmin];

                $menuItems[] = ['label' => Yii::t('app', 'Settings'), 'url' => '', 'items' => $settings, 'visible' => !Yii::$app->user->isGuest];
            }
            
            // display Login page to guests of the site
            if (Yii::$app->user->isGuest) 
            {
                $menuItems[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']];
            }
            else // display Logout to all logged in users
            {
                $menuItems[] = [
                    'label' => Yii::t('app', 'Logout'). ' (' . User::currentLoggedUser()->username . ')',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post']
                ];
            }

            echo Nav::widget([
                'options' => ['class' => 'navbar-nav navbar-right'],
                'items' => $menuItems,
            ]);
            
            NavBar::end();
        ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= Dialog::widget(['overrideYiiConfirm' => true]) ?>
        <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 text-center small">
                    <?= Yii::t('app', '&copy; {date} {link}. All Rights Reserved.', ['date'=>date('Y'), 'link'=>'<a href="https://www.Connect.com" rel="external">' . \Yii::t('app', 'THERA Connect') . '</a>']) ?>
                </div>
            </div>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
