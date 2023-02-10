<?php

use \yii\helpers\Html;

?>

<!-- ======= Hero Section ======= -->
<section id="hero">

    <div class="container">
        <div class="row">
            <div class="col-lg-6 pt-5 pt-lg-0 order-2 order-lg-1 d-flex flex-column justify-content-center" data-aos="fade-up">
                <div class="text-left">
                    <h1><?= Yii::t('app', 'THERA Health Connection') ?></h1>
                    <h2><?= Yii::t('app', 'THERA Health Connection believe that movement is medicine. We also believe in continually testing the boundaries of what physical therapy should be, and what it is meant to be.') ?></h2>
                </div>
            </div>
            <div class="col-lg-6 order-1 order-lg-2 hero-img" data-aos="fade-left">
                <br>
                <br>
<!--                <img src="--><?php //$this->theme->getUrl('img/home/hero-img.jpg')?><!--" class="img-fluid" alt="">-->
                <img src="<?= $this->theme->getUrl('img/home/about1.jpg') ?>" class="img-fluid" alt="">
            </div>
        </div>
    </div>

</section><!-- End Hero -->