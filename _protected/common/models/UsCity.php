<?php

namespace common\models;


use common\helpers\ArrayHelper;

/**
 * Class UsCity
 * @package common\models
 *
 * @property array $counties
 */
class UsCity extends base\UsCity
{
    /**
     * Returns all state for drop down
     * @return array
     */
    public static function getStates()
    {
        return ArrayHelper::map(self::find()->states()->all(), 'state_code', 'state_name');
    }

    /**
     * Returns counties of given state for drop down
     * @param string $state_code By default CA state
     * @return array
     */
    public static function getCounties($state_code = null)
    {
        return ArrayHelper::map(self::find()->counties($state_code)->all(), 'county_code', 'county_name');
    }

    /**
     * Returns cities of given state for drop down
     * @param string $state_code By default CA state
     * @return array
     */
    public static function getStateCities($state_code = null)
    {
        return ArrayHelper::map(self::find()->stateCities($state_code)->all(), 'id', 'city_name');
    }

    /**
     * Returns cities of given county for drop down
     * @param null|string|array $county_code By Default LA county
     * @return array
     */
    public static function getCountyCities($county_code = null)
    {
        return ArrayHelper::map(self::find()->countyCities($county_code)->all(), 'id', 'city_name');
    }

    /**
     * Returns state name from code
     * @param string $state_code
     * @return array|string
     */
    public static function getStateName($state_code)
    {
        $data = '';
        if (!empty($state_code)) {
            $states = self::getStates();
            $data = isset($states[$state_code]) ? $states[$state_code] : $state_code;
        }
        return $data;
    }

    /**
     * Returns county name from code
     * @param string $county_code
     * @return array|string
     */
    public static function getCountyName($county_code)
    {
        $data = '';
        if (!empty($county_code)) {
            $county = self::find()->where(['county_code' => $county_code])->one();
            $data = !empty($county) ? $county->county_name : '';
        }
        return $data;
    }

    /**
     * Returns city name from code
     * @param string $city_code
     * @return array|string
     */
    public static function getCityName($city_code)
    {
        $data = '';
        if (!empty($city_code)) {
            $cities = self::find()->where(['id' => $city_code])->one();
            $data = !empty($cities) ? $cities->city_name : '';
        }
        return $data;
    }

}