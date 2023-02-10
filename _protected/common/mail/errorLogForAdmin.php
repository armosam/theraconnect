<?php

/* @var $this yii\web\View */
/* @var $hasLog bool the flag shows if error log file attached */
?>

<?= Yii::t('app', 'Dear Site Administration') ?>,<br><br>

<?=Yii::t('app', 'There is an issue requiring your immediate action. Please check as soon as possible.') ?><br><br>

<?php if($hasLog): ?>
    <?=Yii::t('app', 'For your information we attached the error log file.') ?><br><br>
<?php endif; ?>