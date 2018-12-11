<?php

namespace App;

class Helpers{
    public static function inputFormat($input){
        $result = [];

        foreach($input as $key => &$value){
            $result[$key] = htmlspecialchars(stripcslashes(trim($value)));
        }
        
        return $result;
    }

}