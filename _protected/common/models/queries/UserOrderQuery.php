<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\UserOrder;

/**
 * This is the ActiveQuery class for [[\common\models\UserOrder]].
 *
 * @see \common\models\UserOrder
 */
class UserOrderQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserOrder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserOrder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
