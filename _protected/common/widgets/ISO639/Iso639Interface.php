<?php

namespace common\widgets\ISO639;


interface Iso639Interface {

    /**
     * Returns an array of all native data
     * @param string $field Field name to return array with
     * @return array
     */
    public static function all($field = null):array;

    /**
     * Returns an array of all native data
     * @return array
     */
    public static function allNative():array;

    /**
     * Returns an array of all english data
     * @return array
     */
    public static function allEnglish():array;

    /**
     * Returns an English name of component by given code
     * @param string $code
     * @return string|null
     */
    public static function englishNameByCode($code):string ;

    /**
     * Returns a native name of component by given code
     * @param string $code
     * @return string|null
     */
    public static function nativeNameByCode($code):string;
}