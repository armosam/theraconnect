<?php

namespace backend\controllers;

use Yii;
use Exception;
use common\models\User;
use common\models\Patient;
use common\helpers\ConstHelper;
use yii\web\NotFoundHttpException;
use common\models\forms\CreatePatientForm;
use common\models\searches\PatientSearch;

/**
 * PatientController implements the CRUD actions for Patient model.
 */
class PatientController extends BackendController
{
    /**
     * Lists all Patient models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Patients List'))
            ->setDescription("THERA Connect List Patients.")
            ->setKeywords('THERA Connect,list,patients')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new PatientSearch();
        $uid = Yii::$app->request->get('uid', null);
        if($uid !== null) {
            $user = User::findOne($uid);
            $searchModel->customer_id = $user->id ?? null;
        }
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Patient model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - View', ['name' => $model->patientFullName]))
            ->setDescription("THERA Connect View Patient.")
            ->setKeywords('THERA Connect,view,patient')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
            'uid' => Yii::$app->request->get('uid', null)
        ]);
    }

    /**
     * Creates a new Patient model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws Exception
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Add New Patient'))
            ->setDescription("THERA Connect Add New Patient.")
            ->setKeywords('THERA Connect,add,patient')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new CreatePatientForm();
        $model->setScenario(Patient::SCENARIO_CREATE);
        $uid = Yii::$app->request->get('uid', null);

        if ($model->load(Yii::$app->request->post())) {

            try {
                $model->createPatient();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Patient created successfully.'));
                return $this->redirect(['view', 'id' => $model->id, 'uid' => $uid]);

            } catch (Exception $e) {
                Yii::error(sprintf('New patient not created: Customer: %s Error: %s', Yii::$app->user->id, $e->getMessage()), 'Frontend-Patient-'.__FUNCTION__);
                Yii::$app->session->setFlash('error', Yii::t('app', 'New patient failed to create. Please contact site administration.'));
            }
        }
        return $this->render('create', [
            'model' => $model,
            'uid' => $uid
        ]);
    }

    /**
     * Updates an existing Patient model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(Patient::SCENARIO_UPDATE);
        $uid = Yii::$app->request->get('uid', null);

        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - Update', ['name' => $model->patientFullName]))
            ->setDescription("THERA Connect Update Patient.")
            ->setKeywords('THERA Connect,update,patient')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if ($model->load(Yii::$app->request->post())) {

            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Patient updated successfully.');
                return $this->redirect(['index', 'uid' => $uid]);
            }

            Yii::$app->session->setFlash('error', 'Patient not updated.');
        }
        return $this->render('update', [
            'model' => $model,
            'uid' => $uid
        ]);
    }

    /**
     * Deletes an existing Patient model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $uid = Yii::$app->request->get('uid', null);
        try {
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('success', 'Patient deleted successfully.');
        }catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Patient failed to delete.');
        }
        return $this->redirect(['index', 'uid' => $uid]);
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
            Yii::error(sprintf('%s.%s: for patient: %s failed. Error: %s', __CLASS__, __FUNCTION__, $model->patientFullName, $e->getMessage() ));
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
            Yii::error(sprintf('%s.%s: for patient: %s failed. Error: %s', __CLASS__, __METHOD__, $model->patientFullName, $e->getMessage() ));
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Patient Not Enabled'));
        }

        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Finds the Patient model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Patient the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Patient::findOne(['id' => $id])) === null) {
            throw new NotFoundHttpException('Record not found');
        }
        return $model;
    }
}
