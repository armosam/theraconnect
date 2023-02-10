<?php

namespace frontend\controllers;

use Yii;
use DateInterval;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii2fullcalendar\models\Event;
use yii\base\InvalidConfigException;
use common\models\Order;
use common\models\Visit;
use common\models\Patient;
use common\helpers\ConstHelper;

/**
 * PatientCalendarController implements the CRUD actions for Order model.
 */
class PatientCalendarController extends FrontendController
{
    /**
     * Displays calendar page.
     * @param $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Visit schedule for {name}', ['name' => $model->patientFullName]))
            ->setDescription("THERA Connect View Scheduled Visits.")
            ->setKeywords('THERA Connect,view,schedule,Visits')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Returns JSON data of visits for calendar events
     * @return array
     * @throws InvalidConfigException
     */
    public function actionEvent($pid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $events = [];

        $orders = Order::find()->where(['order.patient_id' => $pid])->all();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $color = '#'.dechex(rand(0x000000, 0xFFFFFF));
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
                    $Event->nonstandard = ['data' => ['target' => '#patient_calendar_modal_window', 'toggle' => 'modal']];
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
    public function actionDetail($id)
    {
        $order = $this->findOrder(Yii::$app->request->get('oid', 0));
        $model = $this->findVisit($id, $order->id);

        if(!Yii::$app->user->can('viewOwnVisit', ['model' => $model])) {
            return $this->renderAjax('message', [
                'class' => 'alert alert-danger',
                'message' => 'Sorry You cannot view this visit as you do not have permission.',
            ]);
        }

        return $this->renderAjax('detail', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the Patient model based on ID.
     * @param int $id patient_id
     * @return Patient the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($patient = Patient::findOne(['id' => $id, 'customer_id' => Yii::$app->user->id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Patient not found.'));
        }
        return $patient;
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
        if (($visit = Visit::findOne(['id' => $id, 'order_id' => $oid])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested record not found.'));
        }
        return $visit;
    }

    /**
     * Find order by given id
     * @param int $id Order ID
     * @return Order The loaded model if successful
     * @throws NotFoundHttpException
     */
    protected function findOrder($id)
    {
        if (($order = Order::findOne($id)) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Service Request not found.'));
        }
        return $order;
    }
}