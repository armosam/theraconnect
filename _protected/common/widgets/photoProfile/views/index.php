<?php

use common\widgets\photoProfile\PhotoProfile;
use yii\db\Expression;
use yii\helpers\Url;

/** @var PhotoProfile $model */

?>

<!-- ======= Portfolio Section ======= -->
<section id="portfolio" class="portfolio">
    <div class="container">

        <div class="section-title" data-aos="fade-up">
            <h2><?= $model->title?></h2>
            <p><?= $model->hint?></p>
        </div>

        <div class="row">
            <div class="col-lg-12 d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
                <ul id="portfolio-filters">
                    <?php foreach($model->filters as $value => $filter): ?>
                        <li data-filter="<?= $value ?>" <?= $filter['active'] ? 'class="filter-active"' : '' ?>><?= $filter['name']?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="row portfolio-container" data-aos="fade-up" data-aos-delay="200">
            <?php foreach($model->items as $item): ?>
                <div class="col-lg-4 col-md-6 portfolio-item <?= $item['filter'] ?>">
                    <div class="portfolio-wrap">
                        <img src="<?= Url::to(['site/avatar', 'id' => $item['id'], 'w' => 350])?>" class="img-fluid" alt="">
                        <div class="portfolio-info">
                            <h4><?= $item['name']?></h4>
                            <p><?= $item['service_name']?></p>
                        </div>
                        <div class="portfolio-links">
                            <a href="<?= Url::to(['search/view', 'id' => $item['id']]) ?>" title="<?= $item['name']?>"><i class="bx bx-detail"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</section><!-- End Portfolio Section -->

