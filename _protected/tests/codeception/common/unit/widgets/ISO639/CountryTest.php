<?php

namespace tests\codeception\common\unit\widgets\ISO639;

use Codeception\Specify;
use common\widgets\ISO639\Country;
use tests\codeception\common\unit\TestCase;

/**
 * Class CountryTest
 * @package tests\codeception\common\unit\widgets\ISO639
 * @group ISO639WidgetTest
 */
class CountryTest extends TestCase
{
    use Specify;

    public function testAllEnglishCountries()
    {
        $this->specify("allEnglish method returns correct data", function () {
            expect('method returns array', Country::allEnglish())->array();
            expect('method returns array with element name', Country::allEnglish())->hasKey('AM');
            expect('method returns array with element name', Country::allEnglish()['AM'])->equals('Armenia');
            expect('method returns array with count ', Country::allEnglish())->count(250);
        });
    }

    public function testAllNativeCountries()
    {
        $this->specify("allNative method returns correct data", function () {
            expect('method returns array', Country::allNative())->array();
            expect('method returns array with element name', Country::allNative())->hasKey('AM');
            expect('method returns array with element name', Country::allNative()['AM'])->equals('Հայաստան');
            expect('method returns array with count ', Country::allNative())->count(250);
        });
    }

    public function testEnglishNameByCode()
    {
        $this->specify("englishByCode method returns correct data", function () {
            expect('method returns string', Country::englishNameByCode('AM'))->string();
            expect('method returns correct english name', Country::englishNameByCode('am'))->equals('Armenia');
        });
    }

    public function testNativeNameByCode()
    {
        $this->specify("nativeByCode method returns correct data", function () {
            expect('method returns string', Country::nativeNameByCode('am'))->string();
            expect('method returns correct native name', Country::nativeNameByCode('AM'))->equals('Հայաստան');
        });
    }

    public function testPhonePrefixByCode()
    {
        $this->specify("phonePrefixByCode method returns correct data", function () {
            expect('phonePrefixByCode method returns string', Country::phonePrefixByCode('am'))->string();
            expect('phonePrefixByCode method returns correct phone prefix', Country::phonePrefixByCode('AM'))->equals('374');
        });
    }

    public function testContinentCodeByCode()
    {
        $this->specify("continentByCode method returns correct data", function () {
            expect('continentByCode method returns string', Country::continentByCode('am'))->string();
            expect('continentByCode method returns correct continent code', Country::continentByCode('AM'))->equals('AS');
        });
    }

    public function testCapitalNameByCode()
    {
        $this->specify("capitalByCode method returns correct data", function () {
            expect('capitalByCode method returns string', Country::capitalByCode('am'))->string();
            expect('capitalByCode method returns correct capital name', Country::capitalByCode('AM'))->equals('Yerevan');
        });
    }

    public function testCurrencyCodesByCode()
    {
        $this->specify("currenciesByCode method returns correct data", function () {
            expect('currenciesByCode method returns array', Country::currenciesByCode('am'))->array();
            expect('currenciesByCode method returns correct currency codes', Country::currenciesByCode('AM'))->equals(['AMD']);
            expect('currenciesByCode method returns correct currency codes', Country::currenciesByCode('ru'))->equals(['RUB']);
            expect('currenciesByCode method returns correct currency codes', Country::currenciesByCode('Ca'))->equals(['CAD']);
        });
    }

    public function testLanguageCodesByCode()
    {
        $this->specify("languagesByCode method returns correct data", function () {
            expect('languagesByCode method returns array', Country::languagesByCode('am'))->array();
            expect('languagesByCode method returns correct language codes', Country::languagesByCode('AM'))->equals(['hy', 'ru']);
            expect('languagesByCode method returns correct language codes', Country::languagesByCode('ru'))->equals(['ru']);
            expect('languagesByCode method returns correct language codes', Country::languagesByCode('Ca'))->equals(['en', 'fr']);
        });
    }
}