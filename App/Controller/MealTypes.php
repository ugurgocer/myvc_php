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
use App\Core\Model;

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

    public static function editTypes($user_id, $option){
        $caloryWeight = Helpers::kgToCalories($option['weight']);
        $caloryTWeight = Helpers::kgToCalories($option['target_weight']);
        $basalM = Helpers::basalMetabolism($option['gender'], $option['weight'], $option['height'], $option['age']);
        $grandCalory = (($caloryTWeight - $caloryWeight) / 365) + $basalM;

        $calory = $grandCalory / 10;

        try{
            (new MTModel())->updateTypes($user_id, $calory);
        }catch (\Exception $e){
            throw $e;
        }
    }

    public static function readTypes($token)
    {
        try {
            $user_id = (new Model())->isUsable($token);

            $result = (new MTModel())->getTypes($user_id);

            if(!count($result))
                return print_r(json_encode(['success' => false, 'result' => $result, 'message' => 'Öğün tipi bulunamadı.']));
            foreach ($result as $k => $value) {
                foreach ($value as $key => $item)
                    if ($key === 'calories') {
                        $value[$key] = $item . ' kcal';
                    }
                $result[$k] = $value;
            }

            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Öğün tipleri başarıyla getirildi.']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}