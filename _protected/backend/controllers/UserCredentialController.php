<?php

namespace backend\controllers;

use Yii;
use Exception;
use Throwable;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\base\ExitException;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\helpers\ConstHelper;
use common\helpers\ArrayHelper;
use common\models\UserCredential;
use common\exceptions\RecordNotFoundException;
use common\exceptions\UserNotFoundException;
use common\models\searches\UserCredentialSearch;

/**
 * UserCredentialController implements the CRUD actions for UserCredential model.
 * Class UserCredentialController
 * @package frontend\controllers
 */
class UserCredentialController extends BackendController
{
    /**
     * User Credential Index action
     * @param int $uid
     * @return string
     */
    public function actionIndex($uid)
    {
        try {
            $user = $this->findUser($uid);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Required Credentials for {user}', ['user' => $user->getUserFullName()]))
                ->setDescription("THERA Connect List User Credentials.")
                ->setKeywords('THERA Connect,list,credentials')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

            $searchModel = new UserCredentialSearch();
            $searchModel->setAttribute('user_id', $user->id);
            $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['user/index']);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserCredential model.
     * @param integer $id
     * @param integer $uid
     * @return mixed
     */
    public function actionView($id, $uid)
    {
        try {
            $user = $this->findUser($uid);
            $model = $this->findModel($id, $user->id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', '{name} for {user}', ['name' => $model->credentialType->credential_type_name, 'user' => $user->getUserFullName()]))
                ->setDescription("THERA Connect View Credentials.")
                ->setKeywords('THERA Connect,view,credentials')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

        } catch (RecordNotFoundException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index', 'uid' => $uid]);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['user/index']);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Blocked
     * Creates a new UserCredential model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param int $uid User ID
     * @return mixed
     */
    public function actionCreate($uid)
    {
        try {
            $user = $this->findUser($uid);
            $model = new UserCredential();
            $model->setScenario(UserCredential::SCENARIO_CREATE);
            $model->setAttribute('user_id', $user->id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Add New Credential for {user}', ['user' => $user->getUserFullName()]))
                ->setDescription("THERA Connect Add New Credential.")
                ->setKeywords('THERA Connect,add,credential')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

            if ($model->load(Yii::$app->request->post())) {
                $model->setAttribute('status', UserCredential::STATUS_PENDING);
                $model->upload_file = UploadedFile::getInstance($model, 'upload_file');

                if($model->save()) {
                    Yii::$app->session->setFlash('success', 'Credential created successfully.');
                    return $this->redirect(['view', 'uid' => $model->user_id, 'id' => $model->id]);
                }

                Yii::$app->session->setFlash('error', 'Credential not updated.');
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Credential not created. ' . $e->getMessage());
            return $this->redirect(['user/index']);
        }
    }

    /**
     * Updates an existing UserCredential model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @param integer $uid
     * @return mixed
     */
    public function actionUpdate($id, $uid)
    {
        try {
            $user = $this->findUser($uid);
            $model = $this->findModel($id, $user->id);
            $model->setScenario(UserCredential::SCENARIO_UPDATE);

            Yii::$app->meta
                ->setTitle(Yii::t('app', 'Update {name} for {user}', ['name' => $model->credentialType->credential_type_name, 'user' => $user->getUserFullName()]))
                ->setDescription("THERA Connect Update Credential.")
                ->setKeywords('THERA Connect,update,credential')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

            if ($model->load(Yii::$app->request->post())) {

                $model->setAttribute('status', UserCredential::STATUS_PENDING);
                $model->upload_file = UploadedFile::getInstance($model, 'upload_file');

                if($model->save()) {
                    Yii::$app->session->setFlash('success', 'Credential updated successfully.');
                    return $this->redirect(['index', 'uid' => $model->user_id]);
                }

                Yii::$app->session->setFlash('error', 'Credential not updated.');
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Credential not updated. ' . $e->getMessage());
            return $this->redirect(['user/index']);
        }
    }

    /**
     * Update model in AJAX for simple attribute
     * @param $uid
     * @return array|string[]
     * @throws NotFoundHttpException
     * @throws UserNotFoundException
     */
    public function actionUpdateAjax ($uid)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // validate if there is a editable input saved via AJAX
        if (Yii::$app->request->post('hasEditable', false)) {

            $id = (int)Yii::$app->request->post('editableKey', 0);
            $user = $this->findUser($uid);
            $model = $this->findModel($id, $user->id);
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
     * @param int $id Record ID
     * @param int $uid User ID
     * @return mixed
     * @throws Throwable
     */
    public function actionDelete($id, $uid)
    {
        try {
            $user = $this->findUser($uid);
            $this->findModel($id, $user->id)->delete();
            Yii::$app->session->setFlash('success', 'Record deleted successfully.');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index', 'uid' => $uid]);
    }

    /**
     * Approve an existing credential.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id Record ID
     * @param int $uid User ID
     * @return mixed
     */
    public function actionApprove($id, $uid)
    {
        try {
            $user = $this->findUser($uid);
            $this->findModel($id, $user->id)->approve();
            Yii::$app->session->setFlash('success', 'Record approved successfully.');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Record not approved. ' . $e->getMessage());
        }
        return $this->redirect(['index', 'uid' => $uid]);
    }

    /**
     * Disapprove an existing credential.
     * @param int $id Record ID
     * @param int $uid User ID
     * @return mixed
     */
    public function actionDisapprove($id, $uid)
    {
        try {
            $user = $this->findUser($uid);
            $this->findModel($id, $user->id)->disapprove();
            Yii::$app->session->setFlash('success', 'Record disapproved successfully.');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Record not disapproved. ' . $e->getMessage());
        }
        return $this->redirect(['index', 'uid' => $uid]);
    }


    /**
     * Returns document resource to download
     * @param int $id
     * @param int $uid
     * @return Response|void
     * @throws ExitException
     * @throws NotFoundHttpException
     */
    public function actionDocument($id, $uid)
    {
        $model = $this->findModel($id, $uid);

        if(is_file(Yii::getAlias($model->file_content_uri)) && is_readable(Yii::getAlias($model->file_content_uri))) {
            $file_name = Yii::getAlias($model->file_content_uri);
        } elseif (is_resource($model->file_content)){
            $file_name = tempnam('/tmp', 'admin_cred_');
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
     * @param integer $id The user-credential id.
     * @param integer $uid The user id.
     * @return UserCredential|Response The loaded UserCredential model if successful.
     * @throws NotFoundHttpException
     */
    protected function findModel($id, $uid)
    {
        if (($model = UserCredential::findOne(['user_id' => $uid, 'id' => $id])) === null) {
            throw new NotFoundHttpException('Record not found');
        }
        return $model;
    }

    /**
     * Find user by given id and check access
     * @param int $id User ID
     * @return User|Response The loaded model if successful
     * @throws UserNotFoundException
     */
    protected function findUser($id)
    {
        $user = User::findOne($id);
        if($user === null || $user->role->item_name !== User::USER_PROVIDER){
            throw new UserNotFoundException('Record not found');
        }
        return $user;
    }
}