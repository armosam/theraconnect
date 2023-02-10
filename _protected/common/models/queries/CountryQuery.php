<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\Country;
use common\helpers\ConstHelper;

/**
 * This is the ActiveQuery class for [[Country]].
 *
 * @see \common\models\Country
 */
class CountryQuery extends ActiveQuery
{
    public function active($state = true)
    {
        $status = ($state===true) ? ConstHelper::STATUS_ACTIVE : ConstHelper::STATUS_PASSIVE;
        return $this->andWhere(['[[country.status]]' => $status]);
    }

    /**
     * {@inheritdoc}
     * @return Country|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * {@inheritdoc}
     * @return Country[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
}
