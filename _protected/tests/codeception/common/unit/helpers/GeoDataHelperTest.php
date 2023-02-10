<?php

namespace _protected\tests\codeception\common\unit\helpers;

use Yii;
use Codeception\Stub;
use Codeception\Specify;
use common\helpers\GeoDataHelper;
use tests\codeception\common\unit\TestCase;

/**
 * Class GeoDataHelperTest
 * @group GeoDataHelperTest
 * @package _protected\tests\codeception\common\unit\helpers
 */
class GeoDataHelperTest extends TestCase
{
    use Specify;

    public function testGetLocationByLocalIPAddress()
    {
        $this->specify('perform getting geo location from real IP address', function(){
            $armIP = '127.0.0.1';
            $geoData = GeoDataHelper::getLocationFromIPAddress($armIP);
            expect('ensure returned geo data object has latitude attribute', $geoData)->hasAttribute('latitude');
            expect('ensure returned geo data object has longitude attribute', $geoData)->hasAttribute('longitude');
            expect('ensure returned geo data object has address attribute', $geoData)->hasAttribute('address');
            expect('ensure returned geo data object has countryCode attribute', $geoData)->hasAttribute('countryCode');
            expect('ensure returned geo data object has countryName attribute', $geoData)->hasAttribute('countryName');
            expect('ensure returned geo data object has stateCode attribute', $geoData)->hasAttribute('stateCode');
            expect('ensure returned geo data object has stateName attribute', $geoData)->hasAttribute('stateName');
            expect('ensure returned geo data object has cityName attribute', $geoData)->hasAttribute('cityName');
            expect('ensure returned geo data object has postalCode attribute', $geoData)->hasAttribute('postalCode');

            expect('ensure returned geo data object has correct latitude', $geoData->latitude)->stringContainsString('34.052235');
            expect('ensure returned geo data object has correct longitude', $geoData->longitude)->stringContainsString('-118.243683');
            expect('ensure returned geo data object has correct address', $geoData->address)->stringContainsString('Los Angeles, CA 90017');
            expect('ensure returned geo data object has correct country code', $geoData->countryCode)->stringContainsString('US');
            expect('ensure returned geo data object has correct country name', $geoData->countryName)->stringContainsString('United States');
            expect('ensure returned geo data object has correct state code', $geoData->stateCode)->stringContainsString('CA');
            expect('ensure returned geo data object has correct state name', $geoData->stateName)->stringContainsString('California');
            expect('ensure returned geo data object has correct city name', $geoData->cityName)->stringContainsString('Los Angeles');
            expect('ensure returned geo data object has correct postal code', $geoData->postalCode)->stringContainsString('90017');
            expect('ensure returned geo data object has correct time zone', $geoData->timeZone)->stringContainsString('America/Los_Angeles');
        });
    }

    public function testGetLocationByRealIPAddress()
    {
        $this->specify('perform getting geo location from existing real IP address', function(){
            $armIP = '104.200.171.28';
            $geoData = GeoDataHelper::getLocationFromIPAddress($armIP);
            expect('ensure returned geo data object has latitude attribute', $geoData)->hasAttribute('latitude');
            expect('ensure returned geo data object has longitude attribute', $geoData)->hasAttribute('longitude');
            expect('ensure returned geo data object has address attribute', $geoData)->hasAttribute('address');
            expect('ensure returned geo data object has countryCode attribute', $geoData)->hasAttribute('countryCode');
            expect('ensure returned geo data object has countryName attribute', $geoData)->hasAttribute('countryName');
            expect('ensure returned geo data object has stateCode attribute', $geoData)->hasAttribute('stateCode');
            expect('ensure returned geo data object has stateName attribute', $geoData)->hasAttribute('stateName');
            expect('ensure returned geo data object has cityName attribute', $geoData)->hasAttribute('cityName');
            expect('ensure returned geo data object has postalCode attribute', $geoData)->hasAttribute('postalCode');

            expect('ensure returned geo data object has correct latitude', $geoData->latitude)->stringContainsString('34.052235');
            expect('ensure returned geo data object has correct longitude', $geoData->longitude)->stringContainsString('-118.243683');
            expect('ensure returned geo data object has correct address', $geoData->address)->stringContainsString('Los Angeles, CA 90017');
            expect('ensure returned geo data object has correct country code', $geoData->countryCode)->stringContainsString('US');
            expect('ensure returned geo data object has correct country name', $geoData->countryName)->stringContainsString('United States');
            expect('ensure returned geo data object has correct state code', $geoData->stateCode)->stringContainsString('CA');
            expect('ensure returned geo data object has correct state name', $geoData->stateName)->stringContainsString('California');
            expect('ensure returned geo data object has correct city name', $geoData->cityName)->stringContainsString('Los Angeles');
            expect('ensure returned geo data object has correct postal code', $geoData->postalCode)->stringContainsString('90017');
            expect('ensure returned geo data object has correct time zone', $geoData->timeZone)->stringContainsString('America/Los_Angeles');
        });
    }

    public function testGetLocationByRealIPAddressNotFound()
    {
        $this->specify('perform getting geo location from real IP address with missing data', function(){
            $armIP = '46.130.62.207';
            $geoData = GeoDataHelper::getLocationFromIPAddress($armIP);
            expect('ensure returned geo data object has latitude attribute', $geoData)->hasAttribute('latitude');
            expect('ensure returned geo data object has longitude attribute', $geoData)->hasAttribute('longitude');
            expect('ensure returned geo data object has address attribute', $geoData)->hasAttribute('address');
            expect('ensure returned geo data object has countryCode attribute', $geoData)->hasAttribute('countryCode');
            expect('ensure returned geo data object has countryName attribute', $geoData)->hasAttribute('countryName');
            expect('ensure returned geo data object has stateCode attribute', $geoData)->hasAttribute('stateCode');
            expect('ensure returned geo data object has stateName attribute', $geoData)->hasAttribute('stateName');
            expect('ensure returned geo data object has cityName attribute', $geoData)->hasAttribute('cityName');
            expect('ensure returned geo data object has postalCode attribute', $geoData)->hasAttribute('postalCode');
            expect('ensure returned geo data object has timeZone attribute', $geoData)->hasAttribute('timeZone');

            expect('ensure returned geo data object has correct latitude', $geoData->latitude)->stringContainsString('34.052235');
            expect('ensure returned geo data object has correct longitude', $geoData->longitude)->stringContainsString('-118.243683');
            expect('ensure returned geo data object has correct address', $geoData->address)->stringContainsString('Los Angeles, CA 90017');
            expect('ensure returned geo data object has correct country code', $geoData->countryCode)->stringContainsString('US');
            expect('ensure returned geo data object has correct country name', $geoData->countryName)->stringContainsString('United States');
            expect('ensure returned geo data object has correct state code', $geoData->stateCode)->stringContainsString('CA');
            expect('ensure returned geo data object has correct state name', $geoData->stateName)->stringContainsString('California');
            expect('ensure returned geo data object has correct city name', $geoData->cityName)->stringContainsString('Los Angeles');
            expect('ensure returned geo data object has correct postal code', $geoData->postalCode)->stringContainsString('90017');
            expect('ensure returned geo data object has correct time zone', $geoData->timeZone)->stringContainsString('America/Los_Angeles');
        });
    }

    public function testGetIPCountryCode()
    {
        $this->assertEquals('US', GeoDataHelper::getIPCountryCode());
        $_SERVER['HTTP_CF_IPCOUNTRY'] = 'AM';
        $this->assertEquals('AM', GeoDataHelper::getIPCountryCode());
    }

    public function testGetLocationFromAddress()
    {
        $this->specify('perform getting geo location data from address string', function(){
            $address = '2705 W 235th street Apt D Torrance, CA 90505';
            $geoData = GeoDataHelper::getLocationFromAddress($address);
            expect('ensure returned geo data object has latitude attribute', $geoData)->hasAttribute('latitude');
            expect('ensure returned geo data object has longitude attribute', $geoData)->hasAttribute('longitude');
            expect('ensure returned geo data object has address attribute', $geoData)->hasAttribute('address');
            expect('ensure returned geo data object has countryCode attribute', $geoData)->hasAttribute('countryCode');
            expect('ensure returned geo data object has countryName attribute', $geoData)->hasAttribute('countryName');
            expect('ensure returned geo data object has stateCode attribute', $geoData)->hasAttribute('stateCode');
            expect('ensure returned geo data object has stateName attribute', $geoData)->hasAttribute('stateName');
            expect('ensure returned geo data object has cityName attribute', $geoData)->hasAttribute('cityName');
            expect('ensure returned geo data object has postalCode attribute', $geoData)->hasAttribute('postalCode');

            expect('ensure returned geo data object has correct latitude', $geoData->latitude)->stringContainsString('33.8141');
            expect('ensure returned geo data object has correct longitude', $geoData->longitude)->stringContainsString('-118.33394');
            expect('ensure returned geo data object has correct address', $geoData->address)->stringContainsString('2705 W 235th St, Torrance, CA 90505, United States');
            expect('ensure returned geo data object has correct country code', $geoData->countryCode)->stringContainsString('US');
            expect('ensure returned geo data object has correct country name', $geoData->countryName)->stringContainsString('United States');
            expect('ensure returned geo data object has correct state code', $geoData->stateCode)->stringContainsString('CA');
            expect('ensure returned geo data object has correct state name', $geoData->stateName)->stringContainsString('California');
            expect('ensure returned geo data object has correct city name', $geoData->cityName)->stringContainsString('Torrance');
            expect('ensure returned geo data object has correct postal code', $geoData->postalCode)->stringContainsString('90505');
        });
    }
}