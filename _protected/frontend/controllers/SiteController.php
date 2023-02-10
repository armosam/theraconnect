<?php

namespace frontend\controllers;

use Yii;
use Throwable;
use Exception;
use yii\widgets\ActiveForm;
use yii\db\StaleObjectException;
use yii\helpers\Html;
use yii\imagine\Image;
use yii\web\Response;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use common\models\User;
use common\models\UserAvatar;
use common\models\forms\LoginForm;
use common\models\ChangeHistory;
use common\helpers\ConstHelper;
use common\exceptions\EmailVerificationException;
use common\exceptions\AccountActivationException;
use frontend\models\AccountActivation;
use frontend\models\forms\PasswordResetRequestForm;
use frontend\models\forms\ResetPasswordForm;
use frontend\models\forms\ContactForm;
use frontend\models\forms\SignUpForm;
use frontend\models\forms\SignUpCustomerForm;
use frontend\models\forms\SignUpProviderForm;

/**
 * Site controller.
 * It is responsible for displaying static pages, logging users in and out,
 * sign up and account activation, password reset.
 */
class SiteController extends FrontendController
{
    /**
     * Displays the index (home) page.
     * Use it in case your home page contains static content.
     *
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'home';
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Home'))
            ->setDescription('THERA Connect is a web application to help home health agencies find qualified therapists.')
            ->setKeywords('THERA Connect,qualified,help,home,health,therapists')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('index');
    }

    /**
     * Displays the about static page.
     *
     * @return string
     */
    public function actionAbout()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'About Us'))
            ->setDescription('We help home health agencies to find qualified therapists. About Us')
            ->setKeywords('THERA Connect,qualified,therapist')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('about');
    }

    /**
     * Displays the contact static page and sends the contact email.
     *
     * @return string|Response
     */
    public function actionContact()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Contact Us'))
            ->setDescription('Please contact us with any question or suggestion.')
            ->setKeywords('THERA Connect,qualified,therapist,contact,contact us')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new ContactForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->sendContactEmail(Yii::$app->params['supportEmail'])) {
                Yii::$app->session->addFlash('success',
                    Yii::t('app','Thank you for contacting us. We will respond to you as soon as possible.'));
            } else {
                Yii::$app->session->addFlash('error', Yii::t('app','There was an error sending email.'));
                Yii::error('There was an error to send contact email message');
            }

            return $this->refresh();
        } 
        
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays the privacy policy static page.
     *
     * @return string
     */
    public function actionPrivacyPolicy()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Privacy Policy'))
            ->setDescription('We help home health agencies to find qualified therapists. Privacy policy')
            ->setKeywords('THERA Connect,qualified,therapist')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('privacy_policy');
    }

    /**
     * Displays the therms od service static page.
     *
     * @return string
     */
    public function actionTermsOfService()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Terms of Service'))
            ->setDescription('We help home health agencies to find qualified therapists. Therms of service')
            ->setKeywords('THERA Connect,qualified,therapist')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('terms_of_service');
    }

    /**
     * Displays the service static page.
     *
     * @return string
     */
    public function actionService()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Our Services'))
            ->setDescription('We help home health agencies to find qualified therapists. Our Services')
            ->setKeywords('THERA Connect,certified specialist,therapist,service')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('service');
    }

    /**
     * Displays the how it works static page.
     *
     * @return string
     */
    public function actionHowItWork()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'How it works'))
            ->setDescription('We help home health agencies to find qualified therapists. How it works')
            ->setKeywords('THERA Connect,qualified,therapist,works')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('how_it_works');
    }

    /**
     * Logs in the user if his account is activated,
     * if not, displays appropriate message.
     *
     * @return string|Response
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionLogin()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Sign in your account'))
            ->setDescription('Please login you account.')
            ->setKeywords('THERA Connect,qualified,therapist,login,account')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        // now we can try to log in the user
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if($model->isInactive()){
                // if his account is inactive, then show message about it
                Yii::$app->session->addFlash('warning',
                    Yii::t('app','Your account is currently inactive. Please set your account as active in your profile to be visible in the system.'));
            }
            return $this->goBack();
        }
        // user couldn't be logged in, because he has not activated his account
        elseif($model->isNotActivated(true)) {
            // if his account is not activated, he will have to activate it first
            // So we are sending activation link one more time in case it was expired
            Yii::$app->session->addFlash('error', Yii::t('app','You have to activate your account first. We sent you activation email again. Please use latest activation email we sent you to activate your account.'));
            Yii::warning('You have to activate [username: '.$model->username.'] your account first. Please check your email.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);
            return $this->refresh();
        }
        // user couldn't be logged in, because account is suspended
        elseif($model->isSuspended()) {
            // if his account is suspended he has to contact administration
            Yii::$app->session->addFlash('error', Yii::t('app', 'Your account is currently suspended. Please {contact_link}',
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

/*----------------*
 * PASSWORD RESET *
 *----------------*/

    /**
     * Sends email that contains link for password reset action.
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionRequestPasswordReset()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Request password reset'))
            ->setDescription('Forgot you password? You can reset it.')
            ->setKeywords('THERA Connect,qualified,therapist,password,reset,password reset,request password reset')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new PasswordResetRequestForm();

        try {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                if ($model->sendPasswordResetRequestEmail()) {
                    Yii::$app->session->addFlash('success',
                        Yii::t('app','Check your email for further instructions.'));
                }else{
                    Yii::$app->session->addFlash('error',
                        Yii::t('app','We are unable to send email for password reset request by email provided.'));
                }

                return $this->goHome();
            }
        }catch (Exception $e){
            Yii::error('Password reset request failed. '.$e->getMessage(), __CLASS__.':'.__FUNCTION__);
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token Password reset token.
     * @return string|Response
     *
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionResetPassword($token)
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'New Password'))
            ->setDescription('Please set you new password.')
            ->setKeywords('THERA Connect,qualified,therapist,password,set password')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try{
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                if ($model->resetPassword()) {
                    Yii::$app->session->addFlash('success', Yii::t('app', 'Your password successfully changed.'));
                } else {
                    Yii::$app->session->addFlash('error', Yii::t('app', 'We could not reset your password, please try again or {contact_link}.', [
                        'contact_link' => Html::a(Yii::t('app', 'contact us with your information'), 'site/contact')
                    ]));
                }

                return $this->goHome();
            }
        }catch (Exception $e){
            Yii::error('Password reset failed for user '. $model->userModel()->username, __CLASS__.':'.__FUNCTION__);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }    

//------------------------------------------------------------------------------------------------//
// SIGN UP / SIGN UP AS CUSTOMER / SIGN UP AS PROVIDER / ACCOUNT ACTIVATION
//------------------------------------------------------------------------------------------------//

    /**
     * This is default sign up action
     * Signs up the customer account.
     * If user need to activate his account via email, we will display him
     * message with instructions and send him account activation email
     * ( with link containing account activation token ). If activation is not
     * necessary, we will log him in right after sign up process is complete.
     * NOTE: You can decide whether or not activation is necessary,
     * @return string|array|Response
     * @see config/params.php
     */
    public function actionSignUp()
    {
        return $this->redirect('site/login');
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Sign up as Home Health Agency'))
            ->setDescription('Sign up and create you account for free.')
            ->setKeywords('THERA Connect,qualified,therapist,sign up, register, create account')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if (!Yii::$app->user->isGuest){
            return $this->goHome();
        }

        // get setting value for 'Registration Needs Activation'
        $rna = Yii::$app->params['rna'];

        // if 'rna' value is 'true', we instantiate SignUpForm in 'rna' scenario
        $model = $rna ? new SignUpForm(['scenario' => User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION]) : new SignUpForm();

        //For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // collect and validate user data
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            // try to save user data in database
            $user = $model->signUp();
            if (!empty($user) && ($user instanceof User))
            {
                // if user is active he will be logged in automatically ( this will be first user )
                if ($user->status === User::USER_STATUS_ACTIVE)
                {
                    if (Yii::$app->getUser()->login($user)) {
                        return $this->goHome();
                    }

                    // New user couldn't login after sign up display error message to user
                    Yii::$app->session->addFlash('error', Yii::t('app', 'We could not login you first time, please try to login again or {contact_link}.', [
                        'contact_link' => Html::a(Yii::t('app', 'contact us with your information'), 'site/contact')
                    ]));

                    // log this error, so we can debug possible problem easier.
                    Yii::error('Login first time failed! User ' . Html::encode($user->username) . ' could not login to the new created account.', __CLASS__ . ':' . __METHOD__ . ':' . __LINE__);
                } else {
                    // activation is needed
                    if ($model->sendAccountActivationEmail($user)) {
                        Yii::$app->session->addFlash('success',
                            Yii::t('app','Hello {user}, To be able to log in you need to confirm your registration. Please check your email, we have sent you a message.', [
                                'user' => Html::encode($user->username)
                            ]));
                    } else {
                        // email could not be sent
                        // display error message to user
                        Yii::$app->session->addFlash('error',
                            Yii::t('app','We could not send you account activation email, please try again or {contact_link}.', [
                                'contact_link' => Html::a(Yii::t('app', 'contact us with your information'), 'site/contact')
                            ]));

                        // log this error, so we can debug possible problem easier.
                        Yii::error('Signup failed! 
                        User '.Html::encode($user->username).' could not sign up.
                        Possible causes: verification email could not be sent.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);
                    }

                    return $this->refresh();
                }

            } else { // user could not be saved in database

                // display error message to user
                Yii::$app->session->addFlash('error', Yii::t('app','We could not sign you up, please try again or {contact_link}.', [
                    'contact_link' => Html::a(Yii::t('app', 'contact us with your information'), 'site/contact')
                ]));

                // log this error, so we can debug possible problem easier.
                Yii::error('Signup failed! User '.Html::encode($model->username).' could not sign up.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);

                return $this->refresh();
            }
        }
                
        return $this->render('sign-up', [
            'model' => $model,
        ]);     
    }

    /**
     * Signs up the customer account.
     * If user need to activate his account via email, we will display him
     * message with instructions and send him account activation email
     * ( with link containing account activation token ). If activation is not
     * necessary, we will log him in right after sign up process is complete.
     * NOTE: You can decide whether or not activation is necessary,
     * @return string|array|Response
     * @see config/params.php
     */
    public function actionSignUpCustomer()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Sign up as a Home Health Care Agency'))
            ->setDescription('Sign up and create you account for free.')
            ->setKeywords('THERA Connect,home health care,sign up,register,create account')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if (Yii::$app->session->hasFlash('signup.success')) {
            Yii::$app->session->setFlash('success', Yii::$app->session->getFlash('signup.success'));
            return $this->render('sign-up-success');
        }

        if (!Yii::$app->user->isGuest){
            return $this->goHome();
        }

        // get setting value for 'Registration Needs Activation'
        $rna = Yii::$app->params['rna'];

        // if 'rna' value is 'true', we instantiate SignUpForm in 'rna' scenario
        $model = $rna ? new SignUpCustomerForm(['scenario' => User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION]) : new SignUpCustomerForm();

        //For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // collect and validate user data
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            // try to save user data in database
            $user = $model->signUp();
            if (!empty($user) && ($user instanceof User))
            {
                // if user is active he will be logged in automatically ( this will be first user )
                if ($user->status === User::USER_STATUS_ACTIVE)
                {
                    if (Yii::$app->getUser()->login($user)) {
                        Yii::$app->session->setFlash('signup.success',
                            Yii::t('app','Congratulation! {user}, Your account is created. Please complete your profile.', [
                                'user' => Html::encode($user->getUserFullName())
                            ])
                        );
                        return $this->refresh();
                    }

                    // New user couldn't login after sign up display error message to user
                    Yii::$app->session->setFlash('error', Yii::t('app', 'We could not login you first time, please try to login again or {contact_link}.', [
                        'contact_link' => Html::a(Yii::t('app', 'contact us for support'), 'site/contact')
                    ]));

                    // Log this error, so we can debug possible problem easier.
                    Yii::error('Customer Login failed for first time! User ' . Html::encode($user->username) . ' could not login to the new created account.', __CLASS__ . ':' . __FUNCTION__ . ':' . __LINE__);
                } else {
                    // activation is needed
                    if ($model->sendAccountActivationEmail($user)) {
                        Yii::$app->session->setFlash('signup.success',
                            Yii::t('app','Congratulation! {user}, Your account is created. We have sent you a verification email. Please follow the email instruction and confirm your registration.', [
                                'user' => Html::encode($user->getUserFullName())
                            ]));
                    } else {
                        // Email could not be sent. display error message to user
                        Yii::$app->session->setFlash('error',
                            Yii::t('app','Sorry! We could not send you an account verification email, please try again or {contact_link}.', [
                                'contact_link' => Html::a(Yii::t('app', 'contact us for support'), 'site/contact')
                            ]));

                        // log this error, so we can debug possible problem easier.
                        Yii::error('Customer Signup failed! User '.Html::encode($user->username).'. The verification email failed be sent.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);
                    }
                }

            } else { // user could not be saved in database

                // display error message to user
                Yii::$app->session->setFlash('error', Yii::t('app','We could not sign you up, please try again or {contact_link}.', [
                    'contact_link' => Html::a(Yii::t('app', 'contact us for support'), 'site/contact')
                ]));

                // log this error, so we can debug possible problem easier.
                Yii::error('Customer Signup failed! User '.Html::encode($model->username).' could not sign up.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);
            }

            return $this->refresh();
        }

        return $this->render('sign-up-customer', [
            'model' => $model,
        ]);
    }

    /**
     * Signs up the provider account.
     * If user need to activate his account via email, we will display him
     * message with instructions and send him account activation email
     * ( with link containing account activation token ). If activation is not
     * necessary, we will log him in right after sign up process is complete.
     * NOTE: You can decide whether or not activation is necessary,
     * @return string|array|Response
     * @see config/params.php
     */
    public function actionSignUpProvider()
    {
        return $this->redirect('site/login');
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Sign up as a therapist'))
            ->setDescription('Sign up as therapist and create you account for free.')
            ->setKeywords('THERA Connect,qualified,therapist,sign up as therapist,register as therapist,create therapist account')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if (!Yii::$app->user->isGuest){
            return $this->goHome();
        }

        // get setting value for 'Registration Needs Activation'
        $rna = Yii::$app->params['rna'];

        // if 'rna' value is 'true', we instantiate SignUpForm in 'rna' scenario
        $model = $rna ? new SignUpProviderForm(['scenario' => User::SCENARIO_REGISTRATION_NEEDS_ACTIVATION]) : new SignUpProviderForm();

        //For ajax validation of fields
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        // collect and validate user data
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            // try to save user data in database
            $user = $model->signUp();
            if (!empty($user) && ($user instanceof User))
            {
                // if user is active he will be logged in automatically ( this will be first user )
                if ($user->status === User::USER_STATUS_ACTIVE)
                {
                    if (Yii::$app->getUser()->login($user)) {
                        return $this->goHome();
                    }

                    // New user couldn't login after sign up display error message to user
                    Yii::$app->session->addFlash('error', Yii::t('app', 'We could not login you first time, please try to login again or {contact_link}.', [
                        'contact_link' => Html::a(Yii::t('app', 'contact us with your information'), 'site/contact')
                    ]));

                    // log this error, so we can debug possible problem easier.
                    Yii::error('Login first time failed! User ' . Html::encode($user->username) . ' could not login to the new created account.', __CLASS__ . ':' . __METHOD__ . ':' . __LINE__);
                } else {
                    // activation is needed
                    if ($model->sendAccountActivationEmail($user)) {
                        Yii::$app->session->addFlash('success',
                            Yii::t('app','Hello {user}, To be able to log in you need to confirm your registration. Please check your email, we have sent you a message.', [
                                'user' => Html::encode($user->username)
                            ]));
                    } else {
                        // email could not be sent
                        // display error message to user
                        Yii::$app->session->addFlash('error',
                            Yii::t('app','We could not send you account activation email, please try again or {contact_link}.', [
                                'contact_link' => Html::a(Yii::t('app', 'contact us with your information'), 'site/contact')
                            ]));

                        // log this error, so we can debug possible problem easier.
                        Yii::error('Sign up as failed! 
                        User '.Html::encode($user->username).' could not sign up.
                        Possible causes: verification email could not be sent.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);
                    }

                    return $this->refresh();
                }

            } else { // user could not be saved in database

                // display error message to user
                Yii::$app->session->addFlash('error', Yii::t('app','We could not sign you up, please try again or {contact_link}.', [
                    'contact_link' => Html::a(Yii::t('app', 'contact us with your information'), 'site/contact')
                ]));

                // log this error, so we can debug possible problem easier.
                Yii::error('Sign up as failed! User '.Html::encode($model->username).' could not sign up.', __CLASS__.':'.__FUNCTION__.':'.__LINE__);

                return $this->refresh();
            }
        }

        return $this->render('sign-up-provider', [
            'model' => $model,
        ]);
    }

/*--------------------*
 * ACCOUNT ACTIVATION *
 *--------------------*/

    /**
     * Activates the user account so he can log in into system.
     *
     * @param string $token
     * @return Response
     *
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionActivateAccount($token)
    {
        // Logout if user is logged in
        Yii::$app->user->logout(true);

        try {
            $model = new AccountActivation($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($user = $model->activateAccount()) {

            if(Yii::$app->getUser()->login($user)){

                Yii::$app->session->addFlash('success',
                    Yii::t('app', 'Congratulation! You Successfully activated your account. Please complete your profile details. Administration will approve your account soon.'));

                return $this->goHome();
            }else{
                Yii::$app->session->addFlash('success',
                    Yii::t('app', 'Congratulation! You Successfully activated your account. Please login and complete your profile details. Administration will approve your account soon.',
                        ['user' => Html::encode($model->username)]));
                Yii::error('User '. Html::encode($model->username). ' could not login after account activation.', __CLASS__.':'.__FUNCTION__);
            }
        } else {
            Yii::$app->session->addFlash('error',
                Yii::t('app', 'Your account could not be activated, please try again or {contact_link}!',
                    ['contact_link' => Html::a(Yii::t('app', 'contact us with your information'), ['site/contact'])]));
            Yii::error('User '. Html::encode($model->username). ' could not activate account.', __CLASS__.':'.__FUNCTION__);
        }

        return $this->redirect('login');
    }

    /**
     * Verifies email address by accepting token
     * @param string $token
     * @return Response
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionVerifyEmailAddress($token)
    {
        if (empty($token) || !is_string($token)){
            throw new BadRequestHttpException(Yii::t('app', 'Email verification token cannot be blank.'));
        }

        $model = ChangeHistory::findByVerificationToken($token);
        if($model === null){
            throw new BadRequestHttpException(Yii::t('app', 'Wrong email verification token. Please use the latest email we sent you for verification.'));
        }

        try{
            if(!$model->setVerified()){
                throw new EmailVerificationException('Email verification is failed.');
            }

            Yii::$app->session->addFlash('success', Yii::t('app', 'You successfully verified your email address.'));

            if(!Yii::$app->user->isGuest || Yii::$app->getUser()->login($model->user)) {
                return $this->redirect(['profile/index']);
            }

            Yii::warning('User '. Html::encode($model->user->username). ' could not login after email verification.', static::class.':'.__FUNCTION__);

        }catch (Exception $e){
            Yii::error('User '. Html::encode($model->user->username). ' could not verify email address. '. $e->getMessage(), static::class.':'.__FUNCTION__);
            Yii::$app->session->addFlash('error', Yii::t('app', 'Your email address not verified, please try again or {contact_link}.',
                ['contact_link' => Html::a(Yii::t('app', 'contact us with your information'), ['site/contact'])]));
        }
        return $this->goHome();
    }

    /**
     * Activates the user account and redirects to the Reset Password page to set new password.
     *
     * @param string $account_activation_token
     * @param string $password_reset_token
     * @return Response
     * @throws \Exception
     */
    public function actionActivateAccountAndSetNewPassword($account_activation_token, $password_reset_token)
    {
        // Logout if user is logged in
        Yii::$app->user->logout(true);

        try {
            $model = new AccountActivation($account_activation_token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        try {

            if (!$model->activateAccount()) {
                throw new AccountActivationException('Unable to activate user account. Please try later');
            } elseif (!empty($password_reset_token)) {
                return $this->redirect(['site/reset-password', 'token' => $password_reset_token]);
            }

            Yii::$app->session->addFlash('success', Yii::t('app', 'Your account has been activated successfully.'));
            Yii::$app->getUser()->login($model->getUser());

        } catch (Exception $e) {
            Yii::$app->session->addFlash('error',
                Yii::t('app', 'Your account could not be activated, please try again or {contact_link}!',
                    ['contact_link' => Html::a(Yii::t('app', 'contact us with your information'), ['site/contact'])]));

            // log this error, so we can debug possible problem easier.
            Yii::error(sprintf('Activation Failed: Username: %s | Email: %s Could not sign up. Error Message: %s', $model->getUsername(), $model->getEmail(), $e->getMessage()));
        }

        return $this->goHome();
    }

    /**
     * Action returns user avatar file content to the end user as an image
     */
    public function actionAvatar()
    {
        $id = Yii::$app->request->get('id', 0);
        /** @var int $w width of picture */
        $w = Yii::$app->request->get('w', 200);
        /** @var int $h height of picture */
        $h = Yii::$app->request->get('h', $w);
        /** @var int $q quality of picture */
        $q = Yii::$app->request->get('q', 80);
        /** @var int $s source of picture. It can be from file system (=1) or from the database (=0) */
        $s = Yii::$app->request->get('s', false);

        $model = UserAvatar::findOne(['user_id'=>$id]);

        if (empty($model) || (!is_resource($model->file_content) && !is_file(Yii::getAlias('@uploads/avatars/'.$model->file_name)) && !is_readable(Yii::getAlias('@uploads/avatars/'.$model->file_name)))){
            $file_name = Yii::getAlias('@uploads/avatar.png');
            $mime_type = 'png';
        }else{
            if ($s && is_file(Yii::getAlias('@uploads/avatars/'.$model->file_name)) && is_readable(Yii::getAlias('@uploads/avatars/'.$model->file_name))) {
                $file_name = Yii::getAlias('@uploads/avatars/'.$model->file_name);
                $mime_type = substr($model->mime_type, 6);
            } else {
                $mime_type = substr($model->mime_type, 6);
                $file_name = tempnam('/tmp/', 'file-');
                file_put_contents($file_name, $model->file_content);
                if(!file_exists(Yii::getAlias('@uploads/avatars/'.$model->file_name))){
                    copy($file_name, Yii::getAlias('@uploads/avatars/'.$model->file_name));
                }
            }
        }

        Image::getImagine()
            ->open($file_name)
            ->thumbnail(new Box($w, $h), ImageInterface::THUMBNAIL_INSET)
            ->show($mime_type, ['jpeg_quality'=>$q]);

        if (!$s) {
            if (is_file($file_name) && $file_name !== Yii::getAlias('@uploads/avatar.png')) {
                unlink($file_name);
            }
        }
        exit;
    }
}
