<?php

namespace common\components;

use Yii;
use GeoIp2\Database\Reader;
use GeoIp2\Model\City;
use yii\base\Component;
use Exception;
use common\exceptions\LocationNotFoundException;
use common\components\GeoCoding\GeoData;

/**
 * This class overrides existing framework of Here for Yii
 * url: https://www.here.com/products/location-based-services/geocoding-tools
 *
 * Class GeoCoding
 * @package common\components
 *
 * @property string $app_id
 * @property string $app_code
 * @property string $language
 * @property int $generation
 * @property string $api_url
 * @property array $defaultData
 * @property GeoData $geoData
 *
 */
class GeoCoding extends Component
{
    /**
     * Application ID
     * @var string $app_id
     */
    public $app_id;

    /**
     * Application Code
     * @var string $app_code
     */
    public $app_code;

    /**
     * Language of returned data.
     * If not specified then it will return data in location's language
     * @var string $generation
     */
    public $language = '';

    /**
     * Generation is data structure returned by api
     * 0 is most simple structure of data
     * 9 is current data structure
     * @var string $generation
     */
    public $generation = 0;

    /**
     * @var string $api_url
     */
    public $api_url = 'https://geocoder.api.here.com/6.2/geocode.json';

    /**
     * Default data returned id api failed
     * @var array $defaultData
     */
    public $defaultData = [];

    /**
     * Cached geo data
     * @var GeoData $geoData
     */
    private $_geoData;

    /**
     * Inits default data
     */
    public function init(){
        $this->_geoData = new GeoData();
        if(!empty($this->defaultData) && is_array($this->defaultData)){
            foreach($this->defaultData as $property_name => $property_value){
                if(property_exists($this->_geoData, $property_name)){
                    $this->_geoData->$property_name = $property_value;
                }
            }
        }
        parent::init();
    }

    /**
     * Returns current geo data
     * @return GeoData
     */
    public function getGeoData(){
        return $this->_geoData;
    }

    /**
     * Send http request and return data received
     * @param string $url
     * @return null|array
     */
    protected function _sendRequestToHereApi($url)
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            Yii::debug($response, 'GeoLocation');

            if(empty($result)){
                throw new LocationNotFoundException("Response {$response} from API {$url} is not valid");
            }

        }catch (Exception $e){
            $result = [];
            Yii::error('Getting geo data failed by error: ' . $e->getMessage(), 'GeoLocation');
        }
        return $result;
    }

    /**
     * Maps received data with internal geo data type
     * @param array $data
     * @return GeoData
     */
    protected function _mapHereGeoData(array $data)
    {
        if(isset($data['Response']['View'][0]['Result'][0]['Location']) && !empty($data['Response']['View'][0]['Result'][0]['Location'])){
            $data = $data['Response']['View'][0]['Result'][0]['Location'];
            $this->_geoData->address = $data['Address']['Label'] ?? '';
            $this->_geoData->cityName = $data['Address']['City'] ?? '';
            $this->_geoData->countryCode = $data['Address']['Country'] ?? '';
            $this->_geoData->countryName = $data['Address']['AdditionalData'][0]['value'] ?? '';
            $this->_geoData->postalCode = $data['Address']['PostalCode'] ?? '';
            $this->_geoData->stateCode = $data['Address']['State'] ?? '';
            $this->_geoData->stateName = $data['Address']['AdditionalData'][1]['value'] ?? '';
            $this->_geoData->latitude = $data['DisplayPosition']['Latitude'] ?? '';
            $this->_geoData->longitude = $data['DisplayPosition']['Longitude'] ?? '';
            $this->_geoData->timeZone = $data['Address']['TimeZone'] ?? '';
        }

        return $this->_geoData;
    }

    /**
     * Get Location geo data from address
     * @param $address
     * @return GeoData
     */
    public function locationFromAddress($address)
    {
        $data = [
            'searchtext' => $address,
            'app_id' => $this->app_id,
            'app_code' => $this->app_code,
        ];

        if(!empty($this->language)){
            $data['language'] = $this->language;
        }
        $data['gen'] = $this->generation;

        $query = http_build_query($data);
        $url = $this->api_url . '?' . $query;
        $response = $this->_mapHereGeoData($this->_sendRequestToHereApi($url));

        return $response;
    }

    /**
     * Maps geo IP data with internal geo data type
     *
     * @param City $record
     * @return GeoData
     */
    protected function _mapGeoIPData($record)
    {
        if(
            !empty($record->city->name)
            && !empty($record->mostSpecificSubdivision->isoCode)
            && !empty($record->country->name)
            && !empty($record->country->isoCode)
        ) {

            $this->_geoData->cityName = $record->city->name ?? '';
            $this->_geoData->countryCode = $record->country->isoCode ?? '';
            $this->_geoData->countryName = $record->country->name ?? '';
            $this->_geoData->postalCode = $record->postal->code ?? '';
            $this->_geoData->stateCode = $record->mostSpecificSubdivision->isoCode ?? '';
            $this->_geoData->stateName = $record->mostSpecificSubdivision->name ?? '';
            $this->_geoData->latitude = $record->location->latitude ?? '';
            $this->_geoData->longitude = $record->location->longitude ?? '';
            $this->_geoData->timeZone = $record->location->timeZone ?? '';
            $this->_geoData->address = $this->_geoData->cityName . ', ' . $this->_geoData->stateCode . ' ' . $this->_geoData->postalCode . ', ' . $this->_geoData->countryName;
        }

        return $this->_geoData;
    }

    /**
     * Return geo data from IP address
     *
     * @param $ipAddress
     * @return GeoData
     */
    public function locationFromIPAddress($ipAddress)
    {
        try{
            $reader = new Reader(YII_ENV_TEST ? null : Yii::$app->params['geoIPDataPath']);
            $record = $reader->city($ipAddress);
            $reader->close();
            Yii::debug($record->jsonSerialize(), 'GeoIPLocation');
            return $this->_mapGeoIPData($record);
        }catch(Exception $e){
            Yii::error('Getting geoIP data failed by error: ' . $e->getMessage(), 'GeoIPLocation');
            return $this->_geoData;
        }
    }
}