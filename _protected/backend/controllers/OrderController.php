<?php

namespace backend\controllers;

use Yii;
use Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\base\ExitException;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\base\InvalidConfigException;
use common\models\User;
use common\models\Order;
use common\models\Patient;
use common\models\UserOrder;
use common\models\OrderDocument;
use common\helpers\ConstHelper;
use common\models\forms\SubmitOrderForm;
use common\models\forms\CreateOrderForm;
use common\models\forms\CompleteOrderForm;
use common\models\forms\ChangeProviderForm;
use common\models\forms\ApproveFrequencyForm;
use common\exceptions\OrderException;
use common\exceptions\OrderCompleteException;
use common\exceptions\OrderAcceptException;
use common\exceptions\OrderSubmitException;
use common\exceptions\RecordNotFoundException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends BackendController
{
    /**
     * Lists all Order models.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        $pid = Yii::$app->request->get('pid', 0);
        $patient_id = Yii::$app->request->post('expandRowKey', $pid);
        $patient = $this->findPatient($patient_id);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $patient->orders,
            'sort' => false
        ]);

        return $this->renderajax('index', [
            'patient' => $patient,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $pid Patient ID
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate($pid)
    {
        $patient = $this->findPatient($pid);
        $model = new CreateOrderForm(['patient_id' => $patient->id]);

        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Create Service Request for {name}', ['name' => $patient->patientFullName]))
            ->setDescription("THERA Connect Create Service Request.")
            ->setKeywords('THERA Connect,create,Service,Request')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());


        if ($model->load(Yii::$app->request->post())) {
            try {

                $model->createOrder(true);

                if(!empty($model->rpt_provider_id) && ($model->status === Order::ORDER_STATUS_SUBMITTED)) {
                    $model->refresh();
                    if(!$model->acceptOrder($model->rpt_provider_id, $model->pta_provider_id)) {
                        throw new OrderAcceptException('Service request failed to accept and assign to the therapist.');
                    }
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'New service request created successfully.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('success', Yii::t('app', 'New service request created successfully.'));
                return $this->redirect(['patient/index', 'pid' => $patient->id]);

            } catch (OrderAcceptException $e) {
                Yii::error(sprintf('Service Request failed to accept: Patient: %s | Service: %s |  Error: %s', $patient->id, $model->service_id, $e->getMessage()), 'Backend-Order-' . __FUNCTION__);
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'message' => Yii::t('app', 'New service request created, but failed to assign to the specified therapist.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('error', Yii::t('app', 'New service request created, but failed to assign to the specified therapist. Please contact site administration.'));
                return $this->redirect(['patient/index', 'pid' => $patient->id]);
            } catch (Exception $e) {
                Yii::error(sprintf('Service Request failed to create: Patient: %s | Service: %s |  Error: %s', $patient->id, $model->service_id, $e->getMessage()), 'Backend-Order-' . __FUNCTION__);
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'message' => Yii::t('app', 'New service request failed to create.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('error', Yii::t('app', 'New service request failed to create. Please contact site administration.'));
                return $this->redirect(['patient/index', 'pid' => $patient->id]);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'patient' => $patient,
                'model' => $model
            ]);
        }

        return $this->render('create', [
            'patient' => $patient,
            'model' => $model
        ]);
    }

    /**
     * Submits Order status
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return array|string|Response
     */
    public function actionSubmit($id, $pid)
    {
        try {
            $patient = $this->findPatient($pid);
            $model = $this->findSubmitFormModel($id, $patient->id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Submit Service Request for {name}', ['name' => $patient->patientFullName]))
                ->setDescription("THERA Connect Submit Service Request.")
                ->setKeywords('THERA Connect,submit,Service,Request')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

            if ($model->load(Yii::$app->request->post())) {

                if (!$model->submitOrder()) {
                    throw new OrderSubmitException('Service request failed to submit.');
                }

                if (!empty($model->rpt_provider_id) && ($model->status === Order::ORDER_STATUS_SUBMITTED)) {
                    $model->refresh();
                    if (!$model->acceptOrder($model->rpt_provider_id, $model->pta_provider_id)) {
                        throw new OrderAcceptException('Service request submitted successfully, but failed to accept and assign to the therapist.');
                    }
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Service request submitted and accepted successfully.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('success', Yii::t('app', 'Service request submitted and accepted successfully.'));
                return $this->redirect(['patient/index', 'pid' => $patient->id]);
            }

        } catch (Exception $e) {
            Yii::error(sprintf('Service Request failed to submit or accept: Patient: %s | Error: %s', $pid, $e->getMessage()), 'Backend-Order-' . __FUNCTION__);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'container' => '#order-subgrid-' . $pid,
                    'url' => Url::to(['order/index', 'pid' => $pid])
                ];
            }
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['patient/index', 'pid' => $pid]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('submit', [
                'model' => $model,
            ]);
        }

        return $this->render('submit', [
            'model' => $model,
        ]);
    }

    /**
     * Completes order status
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return array|string|Response
     */
    public function actionComplete($id, $pid)
    {
        try {
            $patient = $this->findPatient($pid);
            $model = $this->findCompleteFormModel($id, $patient->id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Complete Service Request for {name}', ['name' => $patient->patientFullName]))
                ->setDescription("THERA Connect Complete Service Request.")
                ->setKeywords('THERA Connect,complete,Service,Request')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

            if($model->load(Yii::$app->request->post())) {
                // Add Discharge Note.
                // Add Summary Note

                if (!$model->completeOrder()) {
                    throw new OrderCompleteException('Service Request failed to complete');
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Service request completed successfully.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('success', Yii::t('app', 'Service request completed successfully.'));
                return $this->redirect(['patient/index', 'pid' => $patient->id]);
            }

        } catch (Exception $e) {
            Yii::error(sprintf('Service Request failed to complete: Patient: %s | Error: %s', $pid, $e->getMessage()), 'Backend-Order-' . __FUNCTION__);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'container' => '#order-subgrid-' . $pid,
                    'url' => Url::to(['order/index', 'pid' => $pid])
                ];
            }
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['patient/index', 'pid' => $pid]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('complete', [
                'model' => $model,
            ]);
        }

        return $this->render('complete', [
            'model' => $model,
        ]);
    }

    /**
     * Approve Order Frequency
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return array|string|Response
     */
    public function actionApproveFrequency($id, $pid)
    {
        try {
            $patient = $this->findPatient($pid);
            $model = $this->findApproveFrequencyFormModel($id, $patient->id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Approve Service Frequency for {name}', ['name' => $patient->patientFullName]))
                ->setDescription("THERA Connect Approve Service Frequency.")
                ->setKeywords('THERA Connect,approve,Service,Frequency')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

            if($model->load(Yii::$app->request->post())) {

                if (!$model->approveFrequency()) {
                    throw new OrderException('Service frequency failed to approve');
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Service frequency approved successfully.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('success', Yii::t('app', 'Service frequency approved successfully.'));
                return $this->redirect(['patient/index', 'pid' => $patient->id]);
            }

        } catch (Exception $e) {
            Yii::error(sprintf('Service frequency failed to approve: Patient: %s | Error: %s', $pid, $e->getMessage()), 'Backend-Order-' . __FUNCTION__);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'container' => '#order-subgrid-' . $pid,
                    'url' => Url::to(['order/index', 'pid' => $pid])
                ];
            }
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['patient/index', 'pid' => $pid]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('approve-frequency', [
                'patient' => $patient,
                'model' => $model,
            ]);
        }

        return $this->render('approve-frequency', [
            'patient' => $patient,
            'model' => $model,
        ]);
    }

    /**
     * Change service provider
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return array|string|Response
     */
    public function actionChangeProvider($id, $pid)
    {
        try {
            $patient = $this->findPatient($pid);
            $model = $this->findChangeProviderFormModel($id, $patient->id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Change Therapist for {name}', ['name' => $patient->patientFullName]))
                ->setDescription("THERA Connect Change Therapist.")
                ->setKeywords('THERA Connect,change,Service,Therapist')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

            if($model->load(Yii::$app->request->post())) {

                if (!$model->changeProvider($model->rpt_provider_id, $model->pta_provider_id)) {
                    throw new OrderException('Therapist failed to change for this patient. Please check logs');
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Therapist has been changed successfully.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('success', Yii::t('app', 'Therapist has been changed successfully.'));
                return $this->redirect(['patient/index', 'pid' => $patient->id]);
            }

        } catch (Exception $e) {
            Yii::error(sprintf('Change therapist failed to complete: Patient: %s | Error: %s', $pid, $e->getMessage()), 'Backend-Order-' . __FUNCTION__);
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'container' => '#order-subgrid-' . $pid,
                    'url' => Url::to(['order/index', 'pid' => $pid])
                ];
            }
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['patient/index', 'pid' => $pid]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('change-provider', [
                'patient' => $patient,
                'model' => $model,
            ]);
        }

        return $this->render('change-provider', [
            'patient' => $patient,
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return mixed
     */
    public function actionView($id, $pid)
    {
        try {
            $patient = $this->findPatient($pid);
            $model = $this->findModel($id, $patient->id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Service Request {order} for {name}', ['order' => $model->order_number, 'name' => $model->patient_name]))
                ->setDescription("THERA Connect View Service Request.")
                ->setKeywords('THERA Connect,view,Service,Request')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['patient/index', 'pid' => $pid]);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'patient' => $patient,
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'patient' => $patient,
            'model' => $model,
        ]);
    }

    /**
     * Returns patient document resource to download
     * @param int $id
     * @return Response|void
     * @throws ExitException
     * @throws NotFoundHttpException
     */
    public function actionDocument(int $id)
    {
        if (($model = OrderDocument::findOne(['[[order_document.id]]' => $id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested document not found.'));
        }

        if(is_file(Yii::getAlias($model->file_content_uri)) && is_readable(Yii::getAlias($model->file_content_uri))) {
            $file_name = Yii::getAlias($model->file_content_uri);
        } elseif (is_resource($model->file_content)){
            $file_name = tempnam('/tmp', 'admin_order_doc_');
            file_put_contents($file_name, $model->file_content);
        } else {
            Yii::$app->session->setFlash('error', 'Document not found to download');
            return $this->redirect(Yii::$app->request->referrer );
        }

        Yii::$app->response->sendFile($file_name, $model->file_name, ['inline' => false]);
        Yii::$app->end();
    }

    /**
     * Returns JSON data list of RPT providers by given service
     * This method used to return data for dependent drop downs
     * @return array
     */
    public function actionListRptProviders()
    {
        $out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $service = Yii::$app->request->post('depdrop_parents', 0);
        if(!empty($service)) {
            $providers = User::rptProviderListWithService($service[0]);
            foreach ($providers as $i => $n) {
                $out[] = ['id' => $i, 'name' => $n];
            }
        }
        return ['output' => $out, 'selected' => ''];
    }

    /**
     * Returns JSON data list of PTA providers by given service
     * This method used to return data for dependent drop downs
     * @return array
     */
    public function actionListPtaProviders()
    {
        $out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $service = Yii::$app->request->post('depdrop_parents', 0);
        if(!empty($service)) {
            $providers = User::ptaProviderListWithService($service[0]);
            foreach ($providers as $i => $n) {
                $out[] = ['id' => $i, 'name' => $n];
            }
        }
        return ['output' => $out, 'selected' => ''];
    }

    /**
     * Returns JSON data list of orders by given provider ID
     * This method used to return data for dependent drop downs
     * @return array
     * @throws InvalidConfigException
     */
    public function actionListProviderOrders()
    {
        $out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post('depdrop_parents', 0);
        if(!empty($post[0]) && is_numeric($post[0])) {
            $userOrders = UserOrder::find()->where(['user_id' => $post[0], 'status' => 'A'])->all();
            foreach ($userOrders as $i => $o) {
                $out[] = ['id' => $o->order->id, 'name' => $o->order->patient_name . ' (SOC: ' . Yii::$app->formatter->asDate($o->order->patient->start_of_care) . ')'];
            }
        }
        return ['output' => $out, 'selected' => ''];
    }

    /**
     * Returns JSON data list of orders by given provider ID
     * This method used to return data for dependent drop downs
     * @return array
     * @throws InvalidConfigException
     */
    public function actionListOrderVisits()
    {
        $out = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post('depdrop_parents', 0);
        if(!empty($post[0]) && !empty($post[1]) && is_numeric($post[1])) {
            $order = Order::find()->joinWith('orderUsers')->where(['id' => $post[1], 'user_order.user_id' => $post[0]])->one();
            $visits = $order->visits;
            foreach ($visits as $i => $v) {
                $out[] = ['id' => $v->id, 'name' => 'Visit at ' . Yii::$app->formatter->asDatetime($v->visited_at)];
            }
        }
        return ['output' => $out, 'selected' => ''];
    }

    /**
     * Finds the Order model based on its primary key value.
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return Order the loaded model
     * @throws RecordNotFoundException
     */
    protected function findModel($id, $pid)
    {
        if (($model = Order::findOne(['id' => $id, 'patient_id' => $pid])) === null) {
            throw new RecordNotFoundException(Yii::t('app', 'Record not found'));
        }
        return $model;
    }

    /**
     * Finds the SubmitOrderForm model based on its primary key value and patient ID.
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return SubmitOrderForm the loaded model
     * @throws RecordNotFoundException
     */
    protected function findSubmitFormModel($id, $pid)
    {
        if (($model = SubmitOrderForm::findOne(['id' => $id, 'patient_id' => $pid])) === null) {
            throw new RecordNotFoundException(Yii::t('app', 'Record not found'));
        }
        $model->setScenario(Order::ORDER_SCENARIO_SUBMIT);
        return $model;
    }

    /**
     * Finds the CompleteOrderForm model based on its primary key value and patient ID.
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return CompleteOrderForm the loaded model
     * @throws RecordNotFoundException
     */
    protected function findCompleteFormModel($id, $pid)
    {
        if (($model = CompleteOrderForm::findOne(['id' => $id, 'patient_id' => $pid])) === null) {
            throw new RecordNotFoundException(Yii::t('app', 'Record not found'));
        }
        $model->setScenario(Order::ORDER_SCENARIO_COMPLETE);
        return $model;
    }

    /**
     * Finds the ApproveFrequencyForm model based on its primary key value and patient ID.
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return ApproveFrequencyForm the loaded model
     * @throws RecordNotFoundException
     */
    protected function findApproveFrequencyFormModel($id, $pid)
    {
        if (($model = ApproveFrequencyForm::findOne(['id' => $id, 'patient_id' => $pid])) === null) {
            throw new RecordNotFoundException(Yii::t('app', 'Record not found'));
        }
        $model->setScenario(Order::ORDER_SCENARIO_APPROVE_FREQUENCY);
        return $model;
    }

    /**
     * Finds the ChangeProviderForm model based on its primary key value and patient ID.
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return ChangeProviderForm the loaded model
     * @throws RecordNotFoundException
     */
    protected function findChangeProviderFormModel($id, $pid)
    {
        if (($model = ChangeProviderForm::findOne(['id' => $id, 'patient_id' => $pid])) === null) {
            throw new RecordNotFoundException(Yii::t('app', 'Record not found'));
        }
        return $model;
    }

    /**
     * Find patient by given id and logged in user ID
     * @param int $id Patient ID
     * @return Patient The loaded model if successful
     * @throws NotFoundHttpException
     */
    protected function findPatient($id)
    {
        if(($patient = Patient::findOne(['id' => $id])) === null) {
            throw new NotFoundHttpException('Patient Record not found');
        }
        return $patient;
    }

}
