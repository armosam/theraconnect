<?php

namespace common\widgets\PhotoProfile;

use yii\web\AssetBundle;

class PhotoProfileAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . "/assets";

    public $css = [
    ];

    public $js = [
        YII_DEBUG ? 'js/isotope.pkgd.js' : 'js/isotope.pkgd.min.js',
        'js/script.js'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];
}