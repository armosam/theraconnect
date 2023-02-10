<?php

use yii\bootstrap4\Nav;
use yii\helpers\Html;
use common\models\User;
use lajax\languagepicker\widgets\LanguagePicker;

//$menuItems[] = ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']];

if (Yii::$app->user->isGuest) {
    $menuItems[] = ['label' => Yii::t('app', 'About Us'), 'url' => ['site/about']];
    $menuItems[] = ['label' => Yii::t('app', 'Contact Us'), 'url' => ['site/contact']];
    $menuItems[] = ['label' => Yii::t('app', 'Login'), 'url' => ['site/login']];
} else {
    // display Article admin page to editor+ roles
    if (Yii::$app->user->identity->isEditor){
        $settingItems[] = ['label' => Yii::t('app', 'Manage Articles'), 'url' => ['article/admin']];
    }

    if (Yii::$app->user->identity->isProvider){
        $menuItems[] = ['label' => Yii::t('app', 'Find Patient'), 'url' => ['search/index']];
        $menuItems[] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['provider-order/index']];
        $menuItems[] = ['label' => Yii::t('app', 'My Calendar'), 'url' => ['provider-calendar/index']];
        $settingItems[] = ['label' => Yii::t('app', 'My Credentials'), 'url' => ['user-credential/index']];
        $menuItems[] = ['label' => Yii::t('app', 'My Credentials'), 'url' => ['user-credential/index'], 'options' => ['class' => 'd-block d-sm-none']];
    }

    if (Yii::$app->user->identity->isCustomer){
        $menuItems[] = ['label' => Yii::t('app', 'My Patients'), 'url' => ['patient/index']];
    }

    $settingItems[] = ['label' => Yii::t('app', 'My Profile'), 'url' => ['profile/index']];
    //$settingItems[] = ['label' => Yii::t('app', 'My Notifications'), 'url' => ['user-notification/index']];
    $settingItems[] = ['label' => Yii::t('app', 'Logout'). ' (' . User::currentLoggedUser()->username . ')', 'url' => ['site/logout'], 'linkOptions' => ['data-method' => 'post', 'type' => 'submit', 'id' => 'logout_desktop']];

    $menuItems[] = ['label' => Yii::t('app', 'My Profile'), 'url' => ['profile/index'], 'options' => ['class' => 'd-block d-sm-none']];
    $menuItems[] = ['label' => Yii::t('app', 'My Notifications'), 'url' => ['user-notification/index'], 'options' => ['class' => 'd-block d-sm-none']];
    $menuItems[] = ['label' => Yii::t('app', 'Logout'). ' (' . User::currentLoggedUser()->username . ')', 'url' => ['site/logout'], 'linkOptions' => ['data-method' => 'post'], 'options' => ['class' => 'd-block d-sm-none', 'id' => 'logout_mobile']];

    $menuItems[] = ['label' => Html::tag('span', '', ['class' => 'bx bxs-cog', 'aria-hidden' => 'true']), 'items' => $settingItems, 'options' => ['class' => 'd-none d-sm-block', 'id' => 'setting_dropdown'],  'dropDownOptions' => ['class' => 'd-none d-sm-block']];
}
?>

<!-- ======= Header ======= -->
<header id="header" class="fixed-top">
    <div class="container d-flex">

        <div class="logo mr-auto">
            <h1 class="text-light">
                <?= Html::beginTag('a', ['href' => Yii::$app->homeUrl]) ?>
                <?= Html::img( $this->theme->getUrl('img/small_logo.png'), ['alt' => 'Logo', 'class' => 'img-fluid', 'width' => 50]) ?>
                <?= Html::beginTag('span', ['class'=> 'd-none d-sm-inline']) . Yii::t('app', Yii::$app->name) . Html::endTag('span') ?>
                <?= Html::endTag('a') ?>
            </h1>
        </div>

        <nav class="nav-menu d-none d-lg-block">
            <?= Nav::widget([
            'options' => [
                'id' => 'header-nav',
                'class' => 'nav-menu d-none d-lg-block'
            ],
            'encodeLabels' => false,
            'items' => $menuItems,
            ])?>
        </nav><!-- .nav-menu -->

    </div>
</header><!-- End Header -->