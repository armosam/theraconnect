<?php

namespace common\helpers;

use Yii;
use common\components\GeoCoding\GeoData;

class GeoDataHelper
{
    /**
     * @param string $address
     * @return GeoData
     */
    public static function getLocationFromAddress($address)
    {
        return Yii::$app->geoCoding->locationFromAddress($address);
    }

    /**
     * Returns GeoIP2 object by given IP address.
     * You can access to city by $obj->city->name or
     * $obj->raw['city']['names']['en'];
     * Also possible $obj->location->longitude and more
     *
     * @param string $ipAddress
     * @return GeoData
     */
    public static function getLocationFromIPAddress($ipAddress)
    {
        //$ipAddress = '46.130.62.207';
        return Yii::$app->geoCoding->locationFromIPAddress($ipAddress);
    }

    /**
     *  This routine calculates the distance between two points (given the
     *  latitude/longitude of those points). It is being used to calculate
     *  the distance between two locations using GeoDataSource(TM) Products
     *
     *  Definitions:
     *    South latitudes are negative, east longitudes are positive
     *
     *  Passed to function:
     *    lat1, lon1 = Latitude and Longitude of point 1 (in decimal degrees)
     *    lat2, lon2 = Latitude and Longitude of point 2 (in decimal degrees)
     *    unit = the unit you desire for results
     *           where: 'M' is statute miles (default)
     *                  'K' is kilometers
     *                  'N' is nautical miles
     *
     * @param string $lat1
     * @param string $lon1
     * @param string $lat2
     * @param string $lon2
     * @param string $unit
     * @return float|int
     */
    public static function getDistanceBetweenTwoPoints($lat1, $lon1, $lat2, $lon2, $unit) {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }

    /**
     * Returns country code if there is set special global variable $_SERVER['HTTP_CF_IPCOUNTRY']
     * Otherwise it will return armenia's code as default
     * Info: $_SERVER['HTTP_CF_IPCOUNTRY'] variable is set by cludflare server;
     *
     * @return string
     */
    public static function getIPCountryCode(){
        if(!empty($_SERVER['HTTP_CF_IPCOUNTRY']) && preg_match('/^[A-Z]{2}$/', $_SERVER['HTTP_CF_IPCOUNTRY'])){
            return $_SERVER['HTTP_CF_IPCOUNTRY'];
        }else{
            return 'US';
        }
    }
}