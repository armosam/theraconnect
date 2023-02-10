<?php

namespace common\widgets\stars;

use yii\base\Widget;

/**
 * Class StarRating
 * @package common\widgets\StarRating
 */
class StarRating extends Widget
{
    /**
     * @var float $currentRating
     */
    public $currentRating = 0;

    /**
     * @var int $reviewCount
     */
    public $reviewCount = 0;

    /**
     * @var int $starPercent
     */
    public $starPercent;

    /**
     * Size of stars: rating-xs rating-sm rating-lg
     * @var string $size
     */
    public $size = 'rating-xs';


    public function init()
    {
        parent::init();

        $this->starPercent = $this->currentRating * 100 / 5;
    }

    public function run()
    {
        StarRatingAsset::register($this->getView());
        return $this->render('star_rating', ['model' => $this]);
    }
}