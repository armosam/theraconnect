<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\UserSignature;

/**
 * This is the ActiveQuery class for [[\common\models\UserSignature]].
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
