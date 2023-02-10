<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <div>
        <div class="body-content">
            <div class="col-lg-12 well">
                <p class="text-justify text-black"><?= Yii::t('app', 'THERA Connections INC. collaborates with the most proficient and experienced therapists in order to enhance all their clients needs. We understand the gravity of the services we provide, are essential to the well being of our clients, therefore we have an array of therapists that will help our clients on their way to recovery. Get ready to break down your barriers, and let us help build you back up. We at THERA Health Connection believe that movement is medicine. We also believe in continually testing the boundaries of what physical therapy should be, and what it is meant to be.') ?></p>
            </div>
            <div class="col-lg-3 hidden-xs">
                <img class="img-responsive img-rounded" src="<?= $this->theme->getUrl('img/about/about.jpg') ?>">
            </div>
            <div class="col-lg-9 col-xs-12 well">
                <p class="text-justify text-black"><?= Yii::t('app', 'We strive to help each and every patient get back from their injury, dysfunction, or sense of not being at their best to getting to their best. Analyzing and retraining the most basic of movements can help to build a bigger, better foundation. Break it down, to build it back up. Let’s get started on this journey together. THERA Health Connection, you the patient are our top priority. We believe in sharing our knowledge with you to improve your rehab and wellness goals. We stress on improving mobility and stability of your body to maximize human performance. We dedicate ourselves to helping you achieve the best you, to get back stronger than ever. Our team is comprised of staff expertly trained to assist with your biomechanics, functional mobility, proper loading and sequencing of movements to aid in your physical rehabilitation.') ?></p>
            </div>
        </div>
    </div>
</div>
