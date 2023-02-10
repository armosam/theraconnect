<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-how-it-works">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="container">
        <div class="container-panel">
            <div class="row">
                <h3 style="padding: 0 15px;"><?= Yii::t('app', 'For Home Health Agencies')?></h3>
                <div class="col-sm-8 col-xs-12 well">
                    <ol>
                        <li>
                            <b><?= Yii::t('app', 'Use our web site to create your account.') ?>:</b><br>
                                <?= Yii::t('app', '{sign_up} and create an account.', ['sign_up' => Html::a(Yii::t('app', 'Sign up'), ['site/sign-up-customer'])]) ?><br>
                                <?= Yii::t('app', 'Login your account and setup your account. Please set all details in the profile page.') ?><br><br>
                        </li>
                        <li>
                            <b><?= Yii::t('app', 'Get activated by administration')?>:</b><br>
                            <?= Yii::t('app', 'After registration we will receive your request to join us.') ?><br>
                            <?= Yii::t('app', 'Our administration will check your request and accept it.') ?><br>
                            <?= Yii::t('app', 'You can also contact us if you find there is an information you have to keep updated.') ?><br><br>
                        </li>
                        <li>
                            <b><?= Yii::t('app', 'You can load your patients')?>:</b><br>
                            <?= Yii::t('app', 'After activation you can login your account and upload your patients')?><br>
                            <?= Yii::t('app', 'Please be informed that there are required fields you have to fill and also upload patient intake, form-485 etc.') ?><br>
                            <?= Yii::t('app', 'When you add a patient you can select services added for patient. Also you can create service requests for each existing patient.')?><br>
                            <?= Yii::t('app', 'After loading your patients and service requests administration is going to find and assign you patient the therapist required.')?><br><br>
                        </li>
                        <li>
                            <b><?= Yii::t('app', 'Check patient care on line')?></b><br>
                            <?= Yii::t('app', 'You can login and check each patient\'s progress and status.' ) ?>
                        </li>
                    </ol>
                    <br><br>
                </div>
                <!-- div class="col-sm-5 col-xs-12">
                    <iframe width="100%" height="238" src="https://www.youtube.com/embed/YZ2fGENYBqo?playlist=Sm_Hdjp2W98&loop=1" frameborder="0" allowfullscreen></iframe>
                </div -->
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <h3 style="padding: 0 15px;"><?= Yii::t('app', 'For Therapists')?></h3>
                <div class="col-sm-8 col-xs-12 well ">
                    <ol>
                        <li>
                            <b><?= Yii::t('app', 'Join as a therapist') ?></b><br>
                            <?= Yii::t('app', 'You can {sign_up_specialist} by submitting an application form on our web site.', ['sign_up_specialist' => Html::a(Yii::t('app', 'Join  as a therapist'), ['join/application-form'])]) ?><br>
                            <?= Yii::t('app', 'You need to provide your current information including service providing, your current license information, speaking languages, coverage areas and etc.') ?><br><br>
                        </li>
                        <li>
                            <b><?= Yii::t('app', 'Get accepted and login') ?></b><br>
                            <?= Yii::t('app', 'We are going to check you application and approve it. ') ?><br>
                            <?= Yii::t('app', 'After approving your application you will get an email message with a link to set your password.') ?><br>
                            <?= Yii::t('app', 'Please set you password and login your new account. Go to your profile page and setup it first.') ?><br><br>
                            <?= Yii::t('app', 'You must set up  all your credentials to be able to take patients. Administration will check and accept your credentials') ?><br><br>
                        </li>
                        <li>
                            <b><?= Yii::t('app', 'Getting new patients') ?></b><br>
                            <?= Yii::t('app', 'You can find a new patients yourself or administration can assign you patients. You will also receive notifications.') ?><br>
                            <?= Yii::t('app', 'Please check patient details and schedule visits.') ?><br>
                            <?= Yii::t('app', 'For each visit you need to complete medical notes.') ?><br><br>
                        </li>
                        <li>
                            <b><?= Yii::t('app', 'Evaluate patient') ?></b><br>
                            <?= Yii::t('app', 'First visit should be patient evaluation. Later you can continue with patient or send to another therapist') ?><br>
                            <?= Yii::t('app', 'During evaluation therapist sets service frequency and administration is going to accept it for future visits.') ?><br>
                        </li>
                    </ol>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div><br><br>

    <?= Yii::t('app', 'Please {contact_us} if you have new ideas or suggestions.', ['contact_us' => Html::a(Yii::t('app', 'contact us'), ['site/contact'])])?>
</div>
