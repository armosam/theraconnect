<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\helpers\ConstHelper;
use common\models\UserCredential;
use common\models\CredentialType;

/**
 * This is the ActiveQuery class for [[\common\models\UserCredential]].
 *
 * @see \common\models\UserCredential
 */
class UserCredentialQuery extends ActiveQuery
{
    /**
     * @param bool $state
     * @return UserCredentialQuery
     */
    public function active($state = true)
    {
        $status = ($state===true) ? ConstHelper::STATUS_ACTIVE : ConstHelper::STATUS_PASSIVE;
        return $this->andWhere([UserCredential::tableName().'.status' => $status]);
    }

    /**
     * @inheritdoc
     * @return UserCredential|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return UserCredential[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param $sort
     * @return $this
     */
    public function order($sort)
    {
        return $this->addOrderBy([UserCredential::tableName() . '.ordering' => $sort]);
    }

    public function withActiveCredentialType()
    {
        return $this->joinWith(['credentialType' => function(CredentialTypeQuery $q ){
            $q->active(true);
        }]);
    }

    public function withActiveCredentialTypeOrdered()
    {
        return $this->joinWith(['credentialType' => function(CredentialTypeQuery $q ){
            $q->active(true);
        }])->active(true)->addOrderBy([CredentialType::tableName() .'.ordering' => SORT_ASC])->order(SORT_ASC);
    }


}
