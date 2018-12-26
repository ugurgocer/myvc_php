<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 20.12.2018
 * Time: 16:09
 */

namespace App\Controller;

use App\Model\FoodCategory as FCModel;

class FoodCategory
{
    public static function categoryPage($token)
    {
        try {
            $result = (new FCModel())->getCategories($token);

            return print_r(json_encode(['success' => true, 'result' => $result, 'message' => 'Kategoriler başarıyla getirildi.']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}