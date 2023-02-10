<?php

namespace frontend\controllers;

use Yii;
use Exception;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\Order;
use common\helpers\ConstHelper;
use common\helpers\ArrayHelper;
use common\models\searches\RequestSearch;
use common\exceptions\OrderAcceptException;

/**
 * It is responsible for displaying search page, listings and detail page.
 * Class SearchController
 * @package frontend\controllers
 */
class SearchController extends FrontendController
{
    /**
     * Displays the search page.
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Available Patients'))
            ->setDescription('Available Patients. We help quickly and efficiently find patients.')
            ->setKeywords(ConstHelper::extractKeyWords('Available Patients. We help quickly and efficiently find patients.'))
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new RequestSearch();
        $searchModel->setPageSize(Yii::$app->params['searchPatientsPageSize']);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single patient data model in view.
     * @param int $id Order ID
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAccept($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Accept Patient {name}', ['name' => $model->patient->patientFullName]) )
            ->setDescription("THERA Connect Accept patient service request")
            ->setKeywords("THERA Connect,search,service,help patient,accept")
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        //For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if($model->load(Yii::$app->request->post())) {
            try {

                $currentUser = User::currentLoggedUser();
                $rpt = ($currentUser->title === User::USER_TITLE_RPT) ? $currentUser->getId() : null;
                $pta = $currentUser->title === User::USER_TITLE_PTA ? $currentUser->getId() : null;

                if (!$model->acceptOrder($rpt, $pta)) {
                    throw new OrderAcceptException('Service request failed to accept and assign.');
                }

                Yii::$app->session->addFlash('success', Yii::t('app', 'Patient successfully accepted and assigned.'));

            } catch (Exception $e) {
                Yii::error(sprintf('Service Request failed to accept: Provider: %s | Patient: %s | Service: %s |  Error: %s', Yii::$app->user->id, $model->patient_id, $model->service_id, $e->getMessage()), 'Frontend-Search-'.__FUNCTION__);
                Yii::$app->session->addFlash('error', Yii::t('app', 'The service request failed to accept. Please contact site administration.'));
            }
            return $this->redirect(['search/index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('accept', [
                'model' => $model
            ]);
        }

        return $this->render('accept', [
            'model' => $model
        ]);
    }

    /**
     * Finds the Order model based on its primary key value.
     * @param int $id Order ID
     * @return Order the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne(['id' => $id, 'status' => [Order::ORDER_STATUS_SUBMITTED, Order::ORDER_STATUS_ACCEPTED]])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Record not found'));
        }
        $model->setScenario(Order::ORDER_SCENARIO_ACCEPT);
        return $model;
    }

}