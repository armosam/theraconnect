<?php

namespace frontend\controllers;

use Yii;
use yii\helpers\Html;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use Exception;
use common\models\User;
use common\helpers\ConstHelper;
use common\models\ChangeHistory;
use common\models\events\UserEvents;
use common\exceptions\EmailVerificationException;
use common\exceptions\DataVerificationException;
use common\exceptions\PhoneNumberVerificationException;
use frontend\models\forms\PhoneVerificationForm;

/**
 * Class ProfileController
 * @package frontend\controllers
 */
class ProfileController extends FrontendController
{
    /**
     * @return string
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'My Profile'))
            ->setDescription('View my profile.')
            ->setKeywords('My profile,avatar,my info,my data,my personal information')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = $this->findModel(Yii::$app->user->getId());

        if(!Yii::$app->user->can('manageProfile', ['model' => $model])){
            throw new MethodNotAllowedHttpException(Yii::t('app', 'You are not allowed to access this page.'));
        }

        if (!User::currentLoggedUser()->isActiveAccount()){
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Your account is not active currently. Please complete you profile data. Your account will be activated soon. {contact_link} if you think this is an issue.', [
                'contact_link' => Html::a(Yii::t('app', 'Contact us'), ['site/contact'])
            ]));
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * @return array|string|Response
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Update Profile'))
            ->setDescription('Update my profile.')
            ->setKeywords('My profile,update,avatar,my info,my data,my personal information')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = $this->findModel(Yii::$app->user->getId());
        $model->setScenario(User::SCENARIO_UPDATE);

        /** @var User $terminationModel */
        $terminationModel = User::findOne(Yii::$app->user->getId());
        $terminationModel->setScenario(User::SCENARIO_TERMINATE_ACCOUNT);

        // For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if (!Yii::$app->user->can('manageProfile', ['model' => $model])) {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'You are not allowed to access this page.'));
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                // only if user entered new password we want to hash and save it
                if ($model->password) {
                    $model->setPassword($model->password);
                }

                // Upload user avatar picture
                $model->avatar->upload_file = UploadedFile::getInstance($model->avatar, 'upload_file');
                if($model->avatar->upload_file && $model->avatar->validate()){
                    if(!$model->avatar->save()){
                        throw new Exception( Yii::t('app', Html::encode('Unable to save User File. Please try later')) );
                    }
                }

                if(!$model->save()){
                    throw new Exception( Yii::t('app', Html::encode('Unable to save User Detail. Please try later')) );
                }

                $transaction->commit();
                Yii::$app->getSession()->addFlash('success', Yii::t('app', 'Your Changes Saved.'));
                return $this->redirect('index');
            }

            // User terminates own account
            $isTerminate = Yii::$app->request->post('terminate', null);
            if ($isTerminate !== null) {
                // User termination model functionality
                if ($terminationModel->load(Yii::$app->request->post()) && $terminationModel->validate()) {
                    if ($terminationModel->terminateUserAccount()) {
                        $transaction->commit();
                        Yii::$app->user->logout();
                        return $this->goHome();
                    }
                }
            }

        }catch(Exception $e){
            $transaction->rollBack();
            Yii::error('User Profile update failed. '. $e->getMessage());
            Yii::$app->session->addFlash('error', $e->getMessage());
        }

        return $this->render('update', [
            'model' => $model,
            'terminationModel' => $terminationModel
        ]);
    }

    /**
     * Sends Verification email message with a verification link
     * @return Response
     */
    public function actionSendVerificationEmail()
    {
        try{
            if(Yii::$app->user->isGuest){
                throw new EmailVerificationException('You need to be logged in to perform this action');
            }

            $model = $this->findModel(Yii::$app->user->id);
            $changeHistory = ChangeHistory::getNotVerified($model->id, 'email');
            if (!($changeHistory && $changeHistory->generateVerificationCode('email') && $changeHistory->save())) {
                throw new EmailVerificationException('Verification code failed to update.');
            }

            $model->on(UserEvents::EVENT_ACCOUNT_EMAIL_CHANGED, [UserEvents::class, 'accountEmailChangedEventHandler'], $changeHistory);
            $model->trigger(UserEvents::EVENT_ACCOUNT_EMAIL_CHANGED);

        }catch (Exception $e){
            Yii::error('Email Verification token failed to resend. ' . $e->getMessage(), 'Profile-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', Yii::t('app', 'Verification email failed to send. Please try again.'));
        }
        return $this->redirect(['profile/index']);
    }

    /**
     * Receive phone number verification number and verify it
     * @return array|string
     * @throws EmailVerificationException
     * @throws PhoneNumberVerificationException
     */
    public function actionVerifyPhone()
    {
        $field = Yii::$app->request-> getQueryParam('field', null);
        if(Yii::$app->user->isGuest){
            throw new EmailVerificationException('You need to be logged in to perform this action');
        }

        if(!in_array($field, ChangeHistory::verificationNeededAttributes(), true)){
            throw new PhoneNumberVerificationException('Unknown field name provided to verify');
        }

        $form = new PhoneVerificationForm();

        if ($form->load(Yii::$app->request->post())) {
            try {
                if(!$form->validate()) {
                    throw new PhoneNumberVerificationException('verification code is not valid');
                }

                $model = ChangeHistory::findByVerificationCode($form->verification_code);
                if ($model === null) {
                    throw new PhoneNumberVerificationException('Verification Code not found in the change history.');
                }

                if(!$model->setVerified()){
                    throw new PhoneNumberVerificationException('Phone Number verification is failed.');
                }

                Yii::$app->session->addFlash('success', Yii::t('app', 'You successfully verified your phone number.'));

            }catch (Exception $e){
                Yii::error("Verification for {$field} failed.". $e->getMessage());
                Yii::$app->session->addFlash('error', Yii::t('app', 'Incorrect verification code. Please get a new code.'));
            }
            return $this->redirect('index');
        }

        return $this->renderAjax('verify-phone', [
            'field' => $field,
            'model' => $form
        ]);
    }

    /**
     * Sends Verification email message with a verification link
     * @return array
     */
    public function actionSendVerificationCodeAgain()
    {
        try{
            if(Yii::$app->user->isGuest){
                throw new EmailVerificationException('You need to be logged in to perform this action');
            }

            $field = Yii::$app->request->post('field', null);

            if(!in_array($field, ChangeHistory::verificationNeededAttributes(), true)){
                throw new DataVerificationException('Unknown field provided to send verification code.');
            }

            if(Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model = $this->findModel(Yii::$app->user->getId());
                $changeHistory = ChangeHistory::getNotVerified($model->id, $field);
                if (!($changeHistory && $changeHistory->generateVerificationCode($field) && $changeHistory->save())) {
                    throw new EmailVerificationException('Verification code failed to update.');
                }

                $model->on(UserEvents::EVENT_ACCOUNT_PHONE_CHANGED, [UserEvents::class, 'accountPhoneChangedEventHandler'], $changeHistory);
                $model->trigger(UserEvents::EVENT_ACCOUNT_PHONE_CHANGED);
            }

        }catch (Exception $e){
            Yii::error('Email Verification token failed to resend. ' . $e->getMessage(), 'Profile-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', Yii::t('app', 'Verification email failed to send. Please try again.'));
        }
        return Yii::$app->session->getAllFlashes();
    }

    /**
     * Dismiss email address change restore it and clear verification requirement
     * @return Response
     */
    public function actionDismissChange($field)
    {
        try {
            if(Yii::$app->user->isGuest){
                throw new EmailVerificationException('You need to be logged in to perform this action');
            }
            if(!in_array($field, ChangeHistory::verificationNeededAttributes(), true)){
                throw new DataVerificationException('Unknown field provided to dismiss changes.');
            }

            $user = $this->findModel(Yii::$app->user->getId());
            if($field === 'email'){
                $user->removeAccountActivationToken();
                $user_update = $user->save(true, ['account_activation_token']);
            }

            $history_update = ChangeHistory::restoreNotVerifiedChange(Yii::$app->user->id, $field);

            if(!$history_update){
                throw new EmailVerificationException('Change History failed to restore.');
            }

            Yii::$app->session->addFlash('success', Yii::t('app', 'Changed {attribute} address restored to the old position successfully.', ['attribute' => $user->getAttributeLabel($field)]));
        }catch (Exception $e){
            Yii::error('Dismiss changes failed to restore old values. ' . $e->getMessage(), 'Profile-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', Yii::t('app', 'Changed attribute failed to restore to the old position.'));
        }
        return $this->redirect(['profile/index']);
    }

    /**
     * Finds the model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param  integer $id The user id.
     * @return User The loaded model.
     *
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }

}
