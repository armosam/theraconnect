<?php

namespace backend\assets;

use yii\web\AssetBundle;

class AdminLtePluginAsset extends AssetBundle
{
    public $sourcePath = '@vendor/almasaeed2010/adminlte';

    public $js = [
        YII_DEBUG ? 'bower_components/datatables.net-bs/js/dataTables.bootstrap.js' : 'bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js',
        // more plugin Js here
    ];
    public $css = [
        YII_DEBUG ? 'bower_components/datatables.net-bs/css/dataTables.bootstrap.css' : 'bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
        // more plugin CSS here
    ];
    public $depends = [
        'dmstr\web\AdminLteAsset',
    ];
}