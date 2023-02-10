<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\NoteCommunication;

/**
 * This is the ActiveQuery class for [[\common\models\NoteCommunication]].
 *
 * @see \common\models\NoteCommunication
 */
class NoteCommunicationQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoteCommunication[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoteCommunication|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
