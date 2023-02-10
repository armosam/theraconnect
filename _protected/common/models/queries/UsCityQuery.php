<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\UsCity;

/**
 * This is the ActiveQuery class for [[\common\models\UsCity]].
 *
 * @see \common\models\UsCity
 */
class UsCityQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UsCity[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UsCity|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * Returns all states
     * @return UsCityQuery
     */
    public function states()
    {
        return $this->select(['state_code', 'state_name'])->groupBy(['state_code', 'state_name'])->orderBy('state_name');
    }

    /**
     * Returns all counties or counties of given state
     * @param null|string|array $state_code
     * @return UsCityQuery
     */
    public function counties($state_code = null)
    {
        $query = $this->select(['county_code', 'county_name']);
        if(!empty($state_code)) {
            if(is_array($state_code)) {
                array_walk($state_code, function (&$val, $key) {
                    $val = strtoupper($val);
                });
            } elseif(is_scalar($state_code)) {
                $state_code = [strtoupper($state_code)];
            }
            $query->andWhere(['[[state_code]]' => $state_code]);
        }
        return $query->groupBy(['county_code', 'county_name'])->orderBy('county_name');
    }

    /**
     * Returns all cities or cities of given state
     * @param null|string|array $state_code
     * @return UsCityQuery
     */
    public function stateCities($state_code = null)
    {
        $query = $this->select(['id', 'city_name']);
        if(!empty($state_code)) {
            if(is_array($state_code)) {
                array_walk($state_code, function (&$val, $key) {
                    $val = strtoupper($val);
                });
            } elseif(is_scalar($state_code)) {
                $state_code = [strtoupper($state_code)];
            }
            $query->andWhere(['[[state_code]]' => $state_code]);
        }
        return $query->groupBy(['id', 'city_name'])->orderBy('city_name');
    }

    /**
     * Returns all cities or cities of given county
     * @param null|string|array $county_code
     * @return UsCityQuery
     */
    public function countyCities($county_code = null)
    {
        $query = $this->select(['id', 'city_name']);
        if(!empty($county_code)) {
            $query->andWhere(['[[county_code]]' => $county_code]);
        }
        return $query->groupBy(['id', 'city_name'])->orderBy('city_name');
    }
}
