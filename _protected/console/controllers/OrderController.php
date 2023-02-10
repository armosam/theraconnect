<?php

namespace console\controllers;

use Yii;
use yii\db\Expression;
use yii\console\Controller;
use common\helpers\ArrayHelper;
use common\helpers\ConstHelper;
use common\models\events\OrderEvents;
use common\models\Order;
use common\models\User;
use common\Exceptions\OrderAcceptException;
use common\Exceptions\OrderCancelException;
use common\Exceptions\OrderRejectException;
use common\Exceptions\OrderServiceRemovedException;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * Class OrderController
 * @package console\controllers
 */
class OrderController extends Controller
{
//    /**
//     * Approves all submitted orders. If there is issue it will cancel order.
//     * If order submitted in acceptable time periods it approves order and raise event "auto approve"
//     * If order submitted with start date which is very close and has to be started in less time that was set in the configuration
//     * variable "timeBeforeOrderApproval" then it will approve order immediately and raise event "fast submit"
//     * If there is issue with order or service it will cancel order
//     *
//     * command: php yii order/approve
//     * @throws OrderCancelException
//     */
//    public function actionApprove()
//    {
//        $orders = Order::find()
//            ->andWhere(['status_id' => Order::ORDER_STATUS_SUBMITTED])
//            ->all();
//
//        if(!empty($orders)){
//            $approved = 0; $notApproved = 0; $canceled = 0;
//            $timeZone = new DateTimeZone(Yii::$app->timeZone);
//            $delayTime = Yii::$app->params['timeBeforeOrderApproval'];
//
//            foreach($orders as $order){
//                /** @var Order $order */
//                $now = new DateTime('now', $timeZone);
//
//                try {
//                    $service_start = new DateTime($order->order_start, $timeZone);
//                    $service_start->sub(new DateInterval("PT{$delayTime}M"));
//
//                    if ($service_start <= $now) {
//                        if ($order->acceptOrder()) {
//                            $msg = Yii::t('app', 'Order #{order_number}-{order_id} Approved: Service Name: {name}, Start: {start}, End: {end}', [
//                                'order_number' => $order->order_number,
//                                'order_id' => $order->id,
//                                'name' => $order->service->service_name_en,
//                                'start' => $order->order_start,
//                                'end' => $order->order_end,
//                            ]);
//                            $approved++;
//                            Yii::info($msg);
//                            $this->stdout($msg. PHP_EOL);
//
//                        } else {
//                            $errors = $order->getFirstErrors();
//                            $error_message = '';
//                            if (!empty($errors)) {
//                                foreach ($errors as $field => $error) {
//                                    $error_message .= ', ' . $field . ': ' . $error;
//                                }
//                            }
//                            throw new OrderAcceptException(Yii::t('app', 'Error in saving of approved order {message}', ['message' => $error_message]));
//                        }
//
//                    }
//                }catch (OrderServiceRemovedException $e){
//
//                    // There is an issue with service on the order. So we have to cancel order
//                    $order->setAttribute('status_id',  Order::ORDER_STATUS_CANCELED);
//                    $order->orderDetail->setAttributes([
//                        'canceled_at' => new Expression('NOW()'),
//                        'canceled_by' => User::USER_SYSTEM_USER_ID,
//                        'cancellation_reason' => ConstHelper::ORDER_CANCELLATION_REASON_SERVICE_REMOVED
//                    ]);
//                    if(!$order->updateAttributes(['status_id']) || !$order->orderDetail->save()){
//                        throw new OrderCancelException(Yii::t('app', 'Order cancellation failed.'));
//                    }
//                    $order->trigger(OrderEvents::EVENT_ORDER_CANCELED);
//                    $msg = Yii::t('app', 'Error: {message} Order #{order_number}-{order_id}, Service Name: {name}, Start: {start}, End: {end} canceled as provider has not that service.', [
//                        'message' => $e->getMessage(),
//                        'order_number' => $order->order_number,
//                        'order_id' => $order->id,
//                        'name' => $order->service->service_name_en,
//                        'start' => $order->order_start,
//                        'end' => $order->order_end,
//                    ]);
//                    $canceled++;
//                    Yii::error($msg);
//                    $this->stdout($msg. PHP_EOL);
//
//                }catch(OrderAcceptException $e){
//
//                    // There is an issue with order. So we have to cancel it
//                    $order->setAttribute('status_id',  Order::ORDER_STATUS_CANCELED);
//                    $order->orderDetail->setAttributes([
//                        'canceled_at' => new Expression('NOW()'),
//                        'canceled_by' => User::USER_SYSTEM_USER_ID,
//                        'cancellation_reason' => ConstHelper::ORDER_CANCELLATION_REASON_ORDER_ISSUE
//                    ]);
//                    if(!$order->updateAttributes(['status_id']) || !$order->orderDetail->save()){
//                        throw new OrderCancelException(Yii::t('app', 'Order cancellation failed.'));
//                    }
//                    $order->trigger(OrderEvents::EVENT_ORDER_CANCELED);
//                    $msg = Yii::t('app', 'Error: {message} Order #{order_number}-{order_id}, Service Name: {name}, Start: {start}, End: {end} canceled as there was an issue to approve it.', [
//                        'message' => $e->getMessage(),
//                        'order_number' => $order->order_number,
//                        'order_id' => $order->id,
//                        'name' => $order->service->service_name_en,
//                        'start' => $order->order_start,
//                        'end' => $order->order_end,
//                    ]);
//                    $canceled++;
//                    Yii::error($msg);
//                    $this->stdout($msg. PHP_EOL);
//
//                }catch(Exception $e){
//                    $msg = Yii::t('app', 'Error: {message} Order #{order_number}-{order_id}, Service Name: {name}, Start: {start}, End: {end} could not be approved.', [
//                        'message' => $e->getMessage(),
//                        'order_number' => $order->order_number,
//                        'order_id' => $order->id,
//                        'name' => $order->service->service_name_en,
//                        'start' => $order->order_start,
//                        'end' => $order->order_end
//                    ]);
//                    $notApproved++;
//                    Yii::error($msg);
//                    $this->stdout($msg. PHP_EOL);
//                }
//            }
//
//            $msg = Yii::t('app', $now->format('Y-m-d H:i:s').': Approved: {approved}, Not Approved: {notApproved}, Canceled:     {canceled} order(s){new_line}', [
//                'approved' => $approved,
//                'notApproved' => $notApproved,
//                'canceled' => $canceled,
//                'new_line' => PHP_EOL]);
//            $this->stdout($msg. PHP_EOL);
//
//
//        }else{
//            $this->stdout( Yii::t('app', 'There are no new submitted orders to approve...{new_line}', ['new_line'=>PHP_EOL]));
//        }
//    }
//
//    /**
//     * Completes all approved orders that already past by end date. If there is issue it will cancel order.
//     * It will check if order is past and it has not been canceled
//     * It will change status to complete and raise event
//     * If there is an issue with order or service it will cancel order.
//     *
//     * command: php yii order/complete
//     * @throws OrderCancelException
//     */
//    public function actionComplete()
//    {
//        $orders = Order::find()
//            ->andWhere(['status_id' => Order::ORDER_STATUS_ACCEPTED])
//            ->andWhere(['<', 'service_end', 'NOW()'])
//            ->all();
//
//        if(!empty($orders)){
//            $completed = 0; $notCompleted = 0; $canceled = 0;
//            foreach($orders as $order){
//
//                try {
//                    // Checks if the service in the order is active in the provider's service list
//                    if (empty($order->provider->userService) || $order->service_id !== $order->provider->userService->service_id){
//                        throw new OrderServiceRemovedException(Yii::t('app', 'Requested Provider has not this service or removed recently.'));
//                    }
//
//                    $order->setAttribute('status_id', Order::ORDER_STATUS_REJECTED);
//                    if ($order->save()) {
//                        $msg = Yii::t('app', 'Order #{order_number}-{order_id} Completed. Service Name: {name}, Start: {start}, End: {end}', [
//                            'order_number' => $order->order_number,
//                            'order_id' => $order->id,
//                            'name' => $order->service->service_name_en,
//                            'start' => $order->order_start,
//                            'end' => $order->order_end,
//                        ]);
//                        $completed++;
//                        Yii::info($msg);
//                        echo $msg . PHP_EOL;
//                    } else {
//                        $error_message = '';
//                        $errors = $order->getFirstErrors();
//                        if (!empty($errors)) {
//                            foreach ($errors as $field => $error) {
//                                $error_message .= ', ' . $field . ': ' . $error;
//                            }
//                        }
//                        throw new OrderRejectException(Yii::t('app', 'Error in saving of completed order{message}', ['message' => $error_message]));
//                    }
//                }catch(OrderServiceRemovedException $e){
//
//                    // There is an issue with service on the order. So we have to cancel order
//                    $order->setAttribute('status_id', Order::ORDER_STATUS_CANCELED);
//                    $order->orderDetail->setAttributes([
//                        'canceled_at' => new Expression('NOW()'),
//                        'canceled_by' => User::USER_SYSTEM_USER_ID,
//                        'cancellation_reason' => ConstHelper::ORDER_CANCELLATION_REASON_SERVICE_REMOVED
//                    ]);
//                    if(!$order->updateAttributes(['status_id']) || !$order->orderDetail->save()){
//                        throw new OrderCancelException(Yii::t('app', 'Order cancellation failed.'));
//                    }
//                    $order->trigger(OrderEvents::EVENT_ORDER_CANCELED);
//                    $msg = Yii::t('app', 'Error: {message} Order #{order_number}-{order_id}, Service Name: {name}, Start: {start}, End: {end} canceled as provider has not that service', [
//                        'message' => $e->getMessage(),
//                        'order_number' => $order->order_number,
//                        'order_id' => $order->id,
//                        'name' => $order->service->service_name_en,
//                        'start' => $order->order_start,
//                        'end' => $order->order_end,
//                    ]);
//                    $canceled++;
//                    Yii::error($msg);
//                    echo $msg . PHP_EOL;
//
//                }catch(OrderRejectException $e){
//
//                    // There is an issue with order. So we have to cancel it
//                    $order->setAttribute('status_id', Order::ORDER_STATUS_CANCELED);
//                    $order->orderDetail->setAttributes([
//                        'canceled_at' => new Expression('NOW()'),
//                        'canceled_by' => User::USER_SYSTEM_USER_ID,
//                        'cancellation_reason' => ConstHelper::ORDER_CANCELLATION_REASON_ORDER_ISSUE
//                    ]);
//                    if(!$order->updateAttributes(['status_id']) || !$order->orderDetail->save()){
//                        throw new OrderCancelException(Yii::t('app', 'Order cancellation failed.'));
//                    }
//                    $order->trigger(OrderEvents::EVENT_ORDER_CANCELED);
//                    $msg = Yii::t('app', 'Error: {message} Order #{order_number}-{order_id}, Service Name: {name}, Start: {start}, End: {end} canceled as there was an issue to complete it.', [
//                        'message' => $e->getMessage(),
//                        'order_number' => $order->order_number,
//                        'order_id' => $order->id,
//                        'name' => $order->service->service_name_en,
//                        'start' => $order->order_start,
//                        'end' => $order->order_end,
//                    ]);
//                    $canceled++;
//                    Yii::error($msg);
//                    echo $msg . PHP_EOL;
//
//                }catch(Exception $e){
//                    $msg = Yii::t('app', 'Error: {message} Order #{order_number}-{order_id}, Service Name: {name}, Start: {start}, End: {end} could not be completed.', [
//                        'message' => $e->getMessage(),
//                        'order_number' => $order->order_number,
//                        'order_id' => $order->id,
//                        'name' => $order->service->service_name_en,
//                        'start' => $order->order_start,
//                        'end' => $order->order_end
//                    ]);
//                    $notCompleted++;
//                    Yii::error($msg);
//                    echo $msg . PHP_EOL;
//                }
//            }
//            echo Yii::t('app', 'Completed: {completed}, Not Completed: {notCompleted}, Canceled: {canceled} order(s){new_line}', [
//                'completed' => $completed,
//                'notCompleted' => $notCompleted,
//                'canceled' => $canceled,
//                'new_line' => PHP_EOL]);
//        }else{
//            echo Yii::t('app', 'There are no new approved orders to complete...{new_line}', ['new_line'=>PHP_EOL]);
//        }
//    }
//
//    /**
//     * Sends reminder notifications to all approved order's customers 24 and 48 hours before start of service
//     *
//     * command: php yii order/reminder
//     * @throws Expression
//     */
//    public function actionReminder()
//    {
//        try {
//            $cronPeriod = Yii::$app->params['cronPeriodForOrderReminder'];
//            $one_day_notification = 0;
//            $two_day_notification = 0;
//            $timeZone = new DateTimeZone(Yii::$app->timeZone);
//
//            $now = date_create('now', $timeZone);
//            $now_cloned = clone $now;
//            $one_day_before = $now_cloned->modify("+ 24 hours")->format('Y-m-d H:i:s');
//            $one_day_next_run = $now_cloned->modify("+ $cronPeriod minutes")->modify('-1 second')->format('Y-m-d H:i:s');
//            $two_days_next_run = $now_cloned->modify("+ 24 hours")->format('Y-m-d H:i:s');
//            $two_days_before = $now_cloned->modify("- $cronPeriod minutes")->modify('+1 second')->format('Y-m-d H:i:s');
//
//
//            $orders_for_one_day_before = Order::find()->with(['customer', 'customer.userDetail'])
//                ->andWhere(['status_id' => Order::ORDER_STATUS_ACCEPTED])
//                ->andWhere(['between', 'service_start', $one_day_before, $one_day_next_run])
//                ->all();
//            if (!empty($orders_for_one_day_before)) {
//                foreach ($orders_for_one_day_before as $order) {
//                    $order->trigger(OrderEvents::EVENT_ORDER_PROVIDER_REMINDER);
//                    $one_day_notification++;
//                }
//            }
//
//            $orders_for_two_days_before = Order::find()->with(['customer', 'customer.userDetail'])
//                ->andWhere(['status_id' => Order::ORDER_STATUS_ACCEPTED])
//                ->andWhere(['between', 'order_start', $two_days_before, $two_days_next_run])
//                ->all();
//            if (!empty($orders_for_two_days_before)) {
//                foreach ($orders_for_two_days_before as $order) {
//                    $order->trigger(OrderEvents::EVENT_ORDER_PROVIDER_REMINDER);
//                    $two_day_notification++;
//                }
//            }
//
//            $msg = Yii::t('app','Date: {date_time}, One Day Notifications: {one_day}, Two Days Notifications: {two_days}', [
//                'date_time' =>  $now->format('Y-m-d H:i:s') ,
//                'one_day' => $one_day_notification,
//                'two_days' => $two_day_notification
//            ]);
//            $this->stdout($msg.PHP_EOL);
//            Yii::info($msg, 'Console-Order-'.__FUNCTION__);
//
//        } catch (Exception $e) {
//            $msg = 'Filed to send reminder notifications. Error Message: '. $e->getMessage();
//            $this->stdout($msg.PHP_EOL);
//            Yii::error($msg,'Console-Order-'.__FUNCTION__);
//        }
//    }
}
