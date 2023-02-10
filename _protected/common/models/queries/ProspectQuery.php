<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\Prospect;

/**
 * This is the ActiveQuery class for [[\common\models\Prospect]].
 *
 * @see \common\models\Prospect
 */
class ProspectQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Prospect[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Prospect|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
