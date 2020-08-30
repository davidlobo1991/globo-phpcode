<?php
/**
 * Created by PhpStorm.
 * User: moussa
 * Date: 16/11/17
 * Time: 13:44
 */

namespace App\Helpers;

class Functions
{
    public static function inArray($array = [], $value, $key = 'id')
    {
        for ($i = 0; $i < count($array); $i++) {
            if ($array[$i][$key] == $value) {
                return 1;
            }
        }

        return 0;
    }

    public static function r_collect($array, bool $force = false)
    {
        foreach ($array as $key => $value) {
            if($force and (gettype($value) == 'object')){
                $value = (array)$value;
            }

            if (is_array($value)) {
                $value = self::r_collect($value, $force);
                $array[$key] = $value;
            }
        }

        return collect($array);
    }

    public static function r_json_decode($array, bool $force = false)
    {
        foreach ($array as $key => $value) {
            if($force and (gettype($value) == 'object')){
                $value = (array)$value;
            }
            if (is_array($value)) {
                $value = self::r_json_decode($value, $force);
                $array[$key] = $value;
            }
        }

        return json_decode(collect($array));
    }
}