<?php

namespace common\rbac\rules;

use common\models\User;
use yii\rbac\Item;
use yii\rbac\Rule;

/**
 * Checks if user ID matches user profile passed via params
 */
class ProfileOwnerRule extends Rule
{
    public $name = 'isProfileOwner';

    /**
     * @param string|integer $user_id The user ID.
     * @param Item $item The role or permission that this rule is associated with
     * @param array $params Parameters passed to ManagerInterface::checkAccess().
     * @return boolean A value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user_id, $item, $params)
    {
        $result = false;
        if( !empty($params['model']) && ($params['model'] instanceof User) ) {
            $result = ($params['model']->id === $user_id);
        }
        return $result;
    }
}