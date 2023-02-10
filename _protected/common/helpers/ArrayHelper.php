<?php

namespace common\helpers;

use Closure;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\BaseArrayHelper;

/**
 * ArrayHelper provides additional array functionality that you can use in your
 * application. It is extends all Yii's functionality and added some more
 *
 * @author Armen Bablanyan <thera@gmail.com>
 * @since 2.0
 */
class ArrayHelper extends BaseArrayHelper
{

    /**
     * Builds a map (key-value pairs) from a multidimensional array or an array of objects.
     * The `$from` and `$to` parameters specify the key names or property names to set up the map.
     * There is a functionality you can give more fields for $to and it will automatically concat separated by space.
     * Optionally, one can further group the map according to a grouping field `$group`.
     *
     * For example,
     *
     * ```php
     * $array = [
     *     ['id' => '123', 'name' => 'aaa', 'name1' => 'aaa1', 'class' => 'x'],
     *     ['id' => '124', 'name' => 'bbb', 'name1' => 'bbb1', 'class' => 'x'],
     *     ['id' => '345', 'name' => 'ccc', 'name1' => 'ccc1', 'class' => 'y'],
     * ];
     *
     * $result = ArrayHelper::map($array, 'id', 'name');
     * // the result is:
     * // [
     * //     '123' => 'aaa',
     * //     '124' => 'bbb',
     * //     '345' => 'ccc',
     * // ]
     *
     * $result = ArrayHelper::map($array, 'id', 'name,name1,...');
     * // the result is:
     * // [
     * //     '123' => 'aaa aaa1 ...',
     * //     '124' => 'bbb bbb1 ...',
     * //     '345' => 'ccc ccc1 ...',
     * // ]
     *
     * $result = ArrayHelper::map($array, 'id', 'name', 'class');
     * // the result is:
     * // [
     * //     'x' => [
     * //         '123' => 'aaa',
     * //         '124' => 'bbb',
     * //     ],
     * //     'y' => [
     * //         '345' => 'ccc',
     * //     ],
     * // ]
     * ```
     *
     * @param array $array
     * @param string|Closure $from
     * @param string|Closure $to
     * @param string|Closure $group
     * @return array
     */
    public static function map($array, $from, $to, $group = null)
    {
        $result = [];
        foreach ($array as $element) {
            $key = static::getValue($element, $from);
            $to_arr = explode(',', $to);
            if(!empty($to_arr) && is_array($to_arr)){
                $value_arr = array();
                foreach($to_arr as $to_field){
                    $value_arr[] = static::getValue($element, $to_field);
                }
                $value = implode(' ', $value_arr);
            }else{
                $value = static::getValue($element, $to);
            }

            if ($group !== null) {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Returns a number of random elements from an array.
     *
     * It returns the random number (specified in $limit) of elements as new array from $array.
     *
     * @param array $array The array to return the elements from
     * @param int $limit The number of elements to return from the array
     * @param bool $keep_keys If this parameter is true it will save original keys
     * @return array The randomized sub array
     */
    public static function array_random_subset($array, $limit = 1, $keep_keys = false) {

        if ($limit == 0 || !is_array($array) || $limit > count($array)) return array();
        if (count($array) == $limit) return $array;

        $random_keys = array_rand($array, $limit);

        $array_filtered = array();
        foreach ($random_keys as $key => $random_key) {
            $return_key = $key;
            if($keep_keys){
                $return_key = $random_key;
            }
            $array_filtered[$return_key] = $array[$random_key];
        }

        shuffle($array_filtered);

        return $array_filtered;
    }

    /**
     * Formats range presentation from min to max. Like 100.00 - 300.00 or
     * First argument is array of models
     * Second one is field name that has to be calculated for each model
     * If values are equal it will show only max value
     * Currently it formats as currency
     *
     * @param array $models Array of models
     * @param string $field_name field name
     * @return string range of currencies
     * @throws InvalidConfigException
     */
    public static function asRange($models, $field_name)
    {
        $result = array();
        if(!empty($models) && is_array($models)){
            $fees = array();
            foreach ($models as $model) {
                $fees[] = floatval($model->$field_name);
            }

            $min = min($fees);
            $max = max($fees);

            if ($min == $max){
                $result = Yii::$app->formatter->asCurrency($max);
            }else {
                $result = Yii::$app->formatter->asCurrency($min) . ' - ' . Yii::$app->formatter->asCurrency($max);
            }
        }

        return $result;
    }
}
