<?php

namespace common\rbac\rules;

use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;
use common\models\Order;

/**
 * Class OrderOwnerRule
 * @package common\rbac\rules
 */
class OrderOwnerRule extends Rule
{
    public $name = 'isOrderOwner';

    /**
     * @param  string|integer $user_id The user ID.
     * @param  Item $item The role or permission that this rule is associated with
     * @param  array $params Parameters passed to ManagerInterface::checkAccess().
     * @return boolean A value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user_id, $item, $params)
    {
        $result = false;
        if( !Yii::$app->user->identity->isCustomer ) {
            return $result;
        }

        if($params['model'] instanceof Order) {
            $result = (!empty($params['model']->customer->id) && $params['model']->customer->id === $user_id);
        }
        return $result;
    }
}