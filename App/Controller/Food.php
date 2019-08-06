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
use App\Core\Model;

class Food
{
    public static function foodPageWithBetweenCalory($token, $min, $max){
        $validate = new Validation();

        try {
            (new Model())->isUsable($token);

            $validate->setItem($min, 'Alt Sınır')->Required()->integer()->min(0)->run();
            $validate->setItem($max, 'Üst Sınır')->Required()->integer()->min($min + 1)->run();

            $result = (new FoodModel())->getFoodWithCaloryInterval($min, $max);

            if(!count($result))
                return print_r(json_encode(['success' => false, 'result' => $result, 'message' => 'Yemek bulunamadı.']));
            foreach ($result as $k => $value) {
                foreach ($value as $key => $item)
                    if ($key === 'kalori'){
                        $value[$key] = $item . ' kcal';
                    }else if($key === 'porsiyon') {
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
            (new Model())->isUsable($token);

            $validate->setItem($inputOption['yiyecek'], 'Yemek Adı')->Required()->string()->min(2)->max(300)->run();
            $validate->setItem($inputOption['kalori'], 'Kalori Miktarı')->Required()->integer()->min(1)->run();

            $inputOption['porsiyon'] = 100;
            $inputOption['porsiyon_unit'] = 'g';

            $result = (new FoodModel())->insertFood($inputOption);

            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Yemek başarıyla kaydedildi']));

        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function searchFood($token, $pattern){
        $validate = new Validation();
        $inputOption = Helpers::inputFormat($pattern);
        try{
            (new Model())->isUsable($token);

            $validate->setItem($inputOption['pattern'], 'Aranan ifade')->Required()->string()->min(2)->run();

            $result = (new FoodModel())->searchFood($inputOption['pattern']);

            if(!count($result))
                return print_r(json_encode(['success' => false, 'result' => $result, 'message' => 'Yemek bulunamadı.']));

            foreach ($result as $k => $value) {
                foreach ($value as $key => $item)
                    if ($key === 'kalori'){
                        $value[$key] = $item . ' kcal';
                    }else if($key === 'porsiyon') {
                        $value[$key] = $item . ' ' . $value[$key . '_unit'];
                        unset($value[$key . '_unit']);
                    }
                $result[$k] = $value;
            }
            
            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Girdiğiniz ifadeyi içeren yemekler getirildi.']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}