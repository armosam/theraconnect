<?php

namespace common\models\queries;

use \yii\db\ActiveQuery;
use common\models\User;
use common\models\UserService;

/**
 * This is the ActiveQuery class for [[\common\models\UserService]].
 *
 * @see \common\models\UserService
 */
class UserServiceQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserService|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return UserService[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * Returns the UserService joined with User model where user status is active.
     *
     * @return $this UserServiceQuery
     */
    public function withActiveUser()
    {
        return $this->joinWith(['user' => function($q){
            /** @var UserQuery $q */
            $q->andWhere(['"user"."status"' => User::USER_STATUS_ACTIVE]);
        }]);
    }
}
