<?php

namespace common\traits;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Trait LogTimeSearchTrait Used in search models to filter logs by log time
 * in grid view: start time to end time in one field.
 *
 * > Note: remember to add `start_time`, `end_time`, `log_time_search` to rules:
 *
 * ```
 * public function rules()
 * {
 *      return [
 *            // ... another rules
 *            [['start_time', 'end_time'], 'integer'],
 *            [['log_time_search'], 'safe'],
 *            [['start_time'], 'compare', 'compareAttribute' => 'end_time', 'operator' => '<=', 'skipOnEmpty' => true],
 *            [['end_time'], 'compare', 'compareAttribute' => 'start_time', 'operator' => '>=', 'skipOnEmpty' => true],
 *      ];
 * }
 * ```
 */
trait LogTimeSearchTrait
{
    /**
     * @var int Datetime stamp filter start.
     */
    public $start_time;
    /**
     * @var int Datetime stamp filter end.
     */
    public $end_time;
    /**
     * @var string Log time with range for filtering logs, uses with \kartik\daterange\DateRangePicker.
     */
    public $log_time_search;


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'log_time_search' => Yii::t('app', 'Log Time')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        $this->formatSearchLogTime();

        return parent::beforeValidate();
    }

    /**
     * Formats `log_time_search` by exploding it to `start_time` and `end_time` and converts them to unix timestamp.
     */
    protected function formatSearchLogTime()
    {
        if (!empty($this->log_time_search)) {
            $exploded = explode(' - ', $this->log_time_search);
            $start_time = ArrayHelper::getValue($exploded, 0);
            $this->start_time = $start_time ? Yii::$app->formatter->asTimestamp($start_time) : null;

            $end_time = ArrayHelper::getValue($exploded, 1);
            $this->end_time = $end_time ? Yii::$app->formatter->asTimestamp($end_time) : null;
        }
    }
}