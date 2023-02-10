<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\NoteDischargeOrder;

/**
 * This is the ActiveQuery class for [[\common\models\NoteDischargeOrder]].
 *
 * @see \common\models\NoteDischargeOrder
 */
class NoteDischargeOrderQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoteDischargeOrder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoteDischargeOrder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
