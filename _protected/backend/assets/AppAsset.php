<?php
/**
 * -----------------------------------------------------------------------------
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 * -----------------------------------------------------------------------------
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 *
 * @since 2.0
 *
 * Customized by Armen Bablanyan
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/theme';
    public $baseUrl = '@theme';

    public $css = [
        YII_DEBUG ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
        'css/site.css',
    ];
    public $js = [
    ];
    
    public $depends = [
        'kartik\switchinput\SwitchInputAsset',
        'yii\web\YiiAsset',
    ];
}
