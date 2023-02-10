<?php

namespace frontend\controllers;

use Yii;
use Exception;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\MethodNotAllowedHttpException;
use common\helpers\ConstHelper;
use common\models\User;

/**
 * Class UserNotificationController
 * @package frontend\controllers
 */
class UserNotificationController extends FrontendController
{
    /**
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'My Notifications'))
            ->setDescription("View my profile.")
            ->setKeywords('My profile,notification,notification preferences,avatar,my info,my data,my personal information')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        /** @var User $model */
        $model = $this->findModel();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * @return string|Response
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update Notifications'))
            ->setDescription("View my profile.")
            ->setKeywords('My profile,notification,notification preferences,avatar,my info,my data,my personal information')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        /**@var User $model */
        $model = $this->findModel();
        $model->setScenario(User::SCENARIO_UPDATE_NOTIFICATIONS);

        if (!Yii::$app->user->can('manageProfile', ['model' => $model])) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'You are not allowed to access this page.'));
        }

        try{

            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if (!$model->save()) {
                    throw new Exception('User model failed to save record.');
                }

                Yii::$app->getSession()->addFlash('success', Yii::t('app', 'Your Changes Saved.'));
                return $this->redirect('/user-notifications');
            }
        } catch(Exception $e) {
            Yii::error(sprintf( $e->getMessage() ));
            Yii::$app->session->addFlash('error', Yii::t('app', $e->getMessage()));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @return User The loaded model.
     *
     * @throws NotFoundHttpException
     */
    protected function findModel()
    {
        if (($model = User::findOne(Yii::$app->user->getId())) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }
}