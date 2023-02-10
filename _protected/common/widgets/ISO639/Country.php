<?php

namespace common\widgets\ISO639;

/**
 * Class Country for country names by ISO639
 * @package common\widgets\ISO639
 */
class Country implements Iso639Interface {

    /**
     * Returns all data or array of specified fields
     * @param null $field
     * @return array
     */
    public static function all($field = null): array
    {
        $list = require(__DIR__. '/data/country_data.php');
        $countries = [];
        foreach ($list as $code => $data){
            if (empty($field)){
                $countries[strtoupper(trim($code))] = $data;
            }else{
                $countries[strtoupper(trim($code))] = $data[$field];
            }
        }
        return $countries;
    }

    /**
     * @inheritDoc
     */
    public static function allNative(): array
    {
        return self::all('native');
    }

    /**
     * @inheritDoc
     */
    public static function allEnglish(): array
    {
        return self::all('name');
    }

    /**
     * @inheritDoc
     */
    public static function englishNameByCode($code): string
    {
        return self::allEnglish()[strtoupper(trim($code))] ?? '';
    }

    /**
     * @inheritDoc
     */
    public static function nativeNameByCode($code): string
    {
        return self::allNative()[strtoupper(trim($code))] ?? '';
    }

    /**
     * Returns phone prefix for country specified by code
     * @param string $code 2 Letter code of country
     * @return string
     */
    public static function phonePrefixByCode($code): string
    {
        return self::all('phone')[strtoupper(trim($code))] ?? '';
    }

    /**
     * Returns continent code for country specified by code
     * @param string $code 2 Letter code of country
     * @return string
     */
    public static function continentByCode($code): string
    {
        return self::all('continent')[strtoupper(trim($code))] ?? '';
    }

    /**
     * Returns capital of country specified by code
     * @param string $code 2 Letter code of country
     * @return string
     */
    public static function capitalByCode($code): string
    {
        return self::all('capital')[strtoupper(trim($code))] ?? '';
    }

    /**
     * Returns a list of currency codes of country specified by code
     * @param string $code 2 Letter code of country
     * @return array
     */
    public static function currenciesByCode($code): array
    {
        $string = self::all('currency')[strtoupper(trim($code))] ?? '';
        return explode(',', $string);
    }

    /**
     * Returns a list of language codes of country specified by code
     * @param string $code 2 Letter code of country
     * @return array
     */
    public static function languagesByCode($code): array
    {
        return self::all('languages')[strtoupper(trim($code))] ?? [];
    }
}