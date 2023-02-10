<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\helpers\ConstHelper;
use common\models\RatingDetail;

/**
 * This is the ActiveQuery class for [[\common\models\RatingDetail]].
 *
 * @see \common\models\RatingDetail
 */
class RatingDetailQuery extends ActiveQuery
{
    /**
     * Returns active rating requests, that are ready to be rated usually during 10 days
     * @param bool $state
     * @return $this
     */
    public function active($state = true)
    {
        $status = ($state===true) ? ConstHelper::STATUS_ACTIVE : ConstHelper::STATUS_PASSIVE;

        return $this->andWhere(['[[rating_detail.status]]' => $status])
            ->andWhere(['>', '[[rating_detail.created_at]]' => "NOW() - INTERVAL '10 days'"]);
    }

    /**
     * @inheritdoc
     * @return RatingDetail|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return RatingDetail[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
}
