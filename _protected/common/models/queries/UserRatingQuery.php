<?php

namespace common\models\queries;

use \yii\db\ActiveQuery;
use common\models\UserRating;

/**
 * This is the ActiveQuery class for [[\common\models\UserRating]].
 *
 * @see \common\models\UserRating
 */
class UserRatingQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return UserRating|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return UserRating[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
}
