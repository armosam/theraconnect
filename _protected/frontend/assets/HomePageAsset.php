<?php
/**
 * -----------------------------------------------------------------------------
 * Home Page Assets for single page template
 * -----------------------------------------------------------------------------
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * -----------------------------------------------------------------------------
 * @author Armen BAblanyan <thera@gmail.com>
 * -----------------------------------------------------------------------------
 */
class HomePageAsset extends AssetBundle
{
    public $basePath = '@webroot/theme';
    public $baseUrl = '@theme';

    public $css = [
        YII_DEBUG ? 'tools/boxicons/css/boxicons.css' : 'tools/boxicons/css/boxicons.min.css',
        YII_DEBUG ? 'tools/owl.carousel/assets/owl.carousel.css' : 'tools/owl.carousel/assets/owl.carousel.min.css',
        'tools/aos/aos.css',
        'css/home.css',
    ];
    public $js = [
        'tools/jquery.easing/jquery.easing.min.js',
        'tools/waypoints/jquery.waypoints.min.js',
        'tools/counterup/counterup.min.js',
        YII_DEBUG ? 'tools/owl.carousel/owl.carousel.js' : 'tools/owl.carousel/owl.carousel.min.js',
        'tools/aos/aos.js',
        'js/home.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap4\BootstrapAsset',
        'yii\bootstrap4\BootstrapPluginAsset',
    ];
}

