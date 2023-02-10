<?php

namespace common\widgets\perpage;

use nterms\pagesize\PageSize;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Class PerPageWidget Extends extension "nterms/yii2-pagesize-widget".
 * > NOTE: params `defaultPageSize` and `pageSizeParam` doesn't use and are taken from yii\widgets\LinkPager.
 */
class PerPageWidget extends PageSize
{
    /**
     * @var string the template to be used for rendering the output.
     */
    public $template = '{list}';
    /**
     * @var array the list of options for the drop down list.
     */
    public $options = ['class' => 'form-control'];
    /**
     * @var array the list of page sizes
     */
    public $sizes = [10 => 10, 20 => 20, 50 => 50, 100 => 100];
    /**
     * @var \yii\data\DataProviderInterface the data provider for the view. This property is required.
     */
    public $dataProvider;


    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (!$this->dataProvider) {
            throw new InvalidConfigException('Param "dataProvider" required');
        }

        PerPageAsset::register($this->view);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->id;
        }

        if ($this->encodeLabel) {
            $this->label = Html::encode($this->label);
        }

        $perPage = $this->getPerPage();

        $listHtml = Html::dropDownList('per-page', $perPage, $this->sizes, $this->options);
        $labelHtml = Html::label($this->label, $this->options['id'], $this->labelOptions);

        $output = str_replace(['{list}', '{label}'], [$listHtml, $labelHtml], $this->template);

        return Html::tag('div', $output, ['class' => 'per-page form-group']);
    }

    /**
     * Gets per page value from url or cookie.
     *
     * @return int
     */
    protected function getPerPage()
    {
        $perPage = $this->dataProvider->getPagination()->pageSize;

        return $perPage;
    }
}