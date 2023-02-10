<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use frontend\widgets\Alert;
use common\models\User;

/* @var $this View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?php if (YII_ENV === 'prod'): ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->

        <!-- Yandex.Metrika counter -->

        <!-- /Yandex.Metrika counter -->
    <?php endif; ?>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="msvalidate.01" content="8F9E8E356F97692258F17218EDD0F84D" />
    <link rel="apple-touch-icon" sizes="57x57" href="<?= $this->theme->getUrl('img/apple-icon-57x57.png') ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= $this->theme->getUrl('img/apple-icon-60x60.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= $this->theme->getUrl('img/apple-icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= $this->theme->getUrl('img/apple-icon-76x76.png') ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= $this->theme->getUrl('img/apple-icon-114x114.png') ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= $this->theme->getUrl('img/apple-icon-120x120.png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= $this->theme->getUrl('img/apple-icon-144x144.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= $this->theme->getUrl('img/apple-icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $this->theme->getUrl('img/apple-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" sizes="36x36"  href="<?= $this->theme->getUrl('img/android-icon-36x36.png') ?>">
    <link rel="icon" type="image/png" sizes="48x48"  href="<?= $this->theme->getUrl('img/android-icon-48x48.png') ?>">
    <link rel="icon" type="image/png" sizes="72x72"  href="<?= $this->theme->getUrl('img/android-icon-72x72.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96"  href="<?= $this->theme->getUrl('img/android-icon-96x96.png') ?>">
    <link rel="icon" type="image/png" sizes="144x144"  href="<?= $this->theme->getUrl('img/android-icon-144x144.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192"  href="<?= $this->theme->getUrl('img/android-icon-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $this->theme->getUrl('img/favicon-16x16.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $this->theme->getUrl('img/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $this->theme->getUrl('img/favicon-96x96.png') ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-70x70.png') ?>">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-144x144.png') ?>">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-150x150.png') ?>">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-310x310.png') ?>">
    <meta name="theme-color" content="#ffffff">

    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?> - <?= Html::encode(Yii::$app->name)?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div class="wrap">
    <?php
        NavBar::begin([
            'brandLabel' => Html::img( $this->theme->getUrl('img/small_logo.png'), ['alt' => 'Logo', 'width' => 50]) . Html::tag('span', Yii::t('app', Yii::$app->name)),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-default navbar-fixed-top',
            ],
        ]);

        //$menuItems[] = ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']];

        if(Yii::$app->user->isGuest) {
            $menuItems[] = ['label' => Yii::t('app', 'About Us'), 'url' => ['site/about']];
            $menuItems[] = ['label' => Yii::t('app', 'Contact Us'), 'url' => ['site/contact']];
            $menuItems[] = ['label' => Yii::t('app', 'Login'), 'url' => ['site/login']];
        } else {
            // display Article admin page to editor+ roles
            if (Yii::$app->user->identity->isEditor) {
                $settingItems[] = ['label' => Yii::t('app', 'Article Admin'), 'url' => ['article/admin']];
            }

            // we do not need to display Article/index, About and Contact pages to editor+ roles
            if (Yii::$app->user->identity->isProvider) {
                $menuItems[] = ['label' => Yii::t('app', 'Find Patient'), 'url' => ['search/index']];
                $menuItems[] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['provider-order/index']];
                $menuItems[] = ['label' => Yii::t('app', 'My Calendar'), 'url' => ['provider-calendar/index']];
                $settingItems[] = ['label' => Yii::t('app', 'My Credentials'), 'url' => ['user-credential/index']];
                $menuItems[] = ['label' => Yii::t('app', 'My Credentials'), 'url' => ['user-credential/index'], 'options' => ['class' => 'visible-xs']];
            }

            if (Yii::$app->user->identity->isCustomer) {
                $menuItems[] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['patient/index']];
            }

            $settingItems[] = ['label' => Yii::t('app', 'My Profile'), 'url' => ['profile/index']];
            //$settingItems[] = ['label' => Yii::t('app', 'My Notifications'), 'url' => ['user-notification/index']];
            $settingItems[] = ['label' => Yii::t('app', 'Logout'). ' (' . User::currentLoggedUser()->username . ')',
                'url' => ['site/logout'], 'linkOptions' => ['data-method' => 'post', 'type' => 'submit', 'id' => 'logout_desktop']
            ];

            $menuItems[] = ['label' => Yii::t('app', 'My Profile'), 'url' => ['profile/index'], 'options' => ['class' => 'visible-xs']];
            $menuItems[] = ['label' => Yii::t('app', 'My Notifications'), 'url' => ['user-notification/index'], 'options' => ['class' => 'visible-xs']];
            $menuItems[] = ['label' => Yii::t('app', 'Logout'). ' (' . User::currentLoggedUser()->username . ')',
                'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post'], 'options' => ['class' => 'visible-xs', 'id' => 'logout_mobile']
            ];
            $menuItems[] = ['label' => Html::tag('span', '', ['class' => 'glyphicon glyphicon-cog', 'aria-hidden' => 'true']), 'items' => $settingItems, 'options' => ['class' => 'hidden-xs', 'id' => 'setting_dropdown'],  'dropDownOptions' => ['class' => 'hidden-xs']];
        } ?>

        <?= Nav::widget([
            'options' => [
                'id' => 'header-nav',
                'class' => 'navbar-nav navbar-right'
            ],
            'encodeLabels' => false,
            'items' => $menuItems,
        ]) ?>

        <?php if (!Yii::$app->user->isGuest): ?>
            <span class="navbar-text"> Welcome: <?= User::currentLoggedUser()->getUserFullName() ?></span>
        <?php endif; ?>

        <?php NavBar::end(); ?>

        <div class="container">
        <?= Breadcrumbs::widget([
            'links' => $this->params['breadcrumbs'] ?? [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="social-icons text-center">
                    <a href="https://www.facebook.com/Connect/" title="Facebook Connect" target="_blank">
                        <i class="fa fa-facebook-square fa-2x"></i>
                    </a>
                    <a href="https://twitter.com/Connect" title="Twitter Connect" target="_blank">
                        <i class="fa fa-twitter-square fa-2x"></i>
                    </a>
                    <a href="https://www.instagram.com/Connect/" title="Instagram Connect" target="_blank">
                        <i class="fa fa-instagram fa-2x"></i>
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="footer-nav hidden-xs text-center">
                    <?= Html::a(Yii::t('app', 'How it works'), Url::to(['site/how-it-work']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                    <?= Html::a(Yii::t('app', 'Home'), Url::to(['site/index']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                    <?= Html::a(Yii::t('app', 'News'), Url::to(['article/index']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                    <?= Html::a(Yii::t('app', 'Services'), Url::to(['site/service']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                    <?= Html::a(Yii::t('app', 'About Us'), Url::to(['site/about']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                    <?= Html::a(Yii::t('app', 'Contact Us'), Url::to(['site/contact']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                    <?= Html::a(Yii::t('app', 'Terms of Service'), Url::to(['site/terms-of-service']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                    <?= Html::a(Yii::t('app', 'Privacy Policy'), Url::to(['site/privacy-policy']), ['class' => 'text-center']) ?>&nbsp;&nbsp;
                </div>
                <div class="footer-nav text-left list-group visible-xs">
                    <?= Html::a(Yii::t('app', 'How it works'), Url::to(['site/how-it-work']), ['class' => 'list-group-link col-xs-6']) ?>
                    <?= Html::a(Yii::t('app', 'Home'), Url::to(['site/index']), ['class' => 'list-group-link col-xs-6']) ?>
                    <?= Html::a(Yii::t('app', 'News'), Url::to(['article/index']), ['class' => 'list-group-link col-xs-6']) ?>
                    <?= Html::a(Yii::t('app', 'Services'), Url::to(['site/service']), ['class' => 'list-group-link col-xs-6']) ?>
                    <?= Html::a(Yii::t('app', 'About Us'), Url::to(['site/about']), ['class' => 'list-group-link col-xs-6']) ?>
                    <?= Html::a(Yii::t('app', 'Contact Us'), Url::to(['site/contact']), ['class' => 'list-group-link col-xs-6']) ?>
                    <?= Html::a(Yii::t('app', 'Terms of Service'), Url::to(['site/terms-of-service']), ['class' => 'list-group-link col-xs-6']) ?>
                    <?= Html::a(Yii::t('app', 'Privacy Policy'), Url::to(['site/privacy-policy']), ['class' => 'list-group-link col-xs-6']) ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-xl-12 text-center small">
                    <?= Yii::t('app', '&copy; {date} {link}. All Rights Reserved.', ['date'=>date('Y'), 'link'=>'<a href="https://www.Connect.com" rel="external">' . Yii::t('app', 'THERA Connect') . '</a>']) ?>
                </div>
            </div>
            <br>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
