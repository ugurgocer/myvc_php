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