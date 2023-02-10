<?php

namespace backend\controllers;

use Yii;
use Exception;
use yii\db\Expression;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\Prospect;
use common\helpers\ConstHelper;
use common\models\UserCredential;
use common\rbac\helpers\RbacHelper;
use common\exceptions\UserSignUpException;
use common\models\searches\ProspectSearch;

/**
 * ProspectController implements the CRUD actions for Prospect model.
 */
class ProspectController extends BackendController
{
    /**
     * Lists all Prospect models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'New Applications'))
            ->setDescription("THERA Connect New Application.")
            ->setKeywords('THERA Connect,new,,join,application')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new ProspectSearch(['status' => Prospect::PROSPECTIVE_STATUS_PENDING]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Prospect model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Application for {name}', ['name' => $model->getProspectFullName()]))
            ->setDescription("THERA Connect View Application.")
            ->setKeywords('THERA Connect,view,join,application')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Prospect model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Add Application'))
            ->setDescription("THERA Connect Add a new application.")
            ->setKeywords('THERA Connect,add,join,application')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new Prospect();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Prospect model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update application for {name}', ['name' => $model->getProspectFullName()]))
            ->setDescription("THERA Connect Update application.")
            ->setKeywords('THERA Connect,update,join,application')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Accept an application and create user.
     * @param int $id application ID
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionAccept($id)
    {
        $model = $this->findModel($id);
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model->setAttributes([
                'status' => Prospect::PROSPECTIVE_STATUS_ACCEPTED,
                'accepted_by' => Yii::$app->user->id,
                'accepted_at' => new Expression('NOW()'),
            ]);
            if(!$model->save()) {
                throw new ErrorException(Yii::t('app', 'Application failed to accept. {error}', ['error' => implode(', ', array_values($model->getFirstErrors()))]));
            }

            $user = new User();
            $user->setScenario(User::SCENARIO_CREATE);
            $user->setAttributes([
                'username' => ConstHelper::calculateUsernameFromEmailAddress($model->email),
                'email' => $model->email,
                'first_name' => $model->first_name,
                'last_name' => $model->last_name,
                'phone1' => $model->phone_number,
                'title' => $model->license_type,
                'address' => $model->address,
                'city' => $model->city,
                'state' => $model->state,
                'zip_code' => $model->zip_code,
                'country' => $model->country,
                'ip_address' => $model->ip_address,
                'timezone' => $model->timezone,
                'lat' => $model->lat,
                'lng' => $model->lng,
                'language' => $model->language,
                'covered_county' => $model->covered_county,
                'covered_city' => $model->covered_city,
                'status' => User::USER_STATUS_NOT_ACTIVATED,
                'note' => $model->note,
            ]);

            $user->setPassword('THERA123');
            $user->generateAuthKey();
            $user->generateAccountActivationToken();
            $user->generatePasswordResetToken();

            if (!($user->save() && RbacHelper::assignRole($user->getId(), User::USER_PROVIDER))) {
                throw new UserSignUpException(Yii::t('app', 'Account failed to create. {error}', ['error' => implode(', ', array_values($user->getFirstErrors()))]));
            }

            if ($user->role->item_name === User::USER_PROVIDER) {
                UserCredential::assignRequiredCredentials($user->getId());
            }

            $user->setService($model->service_id);

            Yii::$app->session->setFlash('success', 'Application accepted and account created successfully.');
            $transaction->commit();
        } catch (Exception $e) {
            Yii::error($e->getMessage(). 'Application ID: '. $id, $this->id.'::'.$this->action->id);
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
            $transaction->rollBack();
        }
        return $this->redirect(['index']);
    }

    /**
     * Reject an application
     * @param int $id Application ID
     * @return mixed
     */
    public function actionReject($id)
    {
        try {
            $model = $this->findModel($id);
            $model->setAttributes([
                'status' => Prospect::PROSPECTIVE_STATUS_REJECTED,
                'accepted_by' => Yii::$app->user->id,
                'accepted_at' => new Expression('NULL'),
                'rejected_by' => Yii::$app->user->id,
                'rejected_at' => new Expression('NOW()'),
                'rejection_reason' => Prospect::PERSPECTIVE_REJECTION_REASON_NOT_DOCUMENTED
            ]);
            if(!$model->save()) {
                throw new ErrorException('Application rejection filed.');
            }
            Yii::$app->session->setFlash('success', 'Application rejected successfully.');
        } catch (Exception $e) {
            Yii::error($e->getMessage(). 'Application ID: '. $id, $this->id.'::'.$this->action->id);
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing Prospect model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Prospect model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Prospect the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Prospect::findOne(['id' => $id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested record does not exist.'));
        }
        return $model;
    }
}
