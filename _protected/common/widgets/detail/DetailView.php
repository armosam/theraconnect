<?php

namespace common\widgets\detail;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class DetailView
 */
class DetailView extends \yii\widgets\DetailView
{
    public $columnGroupsOptions = [
        0 => ['width' => '30%'],
    ];
    public $columnGroupsCount = 2;
    /**
     * @var array the HTML attributes for the container tag of this widget. The `tag` option specifies
     * what container tag should be used. It defaults to `table` if not set.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'table table-striped table-bordered detail-view table-fixed break-word-all'];


    /**
     * Renders the detail view.
     * This is the main entry of the whole detail view rendering.
     */
    public function run()
    {
        $rows = [];
        $i = 0;
        foreach ($this->attributes as $attribute) {
            $rows[] = $this->renderAttribute($attribute, $i++);
        }

        $columnGroup = $this->renderColumnGroups();

        $rows = $columnGroup + $rows;

        $options = $this->options;
        $tag = ArrayHelper::remove($options, 'tag', 'table');
        echo Html::tag($tag, implode("\n", $rows), $options);
    }


    /**
     * Renders the column group HTML.
     * @return array the column group HTML or `false` if no column group should be rendered.
     */
    public function renderColumnGroups()
    {
        $cols = [];
        for ($i = 0; $i < $this->columnGroupsCount; ++$i) {
            $colOptions = ArrayHelper::getValue($this->columnGroupsOptions, $i, []);
            $cols[] = Html::tag('col', '', $colOptions);
        }

        return [Html::tag('colgroup', implode("\n", $cols))];
    }
}