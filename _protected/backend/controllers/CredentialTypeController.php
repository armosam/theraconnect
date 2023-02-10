<?php

namespace backend\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ConstHelper;
use common\models\base\CredentialType;
use common\models\searches\CredentialTypeSearch;

/**
 * CredentialTypeController implements the CRUD actions for CredentialType model.
 */
class CredentialTypeController extends BackendController
{
    /**
     * Lists all CredentialType models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Credential Types'))
            ->setDescription("THERA Connect Credential Types.")
            ->setKeywords('THERA,Connect,Credential,Type.')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new CredentialTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CredentialType model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - View', ['name' => $model->credential_type_name]))
            ->setDescription("THERA Connect View Credential Type.")
            ->setKeywords('THERA,Connect,Credential,Type.')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new CredentialType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Create Credential Type'))
            ->setDescription("THERA Connect Create Credential Type.")
            ->setKeywords('THERA,Connect,Create,Credential,Type.')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new CredentialType();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CredentialType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - Update', ['name' => $model->credential_type_name]))
            ->setDescription("THERA Connect Update Credential Type.")
            ->setKeywords('THERA,Connect,Update,Credential,Type.')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing CredentialType model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the CredentialType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CredentialType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CredentialType::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested record does not exist.');
    }
}
