<?php

namespace common\widgets\perpage;

use yii\web\AssetBundle;

/**
 * Class PerPageAsset
 */
class PerPageAsset extends AssetBundle
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