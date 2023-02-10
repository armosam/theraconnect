<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\helpers\ConstHelper;
use common\models\CredentialType;

/**
 * This is the ActiveQuery class for [[\common\models\queries\CredentialType]].
 *
 * @see \common\models\CredentialType
 */
class CredentialTypeQuery extends ActiveQuery
{
    /**
     * @param bool $state
     * @return CredentialTypeQuery
     */
    public function active($state = true)
    {
        $status = ($state===true) ? ConstHelper::STATUS_ACTIVE : ConstHelper::STATUS_PASSIVE;
        return $this->andWhere(['[[credential_type.status]]' => $status]);
    }

    /**
     * @inheritdoc
     * @return CredentialType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return CredentialType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param $sort
     * @return CredentialTypeQuery|null
     */
    public function order($sort)
    {
        return $this->addOrderBy([CredentialType::tableName() .'.ordering' => $sort]);
    }
}
