<?php

namespace backend\controllers;

use Yii;
use Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\db\Expression;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;
use common\helpers\ConstHelper;
use common\components\PDFToolKit;
use common\models\NoteCommunication;
use common\exceptions\NoteException;
use common\models\searches\NoteCommunicationSearch;

/**
 * NoteCommunicationController implements the CRUD actions for NoteCommunication model.
 */
class NoteCommunicationController extends BackendController
{
    /**
     * Lists all NoteCommunication models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Communication Notes'))
            ->setDescription('THERA Connect Communication Notes.')
            ->setKeywords('THERA Connect,Communication Notes')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());
        $searchModel = new NoteCommunicationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NoteCommunication model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'View Communication Note'))
            ->setDescription('THERA Connect View Communication Note.')
            ->setKeywords('THERA Connect,Communication Notes')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NoteCommunication model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Add Communication Note'))
            ->setDescription('THERA Connect add communication note.')
            ->setKeywords('THERA Connect,add,communication note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteCommunication();

        if (Yii::$app->request->isAjax) {

            if ($model->load(Yii::$app->request->post())) {

                if(($exists = $model->findExistingModel()) !== null) {
                    Yii::error(sprintf("Communication Note by ID %s already exist.", $exists->id), $this->id.'::'.$this->action->id);
                    return $this->renderAjax('message', [
                        'class' => 'alert alert-info',
                        'message' => 'Communication Note you want to create already exists in ' . ConstHelper::getNoteStatusList($exists->status) . ' status.',
                        'model' => $exists
                    ]);
                }

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {
                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException(Yii::t('app', 'Communication Note failed to add.'));
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', Yii::t('app', 'Communication Note added successfully.')),
                        'container' => '#note-communication-index',
                        'url' => Url::to(['note-communication/index'])
                    ];

                } catch (Exception $e) {

                    Yii::error(sprintf("Communication Note for visit %s failed to add.", $model->visit->id), $this->id.'::'.$this->action->id);
                    return [
                        'success' => false,
                        'message' => Yii::t('app', $e->getMessage()),
                        'container' => '#note-communication-index',
                        'url' => Url::to(['note-communication/index'])
                    ];
                }
            }

            return $this->renderAjax('create', [
                'model' => $model
            ]);
        }

        if ($model->load(Yii::$app->request->post())) {

            if(($exists = $model->findExistingModel()) !== null) {
                Yii::error(sprintf("Communication Note by ID %s already exist.", $exists->id), $this->id.'::'.$this->action->id);
                Yii::$app->session->setFlash('error', 'Communication Note is already exist in status ' . ConstHelper::getNoteStatusList($exists->status));
                return $this->redirect(['index']);
            }

            try {
                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException(Yii::t('app', 'Communication Note failed to add.'));
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Communication Note added successfully.'));

            } catch (Exception $e) {
                Yii::error(sprintf("Communication Note for visit %s failed to add.", $model->visit->id), $this->id . '::' . $this->action->id);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Update Communication Note
     * @param int $id NoteCommunication ID
     * @return array|string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update Communication Note'))
            ->setDescription('THERA Connect update communication note.')
            ->setKeywords('THERA Connect,update,communication note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = $this->findModel($id);

        if (Yii::$app->request->isAjax) {

            if ($model->load(Yii::$app->request->post())) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {
                    // Setting existing attributes will prevent wrong updates
                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException('Communication Note failed to update.');
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Communication Note updated successfully.'),
                        'container' => '#note-communication-index',
                        'url' => Url::to(['note-communication/index'])
                    ];

                } catch (Exception $e) {
                    Yii::error(sprintf("Communication Note by ID %s failed to update.", $model->id), $this->id . '::' . $this->action->id);
                    return [
                        'success' => false,
                        'message' => Yii::t('app', 'Communication Note failed to update.'),
                        'container' => '#note-communication-index',
                        'url' => Url::to(['note-communication/index'])
                    ];
                }
            }

            return $this->renderAjax('update', [
                'model' => $model
            ]);
        }

        if ($model->load(Yii::$app->request->post())) {

            try {
                // Setting existing attributes will prevent wrong updates
                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException('Communication Note failed to update.');
                }

                Yii::$app->session->setFlash('success', 'Communication Note updated successfully.');

            } catch (Exception $e) {
                Yii::error(sprintf("Communication Note by ID %s failed to update.", $model->id), $this->id . '::' . $this->action->id);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['index']);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

    /**
     * Accept a note.
     * @param int $id Note ID
     * @return mixed
     */
    public function actionAccept($id)
    {
        try {
            $model = $this->findModel($id);
            $model->setAttributes([
                'status' => ConstHelper::NOTE_STATUS_ACCEPTED,
                'accepted_at' => new Expression('NOW()'),
                'accepted_by' => Yii::$app->user->id
            ]);
            if(!$model->save()) {
                throw new ErrorException('Communication Note acceptation filed.');
            }
            Yii::$app->session->setFlash('success', 'Note accepted successfully.');
        } catch (Exception $e) {
            Yii::error($e->getMessage(). 'Note ID: '. $id, $this->id.'::'.$this->action->id);
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Reject a note.
     * @param int $id Note ID
     * @return mixed
     */
    public function actionReject($id)
    {
        try {
            $model = $this->findModel($id);
            $model->setAttributes([
                'status' => ConstHelper::NOTE_STATUS_PENDING,
                'accepted_at' => new Expression('NULL'),
                'accepted_by' => new Expression('NULL'),
                'submitted_at' => new Expression('NULL'),
                'submitted_by' => new Expression('NULL')
            ]);
            if(!$model->save()) {
                throw new ErrorException('Communication Note rejection filed.');
            }
            Yii::$app->session->setFlash('success', 'Note rejected successfully.');
        } catch (Exception $e) {
            Yii::error($e->getMessage(). 'Note ID: '. $id, $this->id.'::'.$this->action->id);
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing NoteCommunication model.
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
     * Finds the NoteCommunication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NoteCommunication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NoteCommunication::findOne(['id' => $id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested record does not exist.'));
        }
        return $model;
    }
}
