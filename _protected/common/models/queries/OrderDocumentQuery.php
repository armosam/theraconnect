<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\OrderDocument;

/**
 * This is the ActiveQuery class for [[\common\models\OrderDocument]].
 *
 * @see \common\models\OrderDocument
 */
class OrderDocumentQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrderDocument[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrderDocument|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
