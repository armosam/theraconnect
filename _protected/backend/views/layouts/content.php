<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\Breadcrumbs;
use kartik\dialog\Dialog;
use dmstr\widgets\Alert;

/* @var View $this */
/** @var string $content */

?>
<div class="content-wrapper">
    <section class="content-header">
        <?php if (isset($this->blocks['content-header'])) { ?>
            <h1><?= $this->blocks['content-header'] ?></h1>
        <?php } else { ?>
            <h1>
                <?php
                if ($this->title !== null) {
                    echo Html::encode($this->title);
                } else {
                    echo Inflector::camel2words(
                        Inflector::id2camel($this->context->module->id)
                    );
                    echo ($this->context->module->id !== Yii::$app->id) ? '<small>Module</small>' : '';
                } ?>
            </h1>
        <?php } ?>

        <?=
        Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
    </section>

    <section class="content">
        <?= Alert::widget() ?>
        <?= Dialog::widget(['overrideYiiConfirm' => true]) ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">
    <?= Yii::t('app', '&copy; {date} {link}. All Rights Reserved.', ['date'=>date('Y'), 'link'=>'<a href="https://www.Connect.com" rel="external">' . Yii::t('app', 'THERA Connect') . '</a>']) ?>
</footer>