<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

?>

<div class="site-index">

    <div class="jumbotron">
        <!-- h1>Congratulations!</h1 -->
        <?php if(file_exists($this->theme->getPath('img/logo.png'))): ?>
            <?= Html::beginTag('a', ['href' => Yii::$app->urlManagerToFront->createAbsoluteUrl('site/index')]) ?>
            <?= Html::img($this->theme->getUrl('img/logo.png'), ['style' => 'width: 300px;margin: 0 auto 30px auto;']) ?><br>
            <?= Html::endTag('a') ?>
        <?php endif; ?>
        <p class="lead"><?= Yii::t('app','We help you find your therapist') ?></p>

        <p><a class="btn btn-lg btn-success" href="<?=Yii::$app->urlManagerToFront->createAbsoluteUrl('home')?>">Get Started With</a></p>

    </div>

<!--    <div class="body-content">-->
<!---->
<!--        <div class="row">-->
<!--            <div class="col-lg-3">-->
<!--                <h3>Documentation</h3>-->
<!---->
<!--                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod-->
<!--                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,-->
<!--                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo-->
<!--                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse-->
<!--                cillum dolore eu fugiat nulla pariatur.</p>-->
<!---->
<!--                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Documentation &raquo;</a></p>-->
<!--            </div>-->
<!--            <div class="col-lg-3">-->
<!--                <h3>How it works</h3>-->
<!---->
<!--                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod-->
<!--                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,-->
<!--                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo-->
<!--                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse-->
<!--                cillum dolore eu fugiat nulla pariatur.</p>-->
<!---->
<!--                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">More &raquo;</a></p>-->
<!--            </div>-->
<!--            <div class="col-lg-3">-->
<!--                <h3>For Therapists</h3>-->
<!---->
<!--                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod-->
<!--                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,-->
<!--                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo-->
<!--                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse-->
<!--                cillum dolore eu fugiat nulla pariatur.</p>-->
<!---->
<!--                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">More &raquo;</a></p>-->
<!--            </div>-->
<!--            <div class="col-lg-3">-->
<!--                <h3>For Home Health Care</h3>-->
<!---->
<!--                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod-->
<!--                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,-->
<!--                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo-->
<!--                consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse-->
<!--                cillum dolore eu fugiat nulla pariatur.</p>-->
<!---->
<!--                <p><a class="btn btn-default" href="http://www.freetuts.org/">More &raquo;</a></p>-->
<!--            </div>-->
<!--        </div>-->
<!---->
<!--    </div>-->
</div>

