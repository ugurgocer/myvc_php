<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-27
 * Time: 00:11
 */

namespace App\Controller;


use App\Model\MealTypes as MTModel;
use App\Helpers;

class MealTypes
{
    public static function addTypes($user_id, $option){
        $caloryWeight = Helpers::kgToCalories($option['weight']);
        $caloryTWeight = Helpers::kgToCalories($option['target_weight']);
        $basalM = Helpers::basalMetabolism($option['gender'], $option['weight'], $option['height'], $option['age']);
        $grandCalory = (($caloryTWeight - $caloryWeight) / 365) + $basalM;

        $calory = $grandCalory / 10;

        try{
            (new MTModel())->setTypes($user_id, $calory);
        }catch (\Exception $e){
            throw $e;
        }
    }

    public static function readTypes($token){
        try{

        }catch (\Exception $e){

        }
    }
}