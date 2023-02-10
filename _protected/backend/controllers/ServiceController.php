<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\models\Service;
use common\helpers\ConstHelper;
use common\models\searches\ServiceSearch;

/**
 * ServiceController implements the CRUD actions for Service model.
 */
class ServiceController extends BackendController
{
    /**
     * Lists all Service models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Services'))
            ->setDescription("THERA Connect List of Services.")
            ->setKeywords('THERA Connect,service,help pregnant,pregnancycertified')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new ServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Displays a single Service model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Service'). ' - '. $model->service_name )
            ->setDescription("THERA Connect View Service {$model->service_name}")
            ->setKeywords('THERA Connect,service,help pregnant,pregnancycertified')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * Creates a new Service model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Create Service'))
            ->setDescription("THERA Connect Create New Service.")
            ->setKeywords('THERA Connect,new,service,help pregnant,pregnancycertified')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new Service();

        if ($model->load(Yii::$app->request->post())) {
            if (!$model->save()) {
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Service not created.'));
                return $this->render('create', ['model' => $model]);
            }

            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Service created successfully.'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Service model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update Service'). ' - '. $model->service_name )
            ->setDescription("THERA Connect Update Service: {$model->service_name}")
            ->setKeywords('THERA Connect,service,help pregnant,pregnancycertified')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if ($model->load(Yii::$app->request->post())) {
            if(!$model->save()){
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Your changes not saved.'));
                return $this->render('update', ['model' => $model]);
            }

            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Your changes saved successfully.'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Deletes an existing Service model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Delete Service'). ' - '. $model->service_name )
            ->setDescription("THERA Connect Delete Service: {$model->service_name}")
            ->setKeywords('THERA Connect,delete,service,help pregnant,pregnancycertified')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        //$model->delete();
        Yii::$app->session->setFlash('error', 'You are not allowed to delete services. You can only disable.');
        return $this->redirect(['index']);
    }

    /**
     * Disable an existing Service model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDisable($id)
    {
        $model = $this->findModel($id);
        try{
            $model->disable();

            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Service Disabled Successfully'));
        }catch (\Exception $e){
            Yii::error(sprintf('%s.%s: for service: %s - %s', __CLASS__, __METHOD__, $model->service_name, $e->getMessage() ));
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Service Not Disabled'));
        }

        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Enable an existing Service model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionEnable($id)
    {
        $model = $this->findModel($id);
        try{
            $model->enable();

            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Service Enabled Successfully'));
        }catch (\Exception $e){
            Yii::error(sprintf('%s.%s: for service: %s - %s', __CLASS__, __METHOD__, $model->service_name, $e->getMessage() ));
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Service Not Enabled'));
        }

        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Finds the Service model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Service the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Service::findOne(['id' => $id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested record does not exist.'));
        }
        return $model;
    }
}
