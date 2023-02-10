<?php

namespace backend\controllers;

use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\helpers\ConstHelper;

/**
 * UserNotificationController implements the CRUD actions for User Notifications.
 * Class UserNotificationController
 * @package backend\controllers
 */
class UserNotificationController extends UserController
{
    /**
     * Displays User Notification.
     *
     * @param  integer $id The user id.
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'User Notifications'))
            ->setDescription("THERA Connect View and Manage User Notifications.")
            ->setKeywords('THERA Connect,new,service,user service,view,notifications,user notification')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Updates User Notification.
     *
     * @param integer $id The user id.
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'User Notifications'))
            ->setDescription("THERA Connect Manage User Notifications.")
            ->setKeywords('THERA Connect,new,service,new user notifications,manage notifications')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE_NOTIFICATIONS;

        if (!Yii::$app->user->can(User::USER_SUPER_ADMIN)) {
            if ($model->role->item_name === User::USER_SUPER_ADMIN) {
                return $this->goHome();
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Your Changes Saved.'));
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('update', [
            'model' => $model
        ]);
    }

}