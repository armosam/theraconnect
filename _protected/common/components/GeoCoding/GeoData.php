<?php

namespace common\components\GeoCoding;


/**
 * This class overrides existing framework of Here for Yii
 * url: https://www.here.com/products/location-based-services/geocoding-tools
 *
 * Class GeoCoding
 * @package common\components
 *
 */
class GeoData
{
    /**
     * @var string
     */
    public $latitude;

    /**
     * @var string
     */
    public $longitude;

    /**
     * @var string
     */
    public $address;

    /**
     * @var string
     */
    public $countryCode;

    /**
     * @var string
     */
    public $countryName;

    /**
     * @var string
     */
    public $stateCode;

    /**
     * @var string
     */
    public $stateName;

    /**
     * @var string
     */
    public $cityName;

    /**
     * @var string
     */
    public $postalCode;

    /**
     * @var string
     */
    public $timeZone;

}