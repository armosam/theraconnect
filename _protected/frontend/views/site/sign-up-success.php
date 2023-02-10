<h1 class="text-success">Congratulation!</h1>
<p>
    <div><?= Yii::t('app', 'Your account created successfully.') ?></div>
    <div><?= Yii::t('app', 'Please go ahead and do following steps') ?>:</div>
    <ul>
        <?php if(Yii::$app->params['rna'] == true): ?><li><?= Yii::t('app', 'Verify your account using verification email we have sent.') ?></li><?php endif; ?>
        <li><?= Yii::t('app', 'Go to your account and complete your profile.') ?></li>
        <li><?= Yii::t('app', 'Verify your phone number, email address') ?></li>
    </ul>

    <div class="hint-block"><?= Yii::t('app', 'We are going to activate your account soon.') ?></div>
</p>