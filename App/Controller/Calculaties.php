<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-29
 * Time: 22:32
 */

namespace App\Controller;

use App\Core\Validation;
use App\Helpers;
use App\Core\Model;

class Calculaties
{
    public static function getBM($token, $option)
    {
        $validate = new Validation();
        $inputOption = Helpers::inputFormat($option);
        @$inputOption['gender'] = boolval($inputOption['gender']);
        try {
            (new Model)->isUsable($token);

            $validate->setItem(@$inputOption['height'], 'Boy')->Required()->integer()->min(100)->max(250)->run();
            $validate->setItem(@$inputOption['weight'], 'Kilo')->Required()->integer()->min(25)->run();
            $validate->setItem(@$inputOption['gender'], 'Cinsiyet')->Required()->boolean()->run();
            $validate->setItem(@$inputOption['age'], 'Yaş')->Required()->integer()->min(13)->run();

            $result = Helpers::basalMetabolism(
                $inputOption['gender'],
                $inputOption['weight'],
                $inputOption['height'],
                $inputOption['age']
            );

            $inputOption['basal-metabolism'] = $result;
            $inputOption['gender'] = $inputOption['gender'] ? 'Erkek' : 'Kadın';
            return print_r(json_encode(['success' => true, 'result' => $inputOption, 'message' => 'Bazal Metabolizma hesaplandı']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function getIW($token, $option)
    {
        $validate = new Validation();
        $inputOption = Helpers::inputFormat($option);
        @$inputOption['gender'] = boolval($inputOption['gender']);
        try {
            (new Model)->isUsable($token);

            $validate->setItem(@$inputOption['height'], 'Boy')->Required()->integer()->min(100)->max(250)->run();
            $validate->setItem(@$inputOption['gender'], 'Cinsiyet')->Required()->boolean()->run();

            $result = Helpers::idealWeight(
                $inputOption['gender'],
                $inputOption['height']
            );

            $inputOption['ideal-weight'] = $result;
            $inputOption['gender'] = $inputOption['gender'] ? 'Erkek' : 'Kadın';
            return print_r(json_encode(['success' => true, 'result' => $inputOption, 'message' => 'İdeal Kilo hesaplandı']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function getBMI($token, $option)
    {
        $validate = new Validation();
        $inputOption = Helpers::inputFormat($option);
        @$inputOption['gender'] = boolval($inputOption['gender']);
        try {
            (new Model)->isUsable($token);

            $validate->setItem(@$inputOption['height'], 'Boy')->Required()->integer()->min(100)->max(250)->run();
            $validate->setItem(@$inputOption['weight'], 'Kilo')->Required()->integer()->min(25)->run();

            $result = Helpers::bodyMassIndex(
                $inputOption['height'],
                $inputOption['weight']
            );

            $inputOption['body-mass-index'] = $result;
            return print_r(json_encode(['success' => true, 'result' => $inputOption, 'message' => 'Boy Kilo Endeksi Hesaplandı']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function getKg2KCal($token, $option)
    {
        $validate = new Validation();
        $inputOption = Helpers::inputFormat($option);
        @$inputOption['gender'] = boolval($inputOption['gender']);
        try {
            (new Model)->isUsable($token);

            $validate->setItem(@$inputOption['weight'], 'Kilo')->Required()->integer()->min(25)->run();

            $result = Helpers::kgToCalories(
                $inputOption['weight']
            );

            $inputOption['calories'] = $result;
            return print_r(json_encode(['success' => true, 'result' => $inputOption, 'message' => 'Kilonuz kalori değerine çevirildi']));
        } catch (\Exception $e) {
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}