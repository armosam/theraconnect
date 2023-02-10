<?php

namespace common\widgets\ajax\modal;

use yii\web\AssetBundle;

/**
 * Class ModalAjaxAsset
 */
class ModalAjaxAsset extends AssetBundle
{
    public $css = [
        'css/style.css'
    ];

    public $js = [
        'js/script.js'
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