<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\Visit;

/**
 * This is the ActiveQuery class for [[\common\models\Visit]].
 *
 * @see \common\models\Visit
 */
class VisitQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Visit[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Visit|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
