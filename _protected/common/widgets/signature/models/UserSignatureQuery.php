<?php

namespace common\widgets\signature\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[common\widgets\signature\models\base\UserSignature]].
 *
 * @see UserSignature
 */
class UserSignatureQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserSignature[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserSignature|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
