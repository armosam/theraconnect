<?php

namespace backend\controllers;

use common\models\LogArchive;
use common\models\searches\LogArchiveSearch;
use Yii;
use yii\db\ActiveRecord;

/**
 * Class LogArchiveController
 */
class LogArchiveController extends LogController
{
    /**
     * @var string|ActiveRecord
     */
    protected $logModel = LogArchive::class;
    /**
     * @var string|ActiveRecord
     */
    protected $logSearchModel = LogArchiveSearch::class;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->getView()->title = Yii::t('app', 'Server Log Archive');

        $this->viewPath = '@backend/views/log';
    }
}