<?php

namespace common\widgets\signature;

use yii\web\View;
use yii\helpers\Url;
use yii\web\AssetBundle;

/**
 *
 */
class SignatureAsset extends AssetBundle
{
    public $sourcePath = __DIR__ .'/assets';

    public $css = [
        'css/site.css',
    ];

    public $js = [
        'src/jSignature.js',
        'src/plugins/jSignature.CompressorBase30.js',
        'src/plugins/jSignature.CompressorSVG.js',
        'src/plugins/jSignature.UndoButton.js',
        'src/plugins/signhere/jSignature.SignHere.js',
       // ['libs/modernizr.js', 'position' => View::POS_BEGIN],
        ['libs/flashcanvas.js', 'position' => View::POS_HEAD, 'condition' => 'if lt IE 9'],
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}