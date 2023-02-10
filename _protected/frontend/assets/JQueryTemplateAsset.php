<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * -----------------------------------------------------------------------------
 * @author Armen Bablanyan <thera@gmail.com>
 * @link https://github.com/innwin/jquery-tmpl
 * @since 1.0
 * -----------------------------------------------------------------------------
 */
class JQueryTemplateAsset extends AssetBundle
{
    public $sourcePath = '@webroot/theme/js/jquery-template';

    public $js = [
        YII_DEBUG ? 'jquery.tmpl.js' : 'jquery.tmpl.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}

