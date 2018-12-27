<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 27.12.2018
 * Time: 11:13
 */

namespace App\Controller;

use App\Helpers;
use App\Core\Validation;
use App\Model\Meals as MealsModel;

class Meals
{
    public static function createMeal($token, $option){
        $validate = new Validation();
        $meal_foods = [];
        foreach (json_decode($option['foods'],true) as $item)
            $meal_foods[] = Helpers::inputFormat($item);
        unset($option['foods']);
        $inputOption = Helpers::inputFormat($option);

        try{
            $validate->setItem($inputOption['meal_types_id'])->Required()->integer()->min(1)->run();
            foreach ($meal_foods as $item){
                $validate->setItem($item['food_id'])->Required()->integer()->min(1)->run();
                $validate->setItem($item['amount'])->Required()->integer()->min(1)->run();
            }

            (new MealsModel())->insertMeal($token, $inputOption, $meal_foods);

            return print_r(json_encode(['success' => true, 'message' => 'Öğün başarıyla kaydedildi']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function mealPageWithDate($token, $date){
        try{
            $result = (new MealsModel())->getMealsWithDate($token, $date);

            foreach ($result as $k => $value) {
                foreach ($value as $key => $item) {
                    if ($key === 'calories' || $key === 'total_calories') {
                        $value[$key] = $item . ' ' . $value['calories_unit'];
                    }else if($key === 'foods' && count($value['foods'])){
                        foreach ($value['foods'] as $ke => $food){
                            $indexOf = strpos($food['porsiyon_unit'], '(');
                            $food['porsiyon'] .= " ".substr($food['porsiyon_unit'], $indexOf+1, strlen($food['porsiyon_unit']) - 2 - $indexOf);
                            $food['kalori'] .= ' kcal';

                            unset($food['porsiyon_unit']);
                            $value['foods'][$ke] = $food;
                        }
                    }
                }
                unset($value['calories_unit']);
                $result[$k] = $value;
            }

            return print_r(json_encode(['success' => true, "result" => $result, 'message' => 'Öğün başarıyla getirildi']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function editMeal($token, $option){

    }
}