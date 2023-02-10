<?php

namespace common\rbac\rules;

use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;
use common\helpers\ArrayHelper;

/**
 * Class NotePrinterRule
 * @package common\rbac\rules
 */
class NotePrinterRule extends Rule
{
    public $name = 'isNotePrinter';

    /**
     * @param string|integer $user_id The user ID.
     * @param Item $item The role or permission that this rule is associated with
     * @param array $params Parameters passed to ManagerInterface::checkAccess().
     * @return boolean A value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user_id, $item, $params)
    {
        $result = false;
        if( Yii::$app->user->identity->isProvider ) {
            if (!empty($params['model']) && !empty($params['model']->order->providers)) {
                $providers = $params['model']->order->providers;
                $provider_ids = ArrayHelper::getColumn($providers, 'id');
                $result = (in_array($user_id, $provider_ids));
            }
        } elseif( Yii::$app->user->identity->isCustomer ) {
            if (!empty($params['model']) && isset($params['model']->order->customer->id)) {
                $result = ($params['model']->order->customer->id === $user_id);
            }
        }
        return $result;
    }
}