<?php

namespace common\rbac\rules;

use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;
use common\models\UserCredential;

/**
 * Class CredentialOwnerRule
 * @package common\rbac\rules
 */
class CredentialOwnerRule extends Rule
{
    public $name = 'isCredentialOwner';

    /**
     * @param  string|integer $user_id The user ID.
     * @param  Item $item The role or permission that this rule is associated with
     * @param  array $params Parameters passed to ManagerInterface::checkAccess().
     * @return boolean A value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user_id, $item, $params)
    {
        $result = false;
        if( !Yii::$app->user->identity->isProvider ){
            return $result;
        }

        if( $params['model'] instanceof UserCredential ) {
            $result = ($params['model']->user_id === $user_id);
        }
        return $result;
    }
}