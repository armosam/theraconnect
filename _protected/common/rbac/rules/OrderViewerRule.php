<?php

namespace common\rbac\rules;

use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;
use common\models\Order;
use common\helpers\ArrayHelper;

/**
 * Class OrderViewerRule
 * @package common\rbac\rules
 */
class OrderViewerRule extends Rule
{
    public $name = 'isOrderViewer';

    /**
     * @param string|integer $user_id The user ID.
     * @param Item $item The role or permission that this rule is associated with
     * @param array $params Parameters passed to ManagerInterface::checkAccess().
     * @return boolean A value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user_id, $item, $params)
    {
        $result = false;
        if ( !empty($params['model']) && ($params['model'] instanceof Order) ) {
            if ( Yii::$app->user->identity->isProvider ) {
                $providers = $params['model']->orderUsers;
                $provider_ids = ArrayHelper::getColumn($providers, 'user_id');
                $result = (in_array($user_id, $provider_ids));
            } elseif ( Yii::$app->user->identity->isCustomer ) {
                $result = (!empty($params['model']->customer->id) && ($params['model']->customer->id === $user_id));
            }
        }
        return $result;
    }
}