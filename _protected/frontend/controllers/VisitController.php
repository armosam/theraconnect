<?php

namespace frontend\controllers;

use Yii;
use Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\db\Expression;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use common\models\Order;
use common\models\Visit;
use common\models\NoteEval;
use common\models\NoteProgress;
use common\models\NoteRouteSheet;
use common\models\NoteSupplemental;
use common\models\NoteCommunication;
use common\models\NoteDischargeOrder;
use common\models\NoteDischargeSummary;
use common\exceptions\NoteException;
use common\helpers\ConstHelper;

/**
 * VisitController implements the CRUD actions for Visit model.
 */
class VisitController extends FrontendController
{
    /**
     * Lists all Visit models.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $oid = Yii::$app->request->get('oid', 0);
        $order_id = Yii::$app->request->post('expandRowKey', $oid);
        $order = $this->findOrder($order_id);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $order->visits,
            'pagination' => false,
            'sort' => false,
        ]);

        return $this->renderajax('index', [
            'order' => $order,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Visit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Schedule a Visit'))
            ->setDescription('THERA Connect schedule a visit.')
            ->setKeywords('THERA Connect,add new visit')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new Visit();
        $order = $this->findOrder(Yii::$app->request->get('oid', 0));
        $model->setAttribute('order_id', $order->id);

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageVisit', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot add a new visit as you do not have permission on this order.',
                ]);
            }

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if($model->save()) {
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Visit successfully scheduled.'),
                        'container' => '#visit-subgrid-'.$order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $order->id])
                    ];
                }

                Yii::error(sprintf("Visit for order %s failed to schedule at %s. error: %s", $model->order_id, $model->visited_at, implode(', ', $model->getFirstErrors())), 'VisitController-'.__FUNCTION__);
                return [
                    'success' => false,
                    'message' => Yii::t('app', 'Visit failed to scheduled. {error}', ['error' => implode(', ', $model->getFirstErrors())]),
                    'container' => '#visit-subgrid-'.$order->order_number,
                    'url' => Url::to(['visit/index', 'oid' => $order->id])
                ];
            }

            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('create', [
                    'model' => $model
                ]);
            }
        }

        if(!Yii::$app->user->can('manageVisit', ['model' => $model])) {
            Yii::$app->session->setFlash('error', 'Sorry You cannot add a new visit as you do not have permission on this order.');
            return $this->redirect(['/provider-order/index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Visit scheduled successfully.');
            } else {
                Yii::error(sprintf("Visit for order %s failed to schedule at %s", $model->order_id, $model->visited_at), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', 'Visit failed to re-schedule.');
            }

            return $this->redirect(['provider-order/index']);
        }
        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Visit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id Visit ID
     * @return mixed
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Re-Schedule a Visit'))
            ->setDescription('THERA Connect re-schedule a visit.')
            ->setKeywords('THERA Connect,update visit')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $order = $this->findOrder(Yii::$app->request->get('oid', 0));
        $model = $this->findModel($id, $order->id);

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageVisit', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this visit as you do not have permission.',
                ]);
            }

            // Do not allow modifying visit when it already started
            if($model->isVisitStarted()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this visit as it started already.',
                ]);
            }

            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;

                if($model->save()) {
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Visit successfully re-scheduled.'),
                        'container' => '#visit-subgrid-'.$order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $order->id])
                    ];
                }

                Yii::error(sprintf("Visit for order %s failed to re-schedule at %s. error: %s", $model->order_id, $model->visited_at, implode(', ', $model->getFirstErrors())), 'VisitController-'.__FUNCTION__);
                return [
                    'success' => false,
                    'message' => Yii::t('app', 'Visit failed to re-scheduled. {error}', ['error' => implode(', ', $model->getFirstErrors())]),
                    'container' => '#visit-subgrid-'.$order->order_number,
                    'url' => Url::to(['visit/index', 'oid' => $order->id])
                ];
            }

            return $this->renderAjax('update', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageVisit', ['model' => $model])) {
            Yii::$app->session->setFlash('error', 'Sorry You cannot modify this visit as you do not have permission.');
            return $this->redirect(['/provider-order/index']);
        }

        if ($model->isVisitStarted()) {
            Yii::$app->session->setFlash('error', 'Sorry You cannot modify this visit as it started already.');
            return $this->redirect(['/provider-order/index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Visit re-scheduled successfully.');
            } else {
                Yii::error(sprintf("Visit for order %s failed to re-schedule at %s", $model->order_id, $model->visited_at), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', 'Visit failed to re-schedule.');
            }

            return $this->redirect(['provider-order/index']);
        }
        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Manage Communication Note
     * @param int $id Visit ID
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionNoteCommunication($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Manage Communication Note'))
            ->setDescription('THERA Connect manage communication note.')
            ->setKeywords('THERA Connect,manage,communication note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteCommunication(['order_id' => Yii::$app->request->get('oid', 0), 'visit_id' => $id, 'status' => ConstHelper::NOTE_STATUS_PENDING]);
        if(($exists = $model->findExistingModel(Yii::$app->user->id)) !== null) {
            $model = $exists;
        }

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this note as you do not have permission.',
                ]);
            }

            // Check if note is not in the pending status then prevent changes
            if(!$model->isPending()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-info',
                    'message' => 'Communication Note you are requesting already submitted at ' . Yii::$app->formatter->asDatetime($model->submitted_at),
                    'model' => $model
                ]);
            }

            $model->setAttribute('provider_id', Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {

                    $model->setExistingAttributes();

                    if(!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Communication Note failed to manage.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Communication Note managed successfully.'),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];

                } catch (Exception $e) {

                    Yii::error(sprintf("Communication Note for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                    return [
                        'success' => false,
                        'message' => Yii::t('app', $e->getMessage()),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];
                }
            }

            return $this->renderAjax('note/communication', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry You cannot modify this note as you do not have permission.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if note is not in the pending status then prevent changes
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Communication Note is already submitted.'));
            return $this->redirect(['/provider-order/index']);
        }

        $model->setAttribute('provider_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try {

                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app', 'Communication Note failed to manage.'));
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Communication Note managed successfully.'));

            } catch (Exception $e) {
                Yii::error(sprintf("Communication Note for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['/provider-order/index']);
        }

        return $this->render('note/communication', [
            'model' => $model
        ]);
    }

    /**
     * Manage Discharge Order
     * @param int $id Visit ID
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionNoteDischargeOrder($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Manage Discharge Order'))
            ->setDescription('THERA Connect manage discharge order.')
            ->setKeywords('THERA Connect,manage,discharge order')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteDischargeOrder(['order_id' => Yii::$app->request->get('oid', 0), 'visit_id' => $id, 'status' => ConstHelper::NOTE_STATUS_PENDING]);
        if(($exists = $model->findExistingModel(Yii::$app->user->id)) !== null) {
            $model = $exists;
        }

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this note as you do not have permission.',
                ]);
            }

            // Check if note is not in the pending status then prevent changes
            if(!$model->isPending()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-info',
                    'message' => 'Discharge Order you are requesting already submitted at ' . Yii::$app->formatter->asDatetime($model->submitted_at),
                    'model' => $model
                ]);
            }

            // Check if visit should have Discharge Order (isLastVisit()) otherwise show message
            if(!$model->visit->isLastVisit()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => Yii::t('app', 'Only last visit could have a Discharge Order.')
                ]);
            }

            $model->setAttribute('provider_id', Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {

                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Discharge Order failed to manage.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Discharge Order managed successfully.'),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];

                } catch (Exception $e) {

                    Yii::error(sprintf("Discharge Order for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];
                }
            }

            return $this->renderAjax('note/discharge_order', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry You cannot modify this note as you do not have permission.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if note is not in the pending status then prevent changes
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', Yii::t('app','Discharge Order is already submitted.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if visit should have Discharge Order (isLastVisit()) otherwise show message
        if(!$model->visit->isLastVisit()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Only last visit could have a Discharge Order.'));
            return $this->redirect(['/provider-order/index']);
        }

        $model->setAttribute('provider_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try{

                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app', 'Discharge Order failed to manage.'));
                }

                Yii::$app->session->setFlash('success', Yii::t('app','Discharge Order managed successfully.'));

            } catch(Exception $e) {
                Yii::error(sprintf("Discharge Order for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['/provider-order/index']);
        }

        return $this->render('note/discharge_order', [
            'model' => $model
        ]);
    }

    /**
     * Manage Discharge Summary
     * @param int $id Visit ID
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionNoteDischargeSummary($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Manage Discharge Summary'))
            ->setDescription('THERA Connect manage discharge summary.')
            ->setKeywords('THERA Connect,manage,discharge summary')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteDischargeSummary(['order_id' => Yii::$app->request->get('oid', 0), 'visit_id' => $id, 'status' => ConstHelper::NOTE_STATUS_PENDING]);
        if(($exists = $model->findExistingModel(Yii::$app->user->id)) !== null) {
            $model = $exists;
        }

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this note as you do not have permission.',
                ]);
            }

            // Check if note is not in the pending status then prevent changes
            if(!$model->isPending()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-info',
                    'message' => 'Discharge Summary you are requesting already submitted at ' . Yii::$app->formatter->asDatetime($model->submitted_at),
                    'model' => $model
                ]);
            }

            // Check if visit should have Discharge Summary (isLastVisit()) otherwise show message
            if(!$model->visit->isLastVisit()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => Yii::t('app','Only last visit could have a Discharge Summary.')
                ]);
            }

            $model->setAttribute('provider_id', Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {

                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Discharge Summary failed to manage.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Discharge Summary managed successfully.'),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];

                } catch (Exception $e) {
                    Yii::error(sprintf("Discharge Summary for visit %s failed to manage.", $id), 'VisitController-'.__FUNCTION__);
                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'container' => '#visit-subgrid-'.$model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];
                }
            }

            return $this->renderAjax('note/discharge_summary', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry You cannot modify this note as you do not have permission.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if note is not in the pending status then prevent changes
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Discharge Summary is already submitted.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if visit should have Discharge Summary (isLastVisit()) otherwise show message
        if(!$model->visit->isLastVisit()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Only last visit could have a Discharge Summary.'));
            return $this->redirect(['/provider-order/index']);
        }

        $model->setAttribute('provider_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try {

                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app', 'Discharge Summary failed to manage.'));
                }

                Yii::$app->session->setFlash('success', 'Discharge Summary managed successfully.');

            } catch (Exception $e) {
                Yii::error(sprintf("Discharge Summary for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['/provider-order/index']);
        }

        return $this->render('note/discharge_summary', [
            'model' => $model
        ]);
    }

    /**
     * Manage Eval Note
     * @param int $id Visit ID
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionNoteEval($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Manage Evaluation Note'))
            ->setDescription('THERA Connect manage eval note.')
            ->setKeywords('THERA Connect,manage,eval note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteEval(['order_id' => Yii::$app->request->get('oid', 0), 'visit_id' => $id, 'status' => ConstHelper::NOTE_STATUS_PENDING]);
        if(($exists = $model->findExistingModel(Yii::$app->user->id)) !== null) {
            $model = $exists;
        }
        $model->setAttribute('frequency', !empty($model->order->service_frequency) && in_array($model->order->frequency_status, [Order::ORDER_FREQUENCY_STATUS_SUBMITTED, Order::ORDER_FREQUENCY_STATUS_APPROVED]) ? $model->order->service_frequency : $model->frequency);

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this note as you do not have permission.',
                ]);
            }

            // Check if note is not in the pending status then prevent changes
            if(!$model->isPending()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-info',
                    'message' => 'Evaluation Note you are requesting already submitted at ' . Yii::$app->formatter->asDatetime($model->submitted_at),
                    'model' => $model
                ]);
            }

            // Check if visit should have eval note (isFirstVisit()) otherwise show message
            if(!$model->visit->isFirstVisit()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => Yii::t('app','Only first visit could have an Evaluation Note.'),
                ]);
            }

            $model->setAttribute('provider_id', Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {

                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Evaluation Note failed to manage.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Evaluation Note managed successfully.'),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];

                } catch (Exception $e) {
                    Yii::error(sprintf("Evaluation Note for visit %s failed to manage.", $id), 'VisitController-'.__FUNCTION__);
                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'container' => '#visit-subgrid-'.$model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];
                }
            }

            return $this->renderAjax('note/eval', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry You cannot modify this note as you do not have permission.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if note is not in the pending status then prevent changes
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', Yii::t('app','Evaluation Note is already submitted.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if visit should have eval note (isFirstVisit()) otherwise show message
        if(!$model->visit->isFirstVisit()) {
            Yii::$app->session->setFlash('error', Yii::t('app','Only first visit could have an Evaluation Note.'));
            return $this->redirect(['/provider-order/index']);
        }

        $model->setAttribute('provider_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try {

                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app', 'Evaluation Note failed to manage.'));
                }

                Yii::$app->session->setFlash('success', Yii::t('app','Evaluation Note managed successfully.'));

            } catch (Exception $e) {
                Yii::error(sprintf("Evaluation Note for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['/provider-order/index']);
        }

        return $this->render('note/eval', [
            'model' => $model
        ]);
    }

    /**
     * Manage Progress Note
     * @param int $id Visit ID
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionNoteProgress($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Manage Progress Note'))
            ->setDescription('THERA Connect manage progress note.')
            ->setKeywords('THERA Connect,manage,progress note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteProgress(['order_id' => Yii::$app->request->get('oid', 0), 'visit_id' => $id, 'status' => ConstHelper::NOTE_STATUS_PENDING]);
        if(($exists = $model->findExistingModel(Yii::$app->user->id)) !== null) {
            $model = $exists;
        }

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this note as you do not have permission.',
                ]);
            }

            // Check if note is not in the pending status then prevent changes
            if((!$model->isPending() && ($model->order->orderPTA->id === Yii::$app->user->id)) || (!$model->isPending() && !$model->isSubmittedByPTA() && ($model->order->orderRPT->id === Yii::$app->user->id))) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-info',
                    'message' => 'Progress Note you are requesting already submitted at ' . Yii::$app->formatter->asDatetime($model->submitted_at),
                    'model' => $model
                ]);
            }

            $model->setAttribute('provider_id', Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {

                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Progress Note failed to manage.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Progress Note managed successfully.'),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];

                } catch (Exception $e) {
                    Yii::error(sprintf("Progress Note for visit %s failed to manage.", $id), 'VisitController-'.__FUNCTION__);
                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'container' => '#visit-subgrid-'.$model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];
                }
            }

            return $this->renderAjax('note/progress', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry You cannot modify this note as you do not have permission.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if note is not in the pending status then prevent changes
        if((!$model->isPending() && ($model->order->orderPTA->id === Yii::$app->user->id)) || (!$model->isPending() && !$model->isSubmittedByPTA() && ($model->order->orderRPT->id === Yii::$app->user->id))) {
            Yii::$app->session->setFlash('error', Yii::t('app','Progress Note is already submitted.'));
            return $this->redirect(['/provider-order/index']);
        }

        $model->setAttribute('provider_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try {

                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app','Progress Note failed to manage.'));
                }

                Yii::$app->session->setFlash('success', Yii::t('app','Progress Note managed successfully.'));

            } catch (Exception $e) {
                Yii::error(sprintf("Progress Note for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['/provider-order/index']);
        }

        return $this->render('note/progress', [
            'model' => $model
        ]);
    }

    /**
     * Manage Route Sheet
     * @param int $id Visit ID
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionNoteRouteSheet($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Manage Route Sheet'))
            ->setDescription('THERA Connect manage route sheet.')
            ->setKeywords('THERA Connect,manage,route sheet')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteRouteSheet(['order_id' => Yii::$app->request->get('oid', 0), 'visit_id' => $id,  'status' => ConstHelper::NOTE_STATUS_PENDING]);
        if(($exists = $model->findExistingModel(Yii::$app->user->id)) !== null) {
            $model = $exists;
        }

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this note as you do not have permission.',
                ]);
            }

            // Check if note is not in the pending status then prevent changes
            if(!$model->isPending()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-info',
                    'message' => 'Route Sheet you are requesting already submitted at ' . Yii::$app->formatter->asDatetime($model->submitted_at),
                    'model' => $model
                ]);
            }

            // Check if current date time is equal or close to scheduled visit date time
            /*if (!$model->visit->isVisitTime()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => Yii::t('app','Route Sheet should be signed only during the visit.')
                ]);
            }*/

            $model->setAttribute('provider_id', Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {

                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Route Sheet failed to manage.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Route Sheet managed successfully.'),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];

                } catch (Exception $e) {
                    Yii::error(sprintf("Route Sheet for visit %s failed to manage.", $id), 'VisitController-'.__FUNCTION__);
                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'container' => '#visit-subgrid-'.$model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];
                }
            }

            return $this->renderAjax('note/route_sheet', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry You cannot modify this note as you do not have permission.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if note is not in the pending status then prevent changes
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', Yii::t('app','Route Sheet is already submitted.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if current date time is equal or close to scheduled visit date time
        /*if (!$model->visit->isVisitTime()) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Route Sheet should be signed only during the visit.'));
            return $this->redirect(['/provider-order/index']);
        }*/

        $model->setAttribute('provider_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try {

                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app','Route Sheet failed to manage.'));
                }

                Yii::$app->session->setFlash('success', Yii::t('app','Route Sheet managed successfully.'));

            } catch (Exception $e) {
                Yii::error(sprintf("Route Sheet for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['/provider-order/index']);
        }

        return $this->render('note/route_sheet', [
            'model' => $model
        ]);
    }

    /**
     * Manage Physician Order
     * @param int $id Visit ID
     * @return array|string|Response
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     */
    public function actionNoteSupplemental($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Manage Physician Order'))
            ->setDescription('THERA Connect manage physician order.')
            ->setKeywords('THERA Connect,manage,physician order')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteSupplemental(['order_id' => Yii::$app->request->get('oid', 0), 'visit_id' => $id, 'status' => ConstHelper::NOTE_STATUS_PENDING]);
        if(($exists = $model->findExistingModel(Yii::$app->user->id)) !== null) {
            $model = $exists;
        }
        $model->setAttribute('frequency', !empty($model->order->service_frequency) && in_array($model->order->frequency_status, [Order::ORDER_FREQUENCY_STATUS_SUBMITTED, Order::ORDER_FREQUENCY_STATUS_APPROVED]) ? $model->order->service_frequency : $model->frequency);

        if (Yii::$app->request->isAjax) {

            if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify this note as you do not have permission.',
                ]);
            }

            // Check if note is not in the pending status then prevent changes
            if(!$model->isPending()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-info',
                    'message' => 'Physician Order you are requesting already submitted at ' . Yii::$app->formatter->asDatetime($model->submitted_at),
                    'model' => $model
                ]);
            }

            // Check if visit should have supplemental order (isFirstVisit()) otherwise show message
            if(!$model->visit->isFirstVisit()) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => Yii::t('app','Only first visit could have a Physician Order.')
                ]);
            }

            $model->setAttribute('provider_id', Yii::$app->user->id);

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {

                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Physician Order failed to manage.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Physician Order managed successfully.'),
                        'container' => '#visit-subgrid-' . $model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];

                } catch (Exception $e) {
                    Yii::error(sprintf("Physician Order for visit %s failed to manage.", $id), 'VisitController-'.__FUNCTION__);
                    return [
                        'success' => false,
                        'message' => $e->getMessage(),
                        'container' => '#visit-subgrid-'.$model->order->order_number,
                        'url' => Url::to(['visit/index', 'oid' => $model->order_id])
                    ];
                }
            }

            return $this->renderAjax('note/supplemental', [
                'model' => $model
            ]);
        }

        if(!Yii::$app->user->can('manageNote', ['model' => $model])) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry You cannot modify this note as you do not have permission.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if note is not in the pending status then prevent changes
        if (!$model->isPending()) {
            Yii::$app->session->setFlash('error', Yii::t('app','Physician Order is already submitted.'));
            return $this->redirect(['/provider-order/index']);
        }

        // Check if visit should have supplemental order (isFirstVisit()) otherwise show message
        if(!$model->visit->isFirstVisit()) {
            Yii::$app->session->setFlash('error', Yii::t('app','Only first visit could have a Physician Order.'));
            return $this->redirect(['/provider-order/index']);
        }

        $model->setAttribute('provider_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            try {

                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app','Physician order failed to manage.'));
                }

                Yii::$app->session->setFlash('success', Yii::t('app','Physician Order managed successfully.'));

            } catch (Exception $e) {
                Yii::error(sprintf("Physician Order for visit %s failed to manage.", $id), 'VisitController-' . __FUNCTION__);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['/provider-order/index']);
        }

        return $this->render('note/supplemental', [
            'model' => $model
        ]);
    }

    /**
     * Finds the Visit model based on its primary key value and Order ID.
     * If the model is not found, a not exception will be thrown.
     * @param int $id Visit ID
     * @param int $oid Order ID
     * @return Visit the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $oid)
    {
        if (($model = Visit::findOne(['id' => $id, 'order_id' => $oid])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested record not found.'));
        }
        return $model;
    }

    /**
     * Find order by given id and logged in user ID
     * @param int $id Order ID
     * @return Order The loaded model if successful
     * @throws NotFoundHttpException
     */
    protected function findOrder($id)
    {
        if (($order = Order::find()->joinWith('orderUsers')->where(['id' => $id, 'user_order.user_id' => Yii::$app->user->id])->one()) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Service Request not found.'));
        }
        return $order;
    }
}
