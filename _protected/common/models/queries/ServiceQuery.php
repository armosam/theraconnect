<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\helpers\ConstHelper;
use common\models\Service;

/**
 * This is the ActiveQuery class for [[\common\models\Service]].
 *
 * @see \common\models\Service
 */
class ServiceQuery extends ActiveQuery
{
    /**
     * @param bool $state
     * @return ServiceQuery
     */
    public function active($state = true)
    {
        $status = ($state===true) ? ConstHelper::STATUS_ACTIVE : ConstHelper::STATUS_PASSIVE;
        return $this->andWhere([Service::tableName().'.status' => $status]);
    }

    /**
     * @inheritdoc
     * @return Service|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return Service[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @param $sort
     * @return $this
     */
    public function order($sort)
    {
        return $this->addOrderBy([Service::tableName() . '.ordering' => $sort]);
    }
}
