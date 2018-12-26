<?php

namespace App;

class Helpers{
    public static function inputFormat($input)
    {
        $result = [];

        foreach ($input as $key => &$value) {
            $result[$key] = htmlspecialchars(stripcslashes(trim($value)));
        }

        return $result;
    }

    public static function optionToQuery($option){
        $keys = implode(',', array_keys($option));
        $valueKeys = [];
        foreach (array_keys($option) as $k){
            $valueKeys[] = ":".$k;
        }
        $values = implode(', ', array_values($valueKeys));

        return [
            $keys,
            $values
        ];
    }

    public static function optionToUpdate($option){
        $sorgu = [];
        foreach ($option as $key => $value){
            $sorgu[] = $key . "=:".$key;
        }

        return implode(', ', $sorgu);
    }

    public static function basalMetabolism($gender, $weight, $height, $age){
        if(!$gender)
            return (655 + (9.6 * $weight) + (1.8 * $height) - (4.7 * $age));
        return (66.5 + (13.7 * $weight) + (5 * $height) - (6.7 * $age));
    }

    public static function idealWeight($gender, $height){
        if(!$gender)
            return (45.5 + ((2.3 / 2.54) * ($height - 152.4)));
        return (50 + ((2.3 / 2.54) * ($height - 152.4)));
    }

    public static function kgToCalories($weight){
        return $weight * 7716.1791764707;
    }

    public static function bodyMassIndex($height, $weight){
        return $weight / pow(($height / 100), 2);
    }

    public static function urlEncode($s){
        $s = mb_strtolower($s);
        $s = str_replace('ö','oe', $s);
        $s = str_replace('ü', 'ue', $s);
        $s = str_replace('ş', 's', $s);
        $s = str_replace('ç', 'c', $s);
        $s = str_replace('ı', 'i', $s);
        $s = str_replace(' ', '-', $s);
        $s = str_replace('ğ', 'g', $s);

        return $s;
    }
}