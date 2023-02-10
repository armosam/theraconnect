<?php

namespace common\rbac\helpers;

use Yii;
use Exception;
use common\models\User;
use common\rbac\models\Role;
use yii\base\InvalidArgumentException;

/**
 * Rbac helper class.
 */
class RbacHelper
{
    /**
     * Assigns the appropriate role to the registered user.
     * If this is the first registered user in our system, he will get the
     * theCreator role (this should be you), if not, he will get the customer role by default or
     * that role you specified in the second argument.
     *
     * @param integer $id The id of the registered user.
     * @param string $role_name
     * @return string Role name.
     * @throws Exception
     */
    public static function assignRole($id, $role_name = User::USER_CUSTOMER)
    {
        if(!in_array($role_name, [
            User::USER_SUPER_ADMIN,
            User::USER_ADMIN,
            User::USER_EDITOR,
            User::USER_PROVIDER,
            User::USER_CUSTOMER
        ], true)){
            throw new InvalidArgumentException('Role name is wrong.');
        }

        // make sure there are no leftovers
        Role::deleteAll(['user_id' => $id]);

        $usersCount = User::find()->count();

        $auth = Yii::$app->authManager;

        // if this is the first user in our system, give him theCreator role
        if ($usersCount === 1)
        {
            $role = $auth->getRole(User::USER_SUPER_ADMIN);
            $auth->assign($role, $id);
            return $role->name;
        }

        $role = $auth->getRole($role_name);
        $auth->assign($role, $id);

        // return assigned role name in case you want to use this method in tests
        return $role->name;
    }
}

