<?php

namespace frontend\controllers;

use Yii;
use Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\base\ExitException;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use common\models\Order;
use common\models\Patient;
use common\helpers\ConstHelper;
use common\models\OrderDocument;
use common\exceptions\OrderException;
use common\models\forms\CreateOrderForm;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends FrontendController
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
     * @param $pid
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

        if($model->load(Yii::$app->request->post())) {
            try {
                $model->createOrder();

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
                return $this->redirect(['patient/index']);

            } catch (Exception $e) {
                Yii::error(sprintf('Service Request failed to create: Customer: %s | Patient: %s |  Error: %s', Yii::$app->user->id, $pid, $e->getMessage()), 'FrontendOrder::'.__FUNCTION__);
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
                return $this->redirect(['patient/index']);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('create', [
                'model' => $model
            ]);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Update an Order model.
     * If update is successful, the browser will update order grid.
     * @param int $id
     * @param int $pid
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id, $pid)
    {
        $patient = $this->findPatient($pid);
        $model = $this->findModel($id, $patient->id);

        if (!Yii::$app->user->can('manageOrder', ['model' => $model])) {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You do not have access to modify this service request.',
                ]);
            }
            Yii::$app->session->addFlash('error', Yii::t('app', 'Sorry You do not have access to modify this service request.'));
            return $this->redirect(['patient/index']);
        }

        if($model->status !== Order::ORDER_STATUS_PENDING) {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('message', [
                    'class' => 'alert alert-danger',
                    'message' => 'Sorry You cannot modify already submitted or accepted service requests.',
                ]);
            }
            Yii::$app->session->addFlash('error', Yii::t('app', 'Sorry You cannot modify already submitted or accepted service requests.'));
            return $this->redirect(['patient/index']);
        }

        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update Service Request for {name}', ['name' => $patient->patientFullName]))
            ->setDescription("THERA Connect Update Service Request.")
            ->setKeywords('THERA Connect,update,Service,Request')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if($model->load(Yii::$app->request->post())) {

            try {
                $model->intake_file = UploadedFile::getInstance($model, 'intake_file');
                $model->form_485_file = UploadedFile::getInstance($model, 'form_485_file');
                $model->other_file = UploadedFile::getInstance($model, 'other_file');

                $model->setAttributes([
                    'service_name' => $model->service->service_name,
                    'frequency_status' => empty($model->service_frequency) ? null : Order::ORDER_FREQUENCY_STATUS_APPROVED,
                ]);

                if (!$model->save()) {
                    throw new OrderException('Record not saved in the database.');
                }

                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Service request updated successfully.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('success', Yii::t('app', 'Service request updated successfully.'));
                return $this->redirect(['patient/index']);

            } catch (Exception $e) {
                Yii::error(sprintf('Service Request failed to update: Customer: %s | Patient: %s | Order: %s |  Error: %s', Yii::$app->user->id, $pid, $id, $e->getMessage()), 'FrontendOrder::'.__FUNCTION__);
                if (Yii::$app->request->isAjax) {
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return [
                        'success' => false,
                        'message' => Yii::t('app', 'Service request failed to update.'),
                        'container' => '#order-subgrid-' . $patient->id,
                        'url' => Url::to(['order/index', 'pid' => $patient->id])
                    ];
                }
                Yii::$app->session->addFlash('error', Yii::t('app', 'Service request failed to update. Please contact site administration.'));
                return $this->redirect(['patient/index']);
            }
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('update', [
                'model' => $model
            ]);
        }

        return $this->render('update', [
            'model' => $model
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
                ->setTitle(Yii::t('app', 'Service Request {order} for {name}', ['order' => $model->order_number, 'name' => $patient->patientFullName]))
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
     * Returns order document resource to download for customer
     * @param int $id
     * @return Response|void
     * @throws NotFoundHttpException
     * @throws ExitException
     */
    public function actionDocument(int $id)
    {
        if (($model = OrderDocument::find()->joinWith(['order', 'patient'])->where(['[[order_document.id]]' => $id, '[[patient.customer_id]]' => Yii::$app->user->id])->one()) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested document not found.'));
        }

        if(is_file(Yii::getAlias($model->file_content_uri)) && is_readable(Yii::getAlias($model->file_content_uri))) {
            $file_name = Yii::getAlias($model->file_content_uri);
        } elseif (is_resource($model->file_content)){
            $file_name = tempnam('/tmp', 'order_doc_');
            file_put_contents($file_name, $model->file_content);
        } else {
            Yii::$app->session->setFlash('error', 'Document not found to download');
            return $this->redirect(Yii::$app->request->referrer );
        }

        Yii::$app->response->sendFile($file_name, $model->file_name);
        Yii::$app->end();
    }

    /**
     * Finds the Order model based on its primary key value.
     * @param int $id Order ID
     * @param int $pid Patient ID
     * @return Order the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $pid)
    {
        if (($model = Order::findOne(['id' => $id, 'patient_id' => $pid])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
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
        if(($patient = Patient::findOne(['id' => $id, 'customer_id' => Yii::$app->user->id])) === null) {
            throw new NotFoundHttpException('Record not found');
        }
        return $patient;
    }

}
