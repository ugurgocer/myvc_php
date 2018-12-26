<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-21
 * Time: 00:37
 */

namespace App\Controller;

use App\Helpers;
use App\Model\Food as FoodModel;
use App\Core\Validation;

class Food
{
    public static function foodPageWithCategory($token, $id)
    {
        try {
            $result = (new FoodModel())->getFoodWithCategoryID($token, $id);

            foreach ($result as $k => $value) {
                foreach ($value as $key => $item)
                    if ($key === 'kalori' || $key === 'kilojul') {
                        $value[$key] = $item . ' ' . $value[$key . '_unit'];
                        unset($value[$key . '_unit']);
                    }else if ($key === 'porsiyon'){
                        $indexOf = strpos($value[$key.'_unit'], '(');
                        $value[$key.'_unit'] = substr($value[$key.'_unit'], $indexOf+1, strlen($value[$key.'_unit']) - 2 - $indexOf);
                        $value[$key] = $value[$key]." ".$value[$key.'_unit'];
                        unset($value[$key.'_unit']);
                    }
                $result[$k] = $value;
            }

            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Yemekler başarıyla getirildi.']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function foodPageWithBetweenCalory($token, $min, $max){
        $validate = new Validation();

        try {
            $validate->setItem($min)->Required()->integer()->min(0)->run();
            $validate->setItem($max)->Required()->integer()->min($min + 1)->run();

            $result = (new FoodModel())->getFoodWithCaloryInterval($token, $min, $max);

            foreach ($result as $k => $value) {
                foreach ($value as $key => $item)
                    if ($key === 'kalori' || $key === 'kilojul') {
                        $value[$key] = $item . ' ' . $value[$key . '_unit'];
                        unset($value[$key . '_unit']);
                    }
                $result[$k] = $value;
            }

            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Yemekler başarıyla getirildi.']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function createFood($token, $option){
        $validate = new Validation();
        $inputOption = Helpers::inputFormat($option);

        try{
            $validate->setItem($inputOption['yiyecek'])->Required()->string()->min(2)->max(300)->run();
            $validate->setItem($inputOption['porsiyon'])->Required()->string()->min(1)->max(300)->run();
            $validate->setItem($inputOption['porsiyon_unit'])->Required()->string()->min(1)->max(30)->run();
            $validate->setItem($inputOption['kalori'])->Required()->integer()->string()->min(1)->max(10)->run();
            $validate->setItem(@$inputOption['kilojul'])->integer()->min(1)->max(10)->run();
            $validate->setItem($inputOption['category_id'])->Required()->integer()->min(1)->max(50)->run();

            if(isset($inputOption['kilojul']))
                $inputOption['kilojul_unit'] = 'kJ';

            $result = (new FoodModel())->insertFood($token, $inputOption);

            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Yemek başarıyla kaydedildi']));

        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}