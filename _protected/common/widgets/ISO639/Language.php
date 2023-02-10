<?php

namespace common\widgets\ISO639;

/**
 * Class Language for Language names by ISO639
 * @package common\widgets\ISO639
 */
class Language implements Iso639Interface
{
    /**
     * @inheritDoc
     */
    public static function all($field = null): array
    {
        $list = require(__DIR__. '/data/language_data.php');
        $languages = [];
        foreach ($list as $code => $data){
            if (empty($field)){
                $languages[strtolower(trim($code))] = $data;
            }else{
                $languages[strtolower(trim($code))] = $data[$field];
            }
        }
        return $languages;
    }

    /**
     * @inheritDoc
     */
    public static function allEnglish(): array
    {
        $data = self::all('name');
        asort($data);
        return $data;
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
    public static function englishNameByCode($code): string
    {
        return self::allEnglish()[strtolower(trim($code))] ?? '';
    }

    /**
     * @inheritDoc
     */
    public static function nativeNameByCode($code): string
    {
        return self::allNative()[strtolower(trim($code))] ?? '';
    }
}