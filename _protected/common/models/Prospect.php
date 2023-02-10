<?php

namespace common\models;

use Yii;
use Exception;
use common\helpers\GeoDataHelper;
use common\widgets\ISO639\Language;

/**
 * Class Prospect
 * @package common\models
 *
 * @property string|array $statusList
 * @property string $prospectFullName
 * @property string $prospectAddress
 */
class Prospect extends base\Prospect
{
    /**
     * Setting some attributes by default before insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     * @throws Exception
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            if ($insert) {

                if(empty($this->ip_address)) {
                    if (!empty(Yii::$app->request->userIP)) {
                        $this->setAttribute('ip_address', Yii::$app->request->userIP);
                    } else {
                        $this->setAttribute('ip_address', filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP));
                    }
                }

                $locationData = GeoDataHelper::getLocationFromIPAddress($this->getAttribute('ip_address'));
                $this->setAttributes([
                    'timezone' => $this->timezone ?? $locationData->timeZone,
                    'city' => $this->city ?? $locationData->cityName,
                    'state' => $this->state ?? $locationData->stateCode,
                    'country' => $this->country ?? $locationData->countryName,
                    'zip_code' => $this->zip_code ?? $locationData->postalCode
                ]);

                $locationData = GeoDataHelper::getLocationFromAddress($this->address.' '.$this->city.', '.$this->state.' '.$this->zip_code);
                if(!empty($locationData->latitude)){
                    $this->setAttribute('lat', round($locationData->latitude, 6));
                }
                if(!empty($locationData->longitude)){
                    $this->setAttribute('lng', round($locationData->longitude, 6));
                }
            }

            return true;
        }
        return false;
    }

    /**
     * Returns the possible values of status fields.
     *
     * @param null|string $selected
     * @return array|string Array of possible states of status.
     */
    public static function getStatusList($selected = null)
    {
        $data = [
            self::PROSPECTIVE_STATUS_PENDING => Yii::t('app', 'Pending'),
            self::PROSPECTIVE_STATUS_ACCEPTED => Yii::t('app', 'Accepted'),
            self::PROSPECTIVE_STATUS_REJECTED => Yii::t('app', 'Rejected')
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns prospect full name
     * @return string
     */
    public function getProspectFullName()
    {
        $fullName = '';

        if (!empty($this->id)){
            if ( !empty($this->first_name)){
                $fullName .= trim($this->first_name);
            }
            if(!empty($this->last_name)) {
                $fullName .= ' ';
                $fullName .= trim($this->last_name);
            }
        }

        return trim($fullName);
    }

    /**
     * Returns prospect city address
     * @return string
     */
    public function getProspectAddress()
    {
        $result = !empty($this->address) ? $this->address.' ': '';
        $result .= !empty($this->city) ? $this->city.', ': '';
        $result .= !empty($this->state) ? $this->state: '';
        $result .= !empty($this->zip_code) ? (' '.$this->zip_code) : '';
        $result .= !empty($this->country) ? (', '.$this->country) : '';
        return trim($result, ', ');
    }

    /**
     * Returns comma separated languages
     * @return string
     */
    public function getProspectLanguage ()
    {
        $result = null;
        if (is_array($this->language)) {
            $tmp = [];
            foreach ($this->language as $language) {
                $tmp[] = Language::englishNameByCode($language);
            }
            $result = implode(', ', $tmp);
        }
        return $result;
    }

    /**
     * Returns comma separated county coverage
     * @return string
     */
    public function getProspectCoveredCounty ()
    {
        $result = null;
        if (is_array($this->covered_county)) {
            $tmp = [];
            foreach ($this->covered_county as $covered_county) {
                $tmp[] = UsCity::getCountyName($covered_county);
            }
            $result = implode(', ', $tmp);
        }
        return $result;
    }

    /**
     * Returns comma separated city coverage
     * @return string
     */
    public function getProspectCoveredCity ()
    {
        $result = null;
        if (is_array($this->covered_city)) {
            $tmp = [];
            foreach ($this->covered_city as $covered_city) {
                $tmp[] = UsCity::getCityName($covered_city);
            }
            $result = implode(', ', $tmp);
        }
        return $result;
    }

    /**
     * Returns true if application is pending
     * @return bool
     */
    public function isPending()
    {
        return (empty($this->status) || $this->status === Prospect::PROSPECTIVE_STATUS_PENDING);
    }

    /**
     * Returns true if application is accepted
     * @return bool
     */
    public function isAccepted()
    {
        return (empty($this->status) || $this->status === Prospect::PROSPECTIVE_STATUS_ACCEPTED);
    }

    /**
     * Returns true if application is rejected
     * @return bool
     */
    public function isRejected()
    {
        return (empty($this->status) || $this->status === Prospect::PROSPECTIVE_STATUS_REJECTED);
    }

}