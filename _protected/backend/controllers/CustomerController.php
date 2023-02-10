<?php

namespace backend\controllers;

use Yii;
use Throwable;
use Exception;
use yii\base\Model;
use yii\db\Expression;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\bootstrap\ActiveForm;
use common\models\User;
use common\rbac\models\Role;
use common\models\UserAvatar;
use common\helpers\ConstHelper;
use common\models\searches\CustomerSearch;
use common\exceptions\AccountTerminationException;

/**
 * CustomerController implements the CRUD actions for User model as Customer.
 */
class CustomerController extends BackendController
{
    /**
     * Lists all Customer models.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Agencies'))
            ->setDescription("THERA Connect Admin Agencies Page.")
            ->setKeywords('THERA Connect,Home,Admin,therapists')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new CustomerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     *
     * @param  integer $id The user id.
     * @return string
     *
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'View Agency {username}', ['username' => $model->agency_name]))
            ->setDescription("THERA Connect Admin agency View Page.")
            ->setKeywords('THERA Connect,Home,Admin,agency,view')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Agency account.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return array|string|Response
     * @throws Exception
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Create Agency'))
            ->setDescription("THERA Connect Admin Create Agency Page.")
            ->setKeywords('THERA Connect,Home,Admin,user,create')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new User(['scenario' => User::SCENARIO_CREATE]);
        $role = new Role(['scenario' => Role::SCENARIO_CREATE, 'item_name' => User::USER_CUSTOMER]);
        $userAvatar = new UserAvatar(['scenario' => UserAvatar::SCENARIO_CREATE]);

        //For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) &&
            $userAvatar->load(Yii::$app->request->post())
        ){

            $model->setPassword($model->password);
            $model->generateAuthKey();

            if(Model::validateMultiple([$model, $role])) {

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // If user status is not activated we need to generate phone number validation code
                    if($model->status === User::USER_STATUS_NOT_ACTIVATED){
                        $model->generateAccountActivationToken();
                    }

                    $model->generatePasswordResetToken();

                    // Creates user record
                    if (!$model->save()) {
                        throw new Exception(Yii::t('app', 'Unable to save User record. Please try later'));
                    }
                    $model->refresh();

                    //uploading and saving avatar photo
                    $model->avatar->upload_file = UploadedFile::getInstance($userAvatar, 'upload_file');
                    if(!$model->avatar->save()){
                        throw new Exception( Yii::t('app', 'Unable to save User Avatar File. Please try later') );
                    }

                    // Creates role record for the new created user
                    $role->setAttribute('user_id', $model->getId());
                    if (!$role->save()) {
                        throw new Exception(Yii::t('app', 'Unable to save User Roles. Please try later'));
                    }

                    Yii::$app->getSession()->setFlash('success', [Yii::t('app', 'User created successfully.')]);

                    $transaction->commit();

                    return $this->redirect(['view', 'id' => $model->id]);

                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Agency failed to create. Error: {error}', ['error' => $e->getMessage()]));
                    Yii::error('Create Customer is failed! User: ' . $model->username . ' by Email: ' . $model->email . ' not created. ' . $e->getMessage(), __CLASS__ . ':' . __FUNCTION__);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'role' => $role,
            'userAvatar' => $userAvatar
        ]);
    }

    /**
     * Updates an existing agency model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id User ID
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update agency {username}', ['username' => $model->agency_name]))
            ->setDescription("THERA Connect Admin Agency Update Page.")
            ->setKeywords('THERA Connect,Home,Admin,agency,update')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model->setScenario(User::SCENARIO_UPDATE);

        if(empty($model->role)) {
            $model->role->setAttributes(['user_id' => $model->getId(), 'item_name' => User::USER_CUSTOMER]);
            $model->role->save();
        }

        // For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $posted_params = Yii::$app->request->post();

            // load user data with role and validate them
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                // only if user entered new password we want to hash and save it
                if ($model->password) {
                    $model->setPassword($model->password);
                }

                $model->removeAccountActivationToken();

                if($model->status === User::USER_STATUS_NOT_ACTIVATED){
                    $model->generateAccountActivationToken();
                }

                // Cleanup termination details
                if(in_array($model->status, [User::USER_STATUS_NOT_ACTIVATED, User::USER_STATUS_INACTIVE, User::USER_STATUS_ACTIVE, User::USER_STATUS_SUSPENDED])){
                    $model->setAttributes([
                        'termination_reason' => null,
                        'terminated_by' => null,
                        'terminated_at' => null,
                    ]);
                }

                switch ($model->status){
                    case User::USER_STATUS_SUSPENDED:
                        // Set termination details if account is suspended
                        $model->setAttributes([
                                'suspension_reason' => User::USER_SUSPENSION_REASON_SUSPENDED_BY_ADMIN,
                                'suspension_by' => Yii::$app->user->id,
                                'suspension_at' => new Expression('NOW()')
                            ]
                        );
                        break;
                    case User::USER_STATUS_TERMINATED:
                        // Set termination details if account is terminated
                        $model->setAttributes([
                                'termination_reason' => User::USER_TERMINATION_REASON_TERMINATED_BY_ADMIN,
                                'terminated_by' => Yii::$app->user->id,
                                'terminated_at' => new Expression('NOW()')
                            ]
                        );
                        break;
                }

                //uploading and saving avatar photo
                $model->avatar->upload_file = UploadedFile::getInstance($model->avatar, 'upload_file');
                if(!$model->avatar->save()){
                    throw new Exception( Yii::t('app', 'Unable to save User Avatar File. Please try later') );
                }

                if(!$model->save()) {
                    throw new Exception(Yii::t('app', 'Unable to save User record. Please try later'));
                }

                $transaction->commit();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Your Changes Saved.'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch(Exception $e){
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', Yii::t('app', 'Agency failed to update. Error: {error}', ['error' => $e->getMessage()]));
            Yii::error('Update Customer is failed! User: ' . $model->username . ' by Email: ' . $model->email . ' not created. ' . $e->getMessage(), __CLASS__ . ':' . __FUNCTION__);
        }

        return $this->render('update', [
            'model' => $model,
            'role' => $model->role,
            'userAvatar' => $model->avatar
        ]);
    }

    /**
     * Sets user account status Active
     * @param int $id User ID
     * @return Response
     */
    public function actionActivate($id)
    {
        try {
            $model = $this->findModel($id);
            $model->activateUserAccount();
            Yii::$app->session->setFlash('success', 'Record activated successfully.');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Record not activated. ' . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Sets user account status Inactive
     * @param int $id User ID
     * @return Response
     */
    public function actionDeactivate(int $id)
    {
        try {
            $model = $this->findModel($id);
            $model->inactivateUserAccount();
            Yii::$app->session->setFlash('success', 'Record deactivated successfully.');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Record not activated. ' . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id The user id.
     * @return Response
     * @throws Throwable
     */
    public function actionDelete($id)
    {
        try {
            $model = $this->findModel($id);
            if (!empty($model->customerPatients)) {
                throw new AccountTerminationException(Yii::t('app', 'This agency has patients. Set inactive instead of deleting.'));
            }
            $model->delete();

            // delete this user's role from auth_assignment table
            if ($role = Role::find()->where(['user_id'=>$id])->one()) {
                $role->delete();
            }
            Yii::$app->session->setFlash('success', 'Record deleted successfully.');
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', 'Record is not deleted. ' . $e->getMessage());
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param  integer $id The user id.
     * @return User The loaded model.
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
