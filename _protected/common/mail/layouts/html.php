<?php

use yii\helpers\Html;
use yii\web\View;
use yii\mail\MessageInterface;
use common\helpers\ConstHelper;

/* @var $this View the view component instance */
/* @var $message MessageInterface the message being to composed */
/* @var $content string main view render result */

$linkToContactUs = Yii::$app->urlManagerToFront->createAbsoluteUrl([Yii::getAlias('site/contact')]);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
    <?php $this->beginBody() ?>
    <div style="text-align: left;">
        <?= $content ?>
        <?= Yii::t('app', 'In case of any question, please contact the site administration. {link}.', ['link' => Html::a(Yii::t('app', 'Contact us'), $linkToContactUs)]) ?><br><br>
    </div>
    <div style="margin: 20px">
        <div><img src="<?= $message->embed(ConstHelper::getImgPath(true)) ?>" alt="<?= Yii::t('app', 'THERA Connect') ?>" style="width: 80px;border: none" /></div><br>
    </div>
    <div style="margin: 30px auto;">
        <?= Yii::t('app', '&copy; {date} {link}. All Rights Reserved.', ['date'=>date('Y'), 'link'=>'<a href="https://www.theraConnect.com" rel="external">' . Yii::t('app', 'THERA Connect') . '</a>']) ?>
    </div>
    <br>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
