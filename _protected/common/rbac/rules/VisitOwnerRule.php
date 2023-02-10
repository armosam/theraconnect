<?php

namespace common\rbac\rules;

use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;
use common\models\Order;
use common\models\Visit;
use common\helpers\ArrayHelper;

/**
 * Class VisitOwnerRule
 * @package common\rbac\rules
 */
class VisitOwnerRule extends Rule
{
    public $name = 'isVisitOwner';

    /**
     * @param string|integer $user_id The user ID.
     * @param Item $item The role or permission that this rule is associated with
     * @param array $params Parameters passed to ManagerInterface::checkAccess(). $params['model'] could be Visit model or Order model (for new Visit)
     * @return boolean A value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user_id, $item, $params)
    {
        $result = false;
        if( !Yii::$app->user->identity->isProvider ) {
            return $result;
        }

        if ((!empty($params['model']) && ($params['model'] instanceof Visit))) {
            $providers = empty($params['model']->order->providers) ? [] : $params['model']->order->providers;
            $provider_ids = ArrayHelper::getColumn($providers, 'id');
            $result = (($params['model']->created_by === $user_id) || (empty($params['model']->created_by) && in_array($user_id, $provider_ids)));
        } elseif ((!empty($params['model']) && ($params['model'] instanceof Order))) {
            $providers = empty($params['model']->providers) ? [] : $params['model']->providers;
            $provider_ids = ArrayHelper::getColumn($providers, 'id');
            $result = in_array($user_id, $provider_ids);
        }

        return $result;
    }
}