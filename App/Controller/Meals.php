<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 27.12.2018
 * Time: 11:13
 */

namespace App\Controller;

use App\Core\Model;
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
            $user_id = (new Model())->isUsable($token);

            $validate->setItem($inputOption['meal_types_id'], 'Öğün Türü ID')->Required()->integer()->min(1)->run();
            foreach ($meal_foods as $item){
                $validate->setItem($item['food_id'], 'Yemek ID')->Required()->integer()->min(1)->run();
                $validate->setItem($item['amount'], 'Miktar')->Required()->integer()->min(1)->run();
            }

            $result = (new MealsModel())->insertMeal($user_id, $inputOption, $meal_foods);

            return print_r(json_encode(['success' => true, 'result'=>$result, 'message' => 'Öğün başarıyla kaydedildi']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function mealPageWithDate($token, $date){
        try{
            $user_id = (new Model())->isUsable($token);

            $result = (new MealsModel())->getMealsWithDate($user_id, $date);

            if(count($result) === 0)
                return print_r(json_encode(['success' => false, 'result' => $result, 'message' => 'Öğün bulunamadı.']));
            foreach ($result as $k => $value) {
                foreach ($value as $key => $item) {
                    if ($key === 'calories' || $key === 'total_calories') {
                        $value[$key] = $item . ' kcal';
                    }else if($key === 'foods' && count($value['foods'])){
                        foreach ($value['foods'] as $ke => $food){
                            $food['porsiyon'] .= $food['porsiyon_unit'];
                            $food['kalori'] .= ' kcal';

                            unset($food['porsiyon_unit']);
                            $value['foods'][$ke] = $food;
                        }
                    }
                }
                $result[$k] = $value;
            }

            return print_r(json_encode(['success' => true, "result" => $result, 'message' => 'Öğünler başarıyla getirildi']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function editMeal($token, $option){
        try{
            $validate = new Validation();
            $meal_foods = [];
            if(!count(json_decode($option['foods'],true)))
                return print_r(json_encode(['success'=> false, 'error' => 'Yemek Listesinde en azından bir yemek bulunmalıdır']));
            foreach (json_decode($option['foods'],true) as $item)
                $meal_foods[] = Helpers::inputFormat($item);
            unset($option['foods']);
            $inputOption = Helpers::inputFormat($option);

            try{
                $user_id = (new Model())->isUsable($token);
                $validate->setItem($inputOption['meal_id'], 'Öğün ID')->Required()->integer()->min(1)->run();
                foreach ($meal_foods as $item){
                    $validate->setItem($item['food_id'], 'Yemek ID')->Required()->integer()->min(1)->run();
                    $validate->setItem($item['amount'], 'Miktar')->Required()->integer()->min(0)->run();
                }

                (new MealsModel())->updateMeals($user_id, $inputOption, $meal_foods);

                return print_r(json_encode(['success' => true, 'message' => 'Öğün başarıyla kaydedildi']));
            }catch (\Exception $e){
                print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
            }
        }catch (\Exception $e){
            print_r(json_encode(['success'=> false, 'error' => $e->getMessage()]));
        }
    }

    public static function readMeal($token, $meal_id){
        try{
            $user_id = (new Model())->isUsable($token);

            $result = (new MealsModel())->getMeal($user_id, $meal_id);

            if(count($result['foods']) === 0)
                return print_r(json_encode(['success' => false, 'result' => $result, 'message' => 'Öğün bulunamadı.']));

            foreach ($result as $key => $item) {
                    if ($key === 'calories' || $key === 'total_calories') {
                        $result[$key] = $item . ' kcal';
                    }else if($key === 'foods' && count($result['foods'])){
                        foreach ($result['foods'] as $ke => $food){
                            $food['porsiyon'] .= $food['porsiyon_unit'];
                            $food['kalori'] .= ' kcal';

                            unset($food['porsiyon_unit']);
                            $result['foods'][$ke] = $food;
                        }
                    }
                }

            return print_r(json_encode(['success' => true, "result" => $result, 'message' => 'Öğün başarıyla getirildi']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}