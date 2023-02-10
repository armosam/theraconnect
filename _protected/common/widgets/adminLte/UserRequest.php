<?php

namespace common\widgets\adminLte;

use Yii;
use yii\bootstrap\Widget;
use common\models\Patient;
use common\models\User;
use yii\db\Expression;

/**
 * Class UserRequest
 * @package common\widgets\adminLte
 */
class UserRequest extends Widget
{
    /**
     * @return array|string
     */
    public function run()
    {
        if(!Yii::$app->user->isGuest && Yii::$app->has('user')){

            $patients = $this->getActiveRequestsForCurrentUser(Yii::$app->user->id);

            if (!empty($patients)){
                return $this->render('user-request', [
                    'patients' => $patients
                ]);
            }
        }
        return null;
    }

    /**
     * Returns orders of current logged user updated last 24 hours
     * @param $id
     * @return array|Patient[]
     */
    public function getActiveRequestsForCurrentUser($id = 0)
    {
        $user = User::findOne([$id]);
        $orders = [];
        if(!empty($user)) {
            if ($user->role->item_name === User::USER_CUSTOMER) {
                $orders = Patient::find()->fromActiveCustomers()
                    ->where(['patient.status' => Patient::ORDER_STATUS_ACCEPTED])
                    ->where(['patient.customer_id' => $id])
                    ->where(['>', 'accepted_at', new Expression("NOW() - INTERVAL '28 days'")])
                    ->orderBy(['accepted_at' => SORT_DESC])
                    ->limit(10)
                    ->all();
            } elseif ($user->role->item_name === User::USER_PROVIDER) {
                $orders = Patient::find()->fromActiveCustomers()
                    ->where(['patient.status' => Patient::ORDER_STATUS_SUBMITTED])
                    ->where(['order.provider_id' => $id])
                    ->where(['>', 'submitted_at', new Expression("NOW() - INTERVAL '28 days'")])
                    ->orderBy(['submitted_at' => SORT_DESC])
                    ->limit(10)
                    ->all();
            } else {
                $orders = Patient::find()->fromActiveCustomers()
                    ->where(['>', 'submitted_at', new Expression("NOW() - INTERVAL '28 days'")])
                    ->andWhere(['or', ['order.status' => Patient::ORDER_STATUS_SUBMITTED], ['order.status' => Patient::ORDER_STATUS_ACCEPTED]])
                    ->orderBy(['submitted_at' => SORT_DESC, 'accepted_at' => SORT_DESC])
                    ->limit(100)
                    ->all();
            }
        }
        return $orders;
    }
}