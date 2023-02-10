<?php

namespace common\widgets\stars;

use yii\web\AssetBundle;

class StarRatingAsset extends AssetBundle
{
    public $js = [];

    public $css = [
        YII_DEBUG ? 'css/star-rating.css' : 'css/star-rating.min.css'
    ];

    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        // Tell AssetBundle where the assets files are
        $this->sourcePath = __DIR__ . "/assets";
        parent::init();
    }
}