<?php

namespace common\models;

/**
 * Class Country
 * @package common\models
 */
class Country extends base\Country
{
    /**
     * Return full calling number with country prefix and number
     * @param string $country_code ISO country code 2 letters
     * @param string $phone_number Phone number without country prefix
     * @return mixed|null  Full Phone number
     */
    public function getFullCallingNumber($country_code, $phone_number){
        $country = self::find()->where(['iso' => $country_code])->one();

        if (empty($country)) {
            return $phone_number;
        } else {
            return $country->phone_prefix.$phone_number;
        }
    }
}
