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
use common\models\UserService;
use common\helpers\ConstHelper;
use common\models\UserCredential;
use common\models\searches\ProviderSearch;
use common\exceptions\AccountTerminationException;

/**
 * ProviderController implements the CRUD actions for User model as Provider.
 */
class ProviderController extends BackendController
{
    /**
     * Lists all Provider models.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Therapists'))
            ->setDescription("THERA Connect Admin Therapists Page.")
            ->setKeywords('THERA Connect,Home,Admin,therapists')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new ProviderSearch();
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
            ->setTitle(Yii::t('app', 'View therapist {username}', ['username' => $model->username]))
            ->setDescription("THERA Connect Admin therapist View Page.")
            ->setKeywords('THERA Connect,Home,Admin,therapist,view')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Therapist account.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return array|string|Response
     * @throws Exception
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Create Therapist'))
            ->setDescription("THERA Connect Admin Create Therapist Page.")
            ->setKeywords('THERA Connect,Home,Admin,user,create')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new User(['scenario' => User::SCENARIO_CREATE]);
        $role = new Role(['scenario' => Role::SCENARIO_CREATE, 'item_name' => User::USER_PROVIDER]);
        $userService = new UserService(['scenario' => UserService::SCENARIO_DEFAULT]);
        $userAvatar = new UserAvatar(['scenario' => UserAvatar::SCENARIO_CREATE]);

        //For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()) &&
            $userAvatar->load(Yii::$app->request->post()) &&
            $userService->load(Yii::$app->request->post())
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
                        throw new Exception(Yii::t('app', 'Unable to save User. Please try later'));
                    }
                    $model->refresh();

                    $model->avatar->upload_file = UploadedFile::getInstance($userAvatar, 'upload_file');
                    if(!$model->avatar->save()){
                        throw new Exception( Yii::t('app', 'Unable to save User Avatar File. Please try later') );
                    }

                    $userService->setAttribute('user_id', $model->getId());
                    if(!$userService->save()){
                        throw new Exception( Yii::t('app', 'Unable to save User Avatar File. Please try later') );
                    }

                    // Creates role record for the new created user
                    $role->setAttribute('user_id', $model->getId());
                    if (!$role->save()) {
                        throw new Exception(Yii::t('app', 'Unable to save User Roles. Please try later'));
                    }

                    // Creates credential records for the new created user
                    UserCredential::assignRequiredCredentials($model->getId());

                    Yii::$app->getSession()->setFlash('success', [Yii::t('app', 'User created successfully.')]);

                    $transaction->commit();

                    return $this->redirect(['view', 'id' => $model->id]);

                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Therapist failed to create. Error: {error}', ['error' => $e->getMessage()]));
                    Yii::error('Create Provider is failed! User: ' . $model->username . ' by Email: ' . $model->email . ' not created. ' . $e->getMessage(), __CLASS__ . ':' . __FUNCTION__);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'role' => $role,
            'userService' => $userService,
            'userAvatar' => $userAvatar
        ]);
    }

    /**
     * Updates an existing therapist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id User ID
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        // get user object
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update therapist {username}', ['username' => $model->username]))
            ->setDescription("THERA Connect Admin Therapist Update Page.")
            ->setKeywords('THERA Connect,Home,Admin,therapist,update')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model->setScenario(User::SCENARIO_UPDATE);

        $userService = $model->userService;
        if(empty($userService)) {
            $userService = new UserService(['user_id' => $model->getId()]);
        }
        if(empty($model->role)) {
            $model->role->setAttributes(['user_id' => $model->getId(), 'item_name' => User::USER_PROVIDER]);
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
            if ($model->load($posted_params) &&
                $userService->load($posted_params) &&
                Model::validateMultiple([$model, $userService])
            ) {
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

                if(!$userService->save()){
                    throw new Exception( Yii::t('app', 'Unable to save User Service. Please try later') );
                }

                $model->save();

                $transaction->commit();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Your Changes Saved.'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }catch(Exception $e){
            $transaction->rollBack();
            Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Therapist failed to update. Error: {error}', ['error' => $e->getMessage()]));
            Yii::error('Update Provider is failed! User: ' . $model->username . ' by Email: ' . $model->email . ' not updated. ' . $e->getMessage(), __CLASS__ . ':' . __FUNCTION__);
        }

        return $this->render('update', [
            'model' => $model,
            'role' => $model->role,
            'userService' => $userService,
            'userAvatar' => $model->avatar
        ]);
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
            if (!empty($model->providerOrders)) {
                throw new AccountTerminationException(Yii::t('app', 'This therapist has orders. Set inactive instead of deleting.'));
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
     * Sets User Service
     * @param int $id Service ID
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionSetService($id)
    {
        $uid = Yii::$app->request->get('uid', null);
        $model = $this->findModel($uid);

        try {
            $model->setService($id);

            Yii::$app->session->addFlash('success', Yii::t('app', 'Service assigned to the user successfully.'));

        } catch (Exception $e) {
            Yii::error(Yii::t('app', 'User Service {service} failed to assign to the user {user}', ['service' => $id, 'user' => $uid]), __CLASS__ . ':' . __FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->redirect(Yii::$app->request->getReferrer());
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
