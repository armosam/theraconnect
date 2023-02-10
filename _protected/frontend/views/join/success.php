<?php

use \yii\helpers\Html;
?>

<div>
    <h1 class="text-success">Congratulation!</h1>
    <br>
    <p>
        Your application submitted successfully to our team.<br>
        Please give us a couple of days to to check your information.<br>
        We are going to send you a link to activate your account and upload your credentials and information.<br>
    </p>
    <br>
    <p>
        By receiving email message from us please click on the link from email and set your password.<br>
        That's it! Now you can login to your account and upload your information.<br>
        Your uploaded information will be approved by administration and you can take patients.<br>
    </p>
    <br>
    <span class="text-warning">Just a friendly reminder, that it is possible we decline your application if we find your data is not acceptable for us. Thank you</span>

    <div class="hint-block">
        <?= Yii::t('app', 'Please {link} if you have a question.', ['link' => Html::a('Contact us', ['site/contact'], ['class' => 'label label-primary'])]) ?><br>
        <?= Yii::t('app', 'Our phone number is {link}', ['link' => Html::a('+1 (626) 415-8880', 'tel:626-415-8880', ['class' => 'label label-primary'])]) ?>
    </div>
</div>