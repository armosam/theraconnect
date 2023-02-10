<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;
use frontend\models\forms\ContactForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model ContactForm */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-contact">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-lg-5 well">

        <p>
            <?= Yii::t('app', 'If you have business inquiries, questions, or want to leave your review, please fill out the following form to contact us. Thank you.'); ?>
        </p>

        <?php $form = ActiveForm::begin(['id' => 'contact-form']); ?>

            <?= $form->field($model, 'name') ?>
            <?= $form->field($model, 'email') ?>
            <?= $form->field($model, 'subject') ?>
            <?= $form->field($model, 'body')->textArea(['rows' => 4]) ?>
            <?= $form->field($model, 'verifyCode')->widget(Captcha::class, [
                'template' => '<div class="row">
                        <div class="col-md-5"><a href="#">{image}</a></div>
                        <div class="col-md-7">{input}</div><div class="clearfix"></div>
                        <div class="col-md-12 hint-block">' . Yii::t('app', 'Please click on the code to change it.') .'</div>
                    </div>',
                'options' => ['placeholder' => Yii::t('app', 'Enter verification code'), 'class' => 'form-control'],
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Submit'), ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
    <div class="col-lg-6 col-lg-offset-1 hidden-xs">
        <img class="img-responsive img-rounded" src="<?= $this->theme->getUrl('img/contact/contact.jpg') ?>" alt="">
    </div>

</div>
