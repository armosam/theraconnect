<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\NoteDischargeSummary;

/**
 * This is the ActiveQuery class for [[\common\models\NoteDischargeSummary]].
 *
 * @see \common\models\NoteDischargeSummary
 */
class NoteDischargeSummaryQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoteDischargeSummary[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoteDischargeSummary|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
