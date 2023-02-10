<?php

namespace frontend\controllers;

use Yii;
use DateInterval;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii2fullcalendar\models\Event;
use common\models\Order;
use common\models\Visit;
use common\helpers\ConstHelper;

/**
 * ProviderCalendarController implements the CRUD actions for Order model.
 */
class ProviderCalendarController extends FrontendController
{
    /**
     * Displays calendar page.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'My Calendar'))
            ->setDescription("THERA Connect View Scheduled Visits.")
            ->setKeywords('THERA Connect,view,schedule,Visits')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('index');
    }

    /**
     * Returns JSON data of scheduled visits
     * @return array
     * @throws InvalidConfigException
     */
    public function actionEvent()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $events = [];

        $orders = Order::find()->joinWith('orderUsers')->where(['user_order.user_id' => Yii::$app->user->id])->all();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $color = '#' . dechex(rand(0x000000, 0xFFFFFF));
                foreach ($order->visits as $visit) {
                    if(!Yii::$app->user->can('viewOwnVisit', ['model' => $visit])) {
                        continue;
                    }
                    $Event = new Event();
                    $Event->id = $visit->id;
                    $Event->resourceId = $visit->order_id;
                    $Event->className = 'mouse-pointer';
                    $Event->color = $color;
                    $Event->title = $visit->order->patient->patientFullName;
                    $Event->start = ConstHelper::dateTime(Yii::$app->formatter->asDatetime($visit->visited_at))->format('Y-m-d H:i:s');
                    $Event->end = ConstHelper::dateTime(Yii::$app->formatter->asDatetime($visit->visited_at))->add(new DateInterval('PT1H'))->format('Y-m-d H:i:s');
                    $Event->nonstandard = ['data' => ['target' => '#provider_calendar_modal_window', 'toggle' => 'modal']];
                    $events[] = $Event;
                }
            }
        }
        return $events;
    }

    /**
     * Returns Visit model for calendar detail view
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $order = $this->findOrder(Yii::$app->request->get('oid', 0));
        $model = $this->findVisit($id, $order->id);

        if(!Yii::$app->user->can('viewOwnVisit', ['model' => $model])) {
            return $this->renderAjax('message', [
                'class' => 'alert alert-danger',
                'message' => 'Sorry You cannot view this visit as you do not have permission.',
            ]);
        }

        return $this->renderAjax('view', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Order model based on order ID and logged in provider.
     * @param int $id Order_id
     * @return Order the loaded model
     * @throws NotFoundHttpException
     */
    protected function findOrder($id)
    {
        if (($order = Order::find()->joinWith('orderUsers')->where(['id' => $id, 'user_order.user_id' => Yii::$app->user->id])->one()) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Service Request not found.'));
        }
        return $order;
    }

    /**
     * Finds the Visit model based on its primary key value and Order ID.
     * If the model is not found, a not exception will be thrown.
     * @param int $id Visit ID
     * @param int $oid Order ID
     * @return Visit the loaded model
     * @throws NotFoundHttpException
     */
    protected function findVisit($id, $oid)
    {
        if (($model = Visit::findOne(['id' => $id, 'order_id' => $oid])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested record not found.'));
        }
        return $model;
    }
}