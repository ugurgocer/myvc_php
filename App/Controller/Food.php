<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-21
 * Time: 00:37
 */

namespace App\Controller;

use App\Model\Food as FoodModel;

class Food
{
    public static function foodPageWithCategory($token, $id)
    {
        try {
            $result = (new FoodModel())->getFoodWithCategoryID($token, $id);

            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Kategoriler başarıyla getirildi.']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}