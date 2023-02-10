<?php

namespace common\models\searches;

use common\models\LogArchive;

/**
 * Class LogArchiveSearch The same logic as in [[LogSearch]] but for table [[LogArchive::tableName()]].
 * Class LogArchiveSearch
 * @package common\models
 */
class LogArchiveSearch extends LogSearch
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return LogArchive::tableName();
    }
}
