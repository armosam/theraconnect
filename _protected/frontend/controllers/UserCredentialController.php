<?php

namespace frontend\controllers;

use Yii;
use Exception;
use Throwable;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\base\ExitException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use common\helpers\ConstHelper;
use common\helpers\ArrayHelper;
use common\models\UserCredential;
use common\models\searches\UserCredentialSearch;

/**
 * UserCredentialController implements the CRUD actions for UserCredential model.
 * Class UserCredentialController
 * @package frontend\controllers
 */
class UserCredentialController extends FrontendController
{
    /**
     * User Credential View action
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'My Credentials'))
            ->setDescription("THERA Connect List My Credentials.")
            ->setKeywords('THERA Connect,list,credentials,user ')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());
        $searchModel = new UserCredentialSearch();
        $searchModel->setAttribute('user_id', Yii::$app->user->id);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if(!empty($dataProvider->models)) {
            $models = [];
            foreach ($dataProvider->models as $model) {
                if (Yii::$app->user->can('manageCredential', ['model' => $model])) {
                    $models[] = $model;
                }
            }
            $dataProvider->setModels($models);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserCredential model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - View', ['name' => $model->credentialType->credential_type_name]))
            ->setDescription("THERA Connect View Credential.")
            ->setKeywords('THERA Connect,view,credential')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Blocked
     * Creates a new UserCredential model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserCredential();
        $model->setScenario(UserCredential::SCENARIO_CREATE);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Add New Credential'))
            ->setDescription("THERA Connect Add New Credential.")
            ->setKeywords('THERA Connect,add,credential')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if ($model->load(Yii::$app->request->post())) {

            $model->setAttribute('status', UserCredential::STATUS_PENDING);
            $model->setAttribute('user_id', Yii::$app->user->id);
            $model->upload_file = UploadedFile::getInstance($model, 'upload_file');

            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Credential created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            Yii::$app->session->setFlash('error', 'New credential not created.');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing UserCredential model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->setScenario(UserCredential::SCENARIO_UPDATE);
        Yii::$app->meta
            ->setTitle(Yii::t('app', '{name} - Update', ['name' => $model->credentialType->credential_type_name]))
            ->setDescription("THERA Connect Update Credential.")
            ->setKeywords('THERA Connect,update,credential')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if ($model->load(Yii::$app->request->post())) {

            $model->setAttribute('status', UserCredential::STATUS_PENDING);
            $model->setAttribute('user_id', Yii::$app->user->id);
            $model->upload_file = UploadedFile::getInstance($model, 'upload_file');

            if($model->save()) {
                Yii::$app->session->setFlash('success', 'Credential updated successfully.');
                return $this->redirect(['index']);
            }

            Yii::$app->session->setFlash('error', 'Credential not updated.');
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Update model in AJAX for simple attribute
     * @return array|string[]
     * @throws NotFoundHttpException
     */
    public function actionUpdateAjax ()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // validate if there is a editable input saved via AJAX
        if (Yii::$app->request->post('hasEditable', false)) {

            $id = (int)Yii::$app->request->post('editableKey', 0);
            $model = $this->findModel($id);
            $model->setScenario(UserCredential::SCENARIO_UPDATE);

            $post = Yii::$app->request->post();
            $index = ArrayHelper::getValue($post, 'editableIndex', 0);
            $attribute = ArrayHelper::getValue($post, 'editableAttribute', '');
            $post['UserCredential'] = $post['UserCredential'][$index] ?: null;

            $output = $post['UserCredential'][$attribute];
            $message = '';

            if ($model->load($post)) {
                try {
                    $model->setAttribute('status', UserCredential::STATUS_PENDING);
                    $model->setAttribute('user_id', Yii::$app->user->id);
                    $model->upload_file = UploadedFile::getInstanceByName("UserCredential[$index][upload_file]");

                    // It will allow to set UPDATE scenario but sets individual attribute to save. Bypasses validation of UPDATE scenario
                    $attributeNames = ($attribute === 'upload_file') ? ['upload_file', 'mime_type', 'file_name', 'file_size', 'file_content_uri'] : [$attribute];

                    if (!$model->save(true, $attributeNames)) {
                        throw new Exception($model->getFirstError($attribute));
                    }

                    $model->refresh();

                    if ($attribute === 'assigned_number') {
                        $output = empty($model->assigned_number) ? '' : '****' . substr($model->assigned_number, -4);
                    } elseif ($attribute === 'expire_date') {
                        $output = empty($model->expire_date) ? '' : Yii::$app->formatter->asDate($model->expire_date);
                    } elseif ($attribute === 'upload_file') {
                        $output = empty($model->file_name) ? '' : Yii::t('app', 'OK');
                    }

                }catch (Exception $e) {
                    $message = $e->getMessage();
                }
            }

            return [
                'output' => $output,
                'message' => $message
            ];
        }
        return ['output' => '', 'message' => ''];
    }

    /**
     * Blocked
     * Deletes an existing UserCredential model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();
            Yii::$app->session->setFlash('success', 'Credential deleted successfully.');
        }catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Credential not deleted.');
        }
        return $this->redirect(['index']);
    }

    /**
     * Returns document resource to download
     * @param int $id
     * @return Response|void
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws ExitException
     */
    public function actionDocument(int $id) {

        $model = $this->findModel($id);

        if(is_file(Yii::getAlias($model->file_content_uri)) && is_readable(Yii::getAlias($model->file_content_uri))) {
            $file_name = Yii::getAlias($model->file_content_uri);
        } elseif (is_resource($model->file_content)){
            $file_name = tempnam('/tmp', 'cred_');
            file_put_contents($file_name, $model->file_content);
        } else {
            Yii::$app->session->setFlash('error', 'Document not found to download');
            return $this->redirect(Yii::$app->request->referrer );
        }

        Yii::$app->response->sendFile($file_name, $model->file_name);
        Yii::$app->end();
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id The user id.
     * @return UserCredential The loaded model.
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = UserCredential::findOne(['id' => $id, 'user_id' => Yii::$app->user->getId()])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }
}