<?php

namespace common\models\base;

use common\models\Log;

/**
 * This is the model class for table "log_archive". Extends logic of [[Log]].
 * Class LogArchive
 * @package common\models\base
 */
class LogArchive extends Log
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%log_archive}}';
    }
}
