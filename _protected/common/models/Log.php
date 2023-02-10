<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "log".
 * Class Log
 * @package common\models
 * @property string $level_label
 */
class Log extends base\Log
{

    /**
     * Gets level label.
     *
     * @return string
     */
    public function getLevel_label()
    {
        $levels = static::levels();
        $levelLabel = ArrayHelper::getValue($levels, $this->level, '');

        return $levelLabel;
    }

    /**
     * Gets list of labels of levels.
     *
     * @return array List of labels of levels.
     */
    public static function levels()
    {
        return [
            self::LEVEL_TRACE => Yii::t('app', 'Trace'),
            self::LEVEL_INFO => Yii::t('app', 'Info'),
            self::LEVEL_WARNING => Yii::t('app', 'Warning'),
            self::LEVEL_ERROR => Yii::t('app', 'Error'),
        ];
    }

    /**
     * Finds all categories of logs.
     *
     * @return array
     */
    public static function findMappedCategoriesAsArrayAll()
    {
        $logs = static::find()
            ->select('category')
            ->groupBy('category')
            ->asArray()
            ->all();

        $mappedLogs = ArrayHelper::map($logs, 'category', 'category');

        return $mappedLogs;
    }

}