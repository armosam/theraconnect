<?php

use yii\helpers\Html;
use yii\web\View;
use frontend\assets\HomePageAsset;
use frontend\widgets\Alert;

/* @var $this View */
/* @var $content string */

HomePageAsset::register($this);
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

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?= $this->theme->getUrl('img/apple-icon-57x57.png') ?>">
    <link rel="apple-touch-icon" sizes="60x60" href="<?= $this->theme->getUrl('img/apple-icon-60x60.png') ?>">
    <link rel="apple-touch-icon" sizes="72x72" href="<?= $this->theme->getUrl('img/apple-icon-72x72.png') ?>">
    <link rel="apple-touch-icon" sizes="76x76" href="<?= $this->theme->getUrl('img/apple-icon-76x76.png') ?>">
    <link rel="apple-touch-icon" sizes="114x114" href="<?= $this->theme->getUrl('img/apple-icon-114x114.png') ?>">
    <link rel="apple-touch-icon" sizes="120x120" href="<?= $this->theme->getUrl('img/apple-icon-120x120.png') ?>">
    <link rel="apple-touch-icon" sizes="144x144" href="<?= $this->theme->getUrl('img/apple-icon-144x144.png') ?>">
    <link rel="apple-touch-icon" sizes="152x152" href="<?= $this->theme->getUrl('img/apple-icon-152x152.png') ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $this->theme->getUrl('img/apple-icon-180x180.png') ?>">
    <link rel="icon" type="image/png" sizes="36x36" href="<?= $this->theme->getUrl('img/android-icon-36x36.png') ?>">
    <link rel="icon" type="image/png" sizes="48x48" href="<?= $this->theme->getUrl('img/android-icon-48x48.png') ?>">
    <link rel="icon" type="image/png" sizes="72x72" href="<?= $this->theme->getUrl('img/android-icon-72x72.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $this->theme->getUrl('img/android-icon-96x96.png') ?>">
    <link rel="icon" type="image/png" sizes="144x144" href="<?= $this->theme->getUrl('img/android-icon-144x144.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= $this->theme->getUrl('img/android-icon-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $this->theme->getUrl('img/favicon-16x16.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $this->theme->getUrl('img/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="96x96" href="<?= $this->theme->getUrl('img/favicon-96x96.png') ?>">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-70x70.png') ?>">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-144x144.png') ?>">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-150x150.png') ?>">
    <meta name="msapplication-TileImage" content="<?= $this->theme->getUrl('img/ms-icon-310x310.png') ?>">
    <meta name="theme-color" content="#ffffff">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Raleway:300,300i,400,400i,500,500i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <?= Html::csrfMetaTags() ?>

    <title><?= Html::encode($this->title) ?> - <?= Html::encode(Yii::$app->name)?></title>

    <!-- CSS Files -->
    <?php $this->head() ?>

</head>

<body>

<?= $this->render('header', []) ?>

<?= $this->render('hero', []) ?>

<main id="main">
    <?= Alert::widget() ?>
    <?= $content ?>
</main><!-- End #main -->

<?= $this->render('footer', []) ?>

<a href="#" class="back-to-top"><i class="bx bxs-up-arrow-alt"></i></a>

<!-- JS Files -->
<?php $this->endBody() ?>

</body>

</html>

<?php $this->endPage() ?>