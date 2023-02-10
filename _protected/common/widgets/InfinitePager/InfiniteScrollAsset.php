<?php

namespace common\widgets\InfinitePager;

use yii\web\AssetBundle;

/**
 * Class InfiniteScrollAsset
 * @package common\widgets\InfinitePager
 */
class InfiniteScrollAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/assets';

    public $js = [
        'jquery.infinitescroll.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}