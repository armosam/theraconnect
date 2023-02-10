<?php

/** @var int $starsPercent */
/** @var float $currentRating */
/** @var int $countReviews */
/** @var StarRating $model */

use common\widgets\stars\StarRating; ?>

<div class="rating-container <?=$model->size ?> rating-animate is-display-only">
    <div class="rating-stars">
        <span class="empty-stars">
            <span class="star"><i class="glyphicon glyphicon-star-empty"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star-empty"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star-empty"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star-empty"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star-empty"></i></span>
        </span>
        <span class="filled-stars" style="width: <?= $model->starPercent; ?>%;">
            <span class="star"><i class="glyphicon glyphicon-star"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star"></i></span>
            <span class="star"><i class="glyphicon glyphicon-star"></i></span>
        </span>
    </div>
    <div class="caption">
        <span class="rating-caption"><?= $model->currentRating . ' ' . '(' . $model->reviewCount . ')' ?></span>
    </div>
</div>