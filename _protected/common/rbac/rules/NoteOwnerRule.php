<?php

namespace common\rbac\rules;

use Yii;
use yii\rbac\Item;
use yii\rbac\Rule;
use common\helpers\ArrayHelper;

/**
 * Class NoteOwnerRule
 * @package common\rbac\rules
 */
class NoteOwnerRule extends Rule
{
    public $name = 'isNoteOwner';

    /**
     * @param string|integer $user_id The user ID.
     * @param Item $item The role or permission that this rule is associated with
     * @param array $params Parameters passed to ManagerInterface::checkAccess(). $params['model'] is one of our Note... models
     * @return boolean A value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user_id, $item, $params)
    {
        $result = false;
        if( !Yii::$app->user->identity->isProvider ) {
            return $result;
        }

        if( !empty($params['model']) ) {

            $rpt_id = empty($params['model']->order->orderRPT->id) ? null : $params['model']->order->orderRPT->id;
            $visit_creator = empty($params['model']->visit->created_by) ? null : $params['model']->visit->created_by;

            $result = ( ($params['model']->created_by === $user_id) || ($visit_creator === $user_id) || ($rpt_id === $user_id) ) ;
        }

        return $result;
    }
}