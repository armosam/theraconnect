<?php

namespace common\models;

use Yii;
use Exception;
use yii\helpers\Html;
use common\widgets\ISO639\Language;
use common\helpers\ConstHelper;

/**
 * Class Patient
 * @package common\models
 *
 * @property string $patientFullName
 * @property string $patientAddress
 * @property string $patientPreferences
 * @property string $patientAge
 */
class Patient extends base\Patient
{
    /**
     * Setting some attributes automatically before an insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            try {
                if($insert === true) {
                    $this->setAttribute('status', ConstHelper::STATUS_ACTIVE);
                }

            }catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Setting some attributes automatically after an insert or update of the table
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Returns patient full name
     * @param bool $show_date_of_birth
     * @return string
     */
    public function getPatientFullName($show_date_of_birth = false)
    {
        $fullName = '';

        if (!empty($this->id)){
            if ( !empty($this->first_name)){
                $fullName .= trim($this->first_name);
            }
            if(!empty($this->middle_name)) {
                $fullName .= ' ';
                $fullName .= trim($this->middle_name);
            }
            if(!empty($this->last_name)) {
                $fullName .= ' ';
                $fullName .= trim($this->last_name);
            }

            if ($show_date_of_birth && !empty($this->birth_date)) {
                $fullName .= ' (' . $this->birth_date . ')';
            }
        }

        return trim($fullName);
    }

    /**
     * Returns patient full address
     * @return string
     */
    public function getPatientAddress()
    {
        $result = !empty($this->address) ? $this->address.' ': '';
        $result .= !empty($this->city) ? $this->city.', ': '';
        $result .= !empty($this->state) ? $this->state: '';
        $result .= !empty($this->zip_code) ? (' '.$this->zip_code) : '';
        $result .= !empty($this->country) ? (', '.$this->country) : '';
        return trim($result, ', ');
    }

    /**
     * Returns patient's preferences like language and gender
     * @return string|null
     */
    public function getPatientPreferences ()
    {
        $data = [];
        if(!empty($this->preferred_language)){
            $data[] = Html::tag('span', Yii::t('app', 'Speaks {lang}', ['lang' => Language::englishNameByCode($this->preferred_language)]), ['class' => 'label label-warning']);
        }
        if(!empty($this->preferred_gender)){
            $data[] = Html::tag('span', Yii::t('app', '{gender} Only', ['gender' => User::getGenderList($this->preferred_gender)]), ['class' => 'label label-warning']);
        }
        return empty($data) ? null : implode(' and ', $data);
    }

    /**
     * Calculates age by given birthday
     * @return string Age
     * @throws Exception
     */
    public function getPatientAge()
    {
        if (empty($this->birth_date) || !ConstHelper::dateTime($this->birth_date)){
            return 'Not Disclosed';
        }
        return (string)ConstHelper::calculateAgeInYears(ConstHelper::dateTime($this->birth_date));
    }

    /**
     * Disables patient
     */
    public function disable()
    {
        $this->setAttribute('status', ConstHelper::STATUS_PASSIVE);
        $this->save(false);
    }

    /**
     * Enables patient
     */
    public function enable()
    {
        $this->setAttribute('status', ConstHelper::STATUS_ACTIVE);
        $this->save(false);
    }
}