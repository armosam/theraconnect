<?php

namespace common\widgets\ISO639;

/**
 * Class Continent for continent names by ISO639
 * @package common\widgets\ISO639
 */
class Continent {

    /**
     * Returns all data
     * @return array
     */
    public static function all(): array
    {
        $list = require(__DIR__. '/data/continent_data.php');
        $continents = [];
        foreach ($list as $code => $name){
            $continents[strtoupper(trim($code))] = $name;
        }
        return $continents;
    }

    /**
     * @inheritDoc
     */
    public static function allEnglish(): array
    {
        return self::all();
    }

    /**
     * @inheritDoc
     */
    public static function englishNameByCode($code): string
    {
        return self::allEnglish()[strtoupper(trim($code))] ?? '';
    }

}