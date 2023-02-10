<?php

namespace frontend\controllers;

use Yii;
use Throwable;
use Exception;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\Patient;
use common\helpers\ConstHelper;
use common\exceptions\OrderException;
use common\models\forms\CreatePatientForm;
use common\models\searches\PatientSearch;

/**
 * PatientController implements the CRUD actions for Patient model.
 */
class PatientController extends FrontendController
{
    /**
     * Lists all Patient models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'My Patients'))
            ->setDescription('THERA Connect List My Patients.')
            ->setKeywords('THERA Connect,list,patients')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new PatientSearch();
        $searchModel->setAttribute('customer_id', Yii::$app->user->id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if(!empty($dataProvider->models)) {
            $models = [];
            foreach ($dataProvider->models as $model) {
                if (Yii::$app->user->can('managePatient', ['model' => $model]) && User::currentLoggedUser()->isActiveAccount()) {
                    $models[] = $model;
                }
            }
            $dataProvider->setModels($models);
        }

        if (!User::currentLoggedUser()->isActiveAccount()){
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Your account is not active currently. Please complete you profile data. Your account will be activated soon. {contact_link} if you think this is an issue.', [
                'contact_link' => Html::a(Yii::t('app', 'Contact us'), ['site/contact'])
            ]));
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Patient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - View', ['name' => $model->patientFullName]))
            ->setDescription('THERA Connect View Credential.')
            ->setKeywords('THERA Connect,view,credential')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Patient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Add New Patient'))
            ->setDescription('THERA Connect Add New Patient.')
            ->setKeywords('THERA Connect,add,patient')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new CreatePatientForm();
        $model->setScenario(Patient::SCENARIO_CREATE);
        $model->setAttribute('customer_id', Yii::$app->user->id);

        if ($model->load(Yii::$app->request->post())) {

            try {
                $model->createPatient();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Patient created successfully.'));
                return $this->redirect(['patient/index']);

            } catch (Exception $e) {
                Yii::error(sprintf('New patient not created: Customer: %s Error: %s', Yii::$app->user->id, $e->getMessage()), 'FrontendPatient::'.__FUNCTION__);
                Yii::$app->session->setFlash('error', Yii::t('app', 'New patient failed to create. Please contact site administration.'));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Patient model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Patient::SCENARIO_UPDATE);

        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - Update', ['name' => $model->patientFullName]))
            ->setDescription('THERA Connect Update Credential.')
            ->setKeywords('THERA Connect,update,credential')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if ($model->load(Yii::$app->request->post())) {

            $model->setAttribute('customer_id', Yii::$app->user->id);

            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Patient updated successfully.');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Patient not updated.');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Patient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        try {
            if(!empty($model->activeOrders)) {
                throw new OrderException('There are not completed orders for this patient. Please complete orders first.');
            }
            $model->delete();
            Yii::$app->session->setFlash('success', 'Patient deleted successfully.');
        }catch (Exception $e) {
            Yii::error(sprintf('Customer by id="%s" cannot delete patient by id="%s" as there are active orders for this patient.', Yii::$app->user->id, $id), 'FrontendPatient::'.__FUNCTION__);
            Yii::$app->session->setFlash('error', 'Patient not deleted. '. $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Disable an existing Patient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDisable($id)
    {
        $model = $this->findModel($id);
        try{
            $model->disable();

            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Patient Disabled Successfully'));
        }catch (Exception $e){
            Yii::error(sprintf('Disabling the patient: %s failed. Error: %s', $model->patientFullName, $e->getMessage()), 'FrontendPatient::'. __FUNCTION__);
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Patient Not Disabled'));
        }

        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Enable an existing Patient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEnable($id)
    {
        $model = $this->findModel($id);
        try{
            $model->enable();

            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Patient Enabled Successfully'));
        }catch (Exception $e){
            Yii::error(sprintf('Enabling the patient: %s failed. Error: %s', $model->patientFullName, $e->getMessage()), 'FrontendPatient::'. __FUNCTION__);
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Patient Not Enabled'));
        }

        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Finds the Patient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Patient the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Patient::findOne(['id' => $id, 'customer_id' => Yii::$app->user->id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }
}
