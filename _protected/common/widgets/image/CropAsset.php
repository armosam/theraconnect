<?php
/**
 * Created by PhpStorm.
 * @user: MackRias
 * @site: http://mackrais.com
 * @email: mackraiscms@gmail.com
 */

namespace common\widgets\image;

use yii\web\AssetBundle;


class CropAsset extends AssetBundle{

    public $sourcePath = '@common/widgets/image/assets';

    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init() {
        $this->css[] = YII_DEBUG ? 'css/MrSection.css' : 'css/MrSection.min.css';
        $this->css[] = YII_DEBUG ? 'css/jquery.guillotine.css' : 'css/jquery.guillotine.min.css';

        $this->js[] = YII_DEBUG ? 'js/jquery.guillotine.js' : 'js/jquery.guillotine.min.js';
        $this->js[] = YII_DEBUG ? 'js/mr.section.js' : 'js/mr.section.min.js';
    }
}
