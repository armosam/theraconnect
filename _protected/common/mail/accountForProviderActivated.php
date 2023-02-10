<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$linkToAccount = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('profile/index')]);
?>

<?= Yii::t('app', 'Dear {name}', ['name'=> Yii::t('app', 'Specialist')]) ?>,<br><br>

<?=Yii::t('app', 'Your account has been activated successfully.') ?><br><br>

<?= Yii::t('app', 'To complete your account please finish following steps') ?>:
<ul>
    <li><?= Yii::t('app', 'Login your account')?> (<?= Html::a(Yii::t('app', 'Go to my account'), $linkToAccount)?>)</li>
    <li><?= Yii::t('app', 'Add services you are going to provide. You can configure details when you add a new service') ?>,</li>
    <li><?= Yii::t('app', 'Add your qualifications you have currently. We have a list of qualifications that you can select') ?>,</li>
    <li><?= Yii::t('app', 'Add languages you are speaking') ?>,</li>
    <li><?= Yii::t('app', 'Add attractive photos to your gallery to help your expectant clients to be more informed about your services') ?>,</li>
</ul>
<br>
<?= Yii::t('app' , 'After completing these steps your account is ready') ?>.<br><br>

<?= Yii::t('app' , 'You will receive service request notifications from clients') ?>.<br>
<?= Yii::t('app' , 'Please check your email periodically and respond to service requests as soon as it possible. That will help you find your clients fast and easy') ?>.<br><br>
