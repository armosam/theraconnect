<?php

namespace common\components;

use Yii;
use common\models\Language;
use common\helpers\GeoDataHelper;
use lajax\languagepicker\Component as LanguagePickerComponent;

/**
 * This class overrides existing framework class and adds functionality
 * to detect language by IP address.
 * Class LanguagePicker
 * @package common\components
 */
class LanguagePicker extends LanguagePickerComponent
{
    /**
     * default language to be selected;
     * If this parameter is not set it will detect language by IP address
     *
     * @var string
     */
    public $selectedLanguage = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function detectLanguage()
    {
        if(!empty($this->selectedLanguage)){
            Yii::$app->language = $this->selectedLanguage;
            return;
        }

        //For guests it will detect language by ip address
        if (Yii::$app->user->isGuest) {
            $dataByIPAddress = GeoDataHelper::getLocationFromIPAddress(Yii::$app->request->userIP);
            if (isset($dataByIPAddress->countryName)) {
                $country_name = $dataByIPAddress->countryName;
                $language_code = Language::getLanguageCodeByCountryName($country_name);
                Yii::$app->language = $language_code;
                $this->saveLanguageIntoCookie($language_code);
                return;
            }
        }

        $acceptableLanguages = Yii::$app->getRequest()->getAcceptableLanguages();
        foreach ($acceptableLanguages as $language) {
            if ($this->isValidLanguage($language)) {
                Yii::$app->language = $language;
                $this->saveLanguageIntoCookie($language);
                return;
            }
        }

        foreach ($acceptableLanguages as $language) {
            $pattern = preg_quote(substr($language, 0, 2), '/');
            foreach ($this->languages as $key => $value) {
                if (preg_match('/^' . $pattern . '/', $value) || preg_match('/^' . $pattern . '/', $key)) {
                    Yii::$app->language = $this->isValidLanguage($key) ? $key : $value;
                    $this->saveLanguageIntoCookie(Yii::$app->language);
                    return;
                }
            }
        }
    }

    /**
     * Determines whether the language received as a parameter can be processed.
     * @param string $language
     * @return boolean
     */
    protected function isValidLanguage($language)
    {
        return is_string($language) && (isset($this->languages[$language]) || in_array($language, $this->languages));
    }
}