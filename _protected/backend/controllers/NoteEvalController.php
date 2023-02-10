<?php

namespace backend\controllers;

use Yii;
use Exception;
use yii\helpers\Url;
use yii\web\Response;
use yii\db\Expression;
use yii\base\ErrorException;
use yii\web\NotFoundHttpException;
use common\models\Order;
use common\models\NoteEval;
use common\helpers\ConstHelper;
use common\components\PDFToolKit;
use common\exceptions\NoteException;
use common\models\searches\NoteEvalSearch;

/**
 * NoteEvalController implements the CRUD actions for NoteEval model.
 */
class NoteEvalController extends BackendController
{
    /**
     * Lists all NoteEval models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Evaluation Notes'))
            ->setDescription('THERA Connect Evaluation Notes.')
            ->setKeywords('THERA Connect,evaluation notes')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());
        $searchModel = new NoteEvalSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NoteEval model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Evaluation Note'))
            ->setDescription('THERA Connect Evaluation Note.')
            ->setKeywords('THERA Connect,evaluation note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NoteEval model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Add Evaluation Note'))
            ->setDescription('THERA Connect Add Evaluation Note.')
            ->setKeywords('THERA Connect,add evaluation note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new NoteEval();

        if (Yii::$app->request->isAjax) {

            if ($model->load(Yii::$app->request->post())) {

                if(($exists = $model->findExistingModel()) !== null) {
                    Yii::error(sprintf("Evaluation Note by ID %s already exist.", $exists->id), $this->id.'::'.$this->action->id);
                    return $this->renderAjax('message', [
                        'class' => 'alert alert-info',
                        'message' => 'Evaluation Note you want to create already exists in ' . ConstHelper::getNoteStatusList($exists->status) . ' status.',
                        'model' => $exists
                    ]);
                }

                // Check if visit should have eval note (isFirstVisit()) otherwise show message
                if(!$model->visit->isFirstVisit()) {
                    Yii::error('Only first visit could have an Evaluation Note.', $this->id.'::'.$this->action->id);
                    return $this->renderAjax('message', [
                        'class' => 'alert alert-danger',
                        'message' => Yii::t('app','Only first visit could have an Evaluation Note.'),
                    ]);
                }

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {
                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException('Evaluation Note failed to add.');
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Evaluation Note added successfully.'),
                        'container' => '#note-eval-index',
                        'url' => Url::to(['note-eval/index'])
                    ];

                } catch (Exception $e) {

                    Yii::error(sprintf("Evaluation Note for visit %s failed to add.", $model->visit->id), $this->id.'::'.$this->action->id);
                    return [
                        'success' => false,
                        'message' => Yii::t('app', $e->getMessage()),
                        'container' => '#note-eval-index',
                        'url' => Url::to(['note-eval/index'])
                    ];
                }
            }

            return $this->renderAjax('create', [
                'model' => $model
            ]);
        }

        if ($model->load(Yii::$app->request->post())) {

            if(($exists = $model->findExistingModel()) !== null) {
                Yii::error(sprintf("Evaluation Note by ID %s already exist.", $exists->id), $this->id.'::'.$this->action->id);
                Yii::$app->session->setFlash('error', 'Evaluation Note is already exist in status ' . ConstHelper::getNoteStatusList($exists->status));
                return $this->redirect(['index']);
            }

            // Check if visit should have eval note (isFirstVisit()) otherwise show message
            if(!$model->visit->isFirstVisit()) {
                Yii::error('Only first visit could have an Evaluation Note.', $this->id.'::'.$this->action->id);
                Yii::$app->session->setFlash('error', Yii::t('app','Only first visit could have an Evaluation Note.'));
                return $this->redirect(['index']);
            }

            try {
                $model->setExistingAttributes();

                if (!$model->save()) {
                    throw new NoteException('Evaluation Note failed to add.');
                }

                Yii::$app->session->setFlash('success', 'Evaluation Note added successfully.');

            } catch (Exception $e) {
                Yii::error(sprintf("Evaluation Note for visit %s failed to add.", $model->visit->id), $this->id . '::' . $this->action->id);
                Yii::$app->session->addFlash('error', $e->getMessage());
            }

            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model
        ]);
    }

    /**
     * Updates an existing NoteEval model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update Evaluation Note'))
            ->setDescription('THERA Connect Update Evaluation Note.')
            ->setKeywords('THERA Connect,update evaluation note')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = $this->findModel($id);
        $model->setAttribute('frequency', !empty($model->order->service_frequency) && in_array($model->order->frequency_status, [Order::ORDER_FREQUENCY_STATUS_SUBMITTED, Order::ORDER_FREQUENCY_STATUS_APPROVED]) ? $model->order->service_frequency : $model->frequency);

        if (Yii::$app->request->isAjax) {

            if ($model->load(Yii::$app->request->post())) {

                Yii::$app->response->format = Response::FORMAT_JSON;

                try {
                    // Setting existing attributes will prevent wrong updates
                    $model->setExistingAttributes();

                    if (!$model->save()) {
                        throw new NoteException('Evaluation Note failed to update.');
                    }

                    return [
                        'success' => true,
                        'message' => Yii::t('app', 'Evaluation Note updated successfully.'),
                        'container' => '#note-eval-index',
                        'url' => Url::to(['note-eval/index'])
                    ];

                } catch (Exception $e) {
                    Yii::error(sprintf("Evaluation Note by ID %s failed to update.", $model->id), $this->id . '::' . $this->action->id);
                    return [
                        'success' => false,
                        'message' => Yii::t('app', 'Evaluation Note failed to update.'),
                        'container' => '#note-eval-index',
                        'url' => Url::to(['note-eval/index'])
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
                    throw new NoteException('Evaluation Note failed to update.');
                }

                Yii::$app->session->setFlash('success', 'Evaluation Note updated successfully.');

            } catch (Exception $e) {
                Yii::error(sprintf("Evaluation Note by ID %s failed to update.", $model->id), $this->id . '::' . $this->action->id);
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
                throw new ErrorException('Evaluation Note acceptation filed.');
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
                throw new ErrorException('Evaluation Note rejection filed.');
            }
            Yii::$app->session->setFlash('success', 'Note rejected successfully.');
        } catch (Exception $e) {
            Yii::error($e->getMessage(). 'Note ID: '. $id, $this->id.'::'.$this->action->id);
            Yii::$app->session->setFlash('error', 'Error: ' . $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * Deletes an existing NoteEval model.
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
     * Finds the NoteEval model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return NoteEval the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NoteEval::findOne(['id' => $id])) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested record does not exist.'));
        }
        return $model;
    }
}
