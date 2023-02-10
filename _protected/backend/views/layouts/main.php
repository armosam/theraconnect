<?php

use dmstr\helpers\AdminLteHelper;
use backend\assets\AdminLtePluginAsset;
use yii\helpers\Html;
use yii\web\View;

/** @var View $this */
/** @var $content string */

dmstr\web\AdminLteAsset::register($this);
backend\assets\AppAsset::register($this);
//AdminLtePluginAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
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
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition <?= AdminLteHelper::skinClass() ?> sidebar-mini fixed">
<?php $this->beginBody() ?>
<div class="wrapper">

    <?= $this->render(
        'header',
        []
    ) ?>

    <?= $this->render(
        'left',
        []
    ) ?>

    <?= $this->render(
        'content',
        ['content' => $content]
    ) ?>

    <?= $this->render(
        'right',
        []
    ) ?>

</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
