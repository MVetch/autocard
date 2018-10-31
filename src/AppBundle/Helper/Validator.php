<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 25.09.2018
 * Time: 12:35
 */

namespace AppBundle\Helper;

class Validator
{

    public static function notEmpty($index, $array)
    {
        return array_key_exists($index, $array) && !empty($array[$index]);
    }

    public static function notExistsOrEmpty($index, $array)
    {
        return !array_key_exists($index, $array) || (array_key_exists($index, $array) && empty($array[$index]));
    }

    public static function existsAndMoreOrEqualThan($index, $array, $min)
    {
        return array_key_exists($index, $array) && !is_array($array[$index]) && mb_strlen($array[$index]) >= $min;
    }

    public static function existsAndLessOrEqualThan($index, $array, $max)
    {
        return array_key_exists($index, $array) && !is_array($array[$index]) && mb_strlen($array[$index]) <= $max;
    }

    public static function existsAndInRange($index, $array, $min, $max)
    {
        return static::existsAndMoreOrEqualThan($index, $array, $min) && static::existsAndLessOrEqualThan($index, $array, $max);
    }

}