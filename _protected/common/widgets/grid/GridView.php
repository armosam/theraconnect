<?php

namespace common\widgets\grid;

use Yii;
use Exception;
use yii\web\Cookie;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use common\widgets\perpage\PerPageWidget;

/**
 * Class GridView Add additional pagination to top, add per page widget.
 *
 * @property null|string|int $perPageFromCookies
 * @property int $perPage
 * @property string|int $cookiesPerPage
 */
class GridView extends \yii\grid\GridView
{
    /**
     * @var string the layout that determines how different sections of the grid view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{perPage}`: the per-page dropdown. See [[renderPerPage()]]
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     */
    public $layout = "{perPage} {pager} {summary}\n{items}\n{pager}";
    /**
     * @var array the HTML attributes for the summary of the list view.
     * The "tag" element specifies the tag name of the summary element and defaults to "div".
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $summaryOptions = ['class' => 'summary-right'];
    /**
     * @var array the configuration for the per pager widget. By default, [[PerPageWidget]] will be
     * used to render the per pager. You can use a different widget class by configuring the "class" element.
     */
    public $perPager = [];
    /**
     * @var string additional jQuery selector for selecting filter input fields
     */
    public $filterSelector = 'select[name="per-page"]';
    /**
     * @var array the HTML attributes for the grid table element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $tableOptions = ['class' => 'table table-striped table-bordered'];
    /**
     * @var array the HTML attributes of container of grid table
     */
    public $options = ['class' => 'table-responsive', 'style' => 'border:none;'];
    /**
     * @var string Name of cookie to store per page value.
     * Set your own value for each widget if you want to have different values for each per page dropdown.
     */
    public $perPageCookieKey = 'gridPerPage';


    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        $this->dataProvider->getPagination()->pageSize = $this->getPerPage();

        GridViewAsset::register($this->view);
    }

    /**
     * Gets per page value from url or cookie.
     *
     * @return int
     */
    protected function getPerPage()
    {
        $cookiesPerPage = $this->getPerPageFromCookies();
        $pagination = $this->dataProvider->getPagination();

        if (!empty($_GET[$pagination->pageSizeParam])) {
            $perPage = $_GET[$pagination->pageSizeParam];
        } elseif ($cookiesPerPage) {
            $perPage = $cookiesPerPage;
        } else {
            $perPage = $pagination->defaultPageSize;
        }

        $pagination->pageSize = $perPage;
        $this->setCookiesPerPage($perPage);

        return $perPage;
    }

    /**
     * Gets per page value from cookies, if no value `null` will be return.
     *
     * @return int|string|null Cookie per page value or null
     */
    protected function getPerPageFromCookies()
    {
        $cookiesPerPage = Yii::$app->request->cookies->getValue($this->perPageCookieKey);

        return $cookiesPerPage;
    }

    /**
     * Set per page value in cookies.
     *
     * @param string|int $perPage Per page value.
     */
    protected function setCookiesPerPage($perPage)
    {
        Yii::$app->response->cookies->add(new Cookie([
            'name' => $this->perPageCookieKey,
            'value' => $perPage,
        ]));
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{perPage}':
                return $this->renderPerPage();
            default:
                return parent::renderSection($name);
        }
    }

    /**
     * Renders per page widget.
     *
     * @return string
     * @throws Exception
     */
    public function renderPerPage()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class PerPageWidget */
        $perPager = $this->perPager;
        $class = ArrayHelper::remove($perPager, 'class', PerPageWidget::class);
        $perPager['dataProvider'] = $this->dataProvider;

        return $class::widget($perPager);
    }
}