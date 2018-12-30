<?php

namespace App\Controller;

use App\Core\Model;
use App\Helpers;
use App\Core\Validation;
use App\Model\User as UserModel;
use App\Controller\MealTypes as MTController;

class User {
    public static function register($option){
        $inputOption = Helpers::inputFormat($option);
        @$inputOption['gender'] = @boolval($inputOption['gender']);
        $validate = new Validation();

        try{
            $validate->setItem(@$inputOption['name'], 'Ad')->Required()->string()->min(3)->max(50)->run();
            $validate->setItem(@$inputOption['username'], 'Kullanıcı Adı')->Required()->string()->alphaNumeric()->min(5)->max(30)->run();
            $validate->setItem(@$inputOption['surname'], 'Soyad')->Required()->string()->min(2)->max(100)->run();
            $validate->setItem(@$inputOption['email'], 'E-Posta')->Required()->email();
            $validate->setItem(@$inputOption['height'], 'Boy')->Required()->integer()->min(100)->max(250)->run();
            $validate->setItem(@$inputOption['weight'], 'Kilo')->Required()->integer()->min(25)->run();
            $validate->setItem(@$inputOption['target_weight'], 'Hedef Kilo')->Required()->integer()->min(25)->run();
            $validate->setItem(@$inputOption['gender'], 'Cinsiyet')->Required()->boolean()->run();
            $validate->setItem(@$inputOption['age'], 'Yaş')->Required()->integer()->min(13)->run();
            $validate->setItem(@$inputOption['password'],'Şifre')->Required()->string()->min(8)->max(25)->run();

            try{
                $inputOption['password'] = md5($inputOption['password']);
                $sonuc = (new UserModel())->createUser($inputOption);

                MTController::addTypes($sonuc['user_id'], $inputOption);

                return print_r(json_encode(['success'=>true, 'result' => $sonuc, 'message' => 'Kullanıcı kaydı başarıyla tamamlandı.']));
            }catch (\PDOException $e){
                if($e->errorInfo[0] == 23000){
                    return print_r(json_encode(['success'=>false, 'error' => "Bu kullanıcı adı kullanılıyor."]));
                }
                return print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
            }

        }catch (\Exception $e){
            print_r(json_encode(['success'=>false, 'error' => $e->getMessage()]));
        }
    }

    public static function login($option){
        $inputOption = Helpers::inputFormat($option);

        $validate = new Validation();

        try{
            $validate->setItem($inputOption['username'], 'Kullanıcı Adı')->Required()->string()->alphaNumeric()->min(5)->max(30)->run();
            $validate->setItem($inputOption['password'], 'Şifre')->Required()->string()->min(8)->max(25)->run();

            try{
                $inputOption['password'] = md5($inputOption['password']);
                $sonuc = (new UserModel())->loginUser($inputOption);

                return print_r(json_encode(['success'=>true, 'result' => $sonuc, 'message' => 'Giriş işlemi başarılı']));
            }catch (\PDOException $e){
                if($e->getCode() == 330320){
                    return print_r(json_encode(['success'=>false, 'error' => $e->getMessage()]));
                }else if($e->getCode() == 2002){
                    return print_r(json_encode(['success'=>false, 'error' => 'Bağlantı yok.']));
                }
            }

        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function logout($option){
        $user_id = $option['id'];

        try{
            (new UserModel())->deleteToken($user_id);

            return print_r(json_encode(['success'=>true, 'message' => 'Hesabınızdan çıkış yapıldı.']));
        }catch (\PDOException $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function userPage($token){
        try{
            $user_id = (new Model())->isUsable($token);

            $result = (new UserModel())->userRead($user_id);

            return print_r(json_encode(['success'=>true, 'result' => $result, 'message' => 'Bilgileriniz başarıyla getirildi.']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function editUser($token, $option){
        $inputOption = Helpers::inputFormat($option);
        @$inputOption['gender'] = @boolval($inputOption['gender']);
        $validate = new Validation();

        try{
            $user_id = (new Model())->isUsable($token);

            $validate->setItem(@$inputOption['name'], 'Ad')->Required()->string()->min(3)->max(50)->run();
            $validate->setItem(@$inputOption['username'], 'Kullanıcı Adı')->Required()->string()->alphaNumeric()->min(5)->max(30)->run();
            $validate->setItem(@$inputOption['surname'], 'Soyad')->Required()->string()->min(2)->max(100)->run();
            $validate->setItem(@$inputOption['email'], 'E-Posta')->Required()->email();
            $validate->setItem(@$inputOption['height'], 'Boy')->Required()->integer()->min(100)->max(250)->run();
            $validate->setItem(@$inputOption['weight'], 'Kilo')->Required()->integer()->min(25)->run();
            $validate->setItem(@$inputOption['target_weight'], 'Hedef Kilo')->Required()->integer()->min(25)->run();
            $validate->setItem(@$inputOption['gender'], 'Cinsiyet')->Required()->boolean()->run();
            $validate->setItem(@$inputOption['age'], 'Yaş')->Required()->integer()->min(13)->run();

            try{
                $sonuc = (new UserModel())->userUpdate($user_id, $inputOption);

                MTController::editTypes($user_id, $inputOption);

                return print_r(json_encode(['success'=>true, 'result' => $sonuc, 'message' => 'Kullanıcı bilgileri başarıyla değiştirildi.']));
            }catch (\PDOException $e){
                if($e->errorInfo[0] == 23000){
                    return print_r(json_encode(['success'=>false, 'error' => "Bu kullanıcı adı kullanılıyor."]));
                }
                return print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
            }
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}