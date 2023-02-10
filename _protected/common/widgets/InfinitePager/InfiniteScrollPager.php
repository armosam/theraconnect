<?php

namespace common\widgets\InfinitePager;

use yii\helpers\Json;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;

/**
 * Class InfiniteScrollPager
 * @package common\widgets
 */
class InfiniteScrollPager extends LinkPager
{
    public $containerSelector = '.list-view';
    public $itemSelector = '.item';
    public $paginationSelector = '.pagination';
    public $nextSelector = '.pagination .next a:first';
    public $wrapperSelector = '.list-view';
    public $bufferPx = 40;
    public $pjaxContainer = null;
    public $autoStart = true;
    public $alwaysHidePagination = true;
    // Direct options of jquery plugin
    public $pluginOptions = [];

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $default = [
            'pagination' => $this->paginationSelector,
            'next' => $this->nextSelector,
            'item' => $this->itemSelector,
            'state' => [
                'isPaused' => !$this->autoStart,
            ],
            'pjax' => [
                'container' => $this->pjaxContainer,
            ],
            'bufferPx' => $this->bufferPx,
            'wrapper' => $this->wrapperSelector,
            'alwaysHidePagination' => $this->alwaysHidePagination,
        ];
        $this->pluginOptions = ArrayHelper::merge($default, $this->pluginOptions);
        InfiniteScrollAsset::register($this->view);
        $this->initInfiniteScroll();
        parent::init();
    }

    /**
     * @return string|void
     */
    public function run()
    {
        parent::run();
    }

    /**
     * Initializes infinitescroll JS
     */
    public function initInfiniteScroll()
    {
        $options = Json::encode($this->pluginOptions);
        $js = "$('{$this->containerSelector}').infinitescroll({$options});";
        $this->view->registerJs($js);
    }
}