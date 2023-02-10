<?php

namespace backend\controllers;

use Yii;
use yii\db\StaleObjectException;
use yii\helpers\Html;
use yii\web\Response;
use Throwable;
use common\helpers\ConstHelper;
use common\models\forms\LoginForm;

/**
 * Site controller.
 * It is responsible for displaying static pages, and logging users in and out.
 */
class SiteController extends BackendController
{
    /**
     * Displays the index (home) page.
     * Use it in case your home page contains static content.
     *
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->meta
        ->setTitle(Yii::t('app', 'Admin Home Page'))
        ->setDescription("THERA Connect Admin Home Page.")
        ->setKeywords('THERA Connect,Home,Admin,help pregnant,pregnancycertified')
        ->setImage(ConstHelper::getImgPath())
        ->register(Yii::$app->getView());
        return $this->render('index');
    }

    /**
     * Logs in the user if his account is activated,
     * if not, displays standard error message.
     *
     * @return string|Response
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionLogin()
    {
        $this->layout = '//main-login';
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Sign in your account'))
            ->setDescription("THERA Connect Admin Login Page.")
            ->setKeywords('THERA Connect,Home,Admin,Login,help pregnant,pregnancycertified')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());
        if (!Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $model = new LoginForm() ;

        // everything went fine, log in the user
        if ($model->load(Yii::$app->request->post()) && $model->login())
        {
            if($model->isInactive()){
                // if his account is inactive, then show message about it
                Yii::$app->session->setFlash('warning',
                    Yii::t('app','Your account is currently inactive. Please set your account as active in your profile to be visible in the system.'));
            }
            return $this->goBack();
        }
        // user couldn't be logged in, because he has not activated his account
        elseif($model->isNotActivated(true)) {
            // if his account is not activated, he will have to activate it first
            // So we are sending activation link one more time in case it was expired
            Yii::$app->session->setFlash('error',
                Yii::t('app','You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'));
            Yii::warning('You have to activate [username: '.$model->username.'] your account first. Please check your email.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);
            return $this->refresh();
        }
        // user can not login as his account suspended somehow
        elseif ($model->isSuspended()) {
            // if his account is suspended, then advise to contact with administration
            Yii::$app->session->setFlash('error',
                Yii::t('app','Your account is currently suspended. Please {contact_link}',
                    ['contact_link' => Html::a(Yii::t('app', 'contact us with your information'), ['site/contact'])]));
            Yii::warning('Your account [username: '.$model->username.'] is currently suspended. Please contact with administration.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);
            return $this->refresh();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }


    /**
     * Logs out the user.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
