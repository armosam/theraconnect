<?php

namespace common\widgets\grid;

use yii\web\AssetBundle;

/**
 * Class GridViewAsset
 */
class GridViewAsset extends AssetBundle
{
    public $css = [
        'css/style.css'
    ];

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';

        parent::init();
    }
}