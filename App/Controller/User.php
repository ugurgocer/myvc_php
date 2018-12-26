<?php

namespace App\Controller;

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
            $validate->setItem(@$inputOption['name'])->Required()->string()->min(3)->max(50)->run();
            $validate->setItem(@$inputOption['username'])->Required()->string()->min(5)->max(30)->run();
            $validate->setItem(@$inputOption['surname'])->Required()->string()->min(2)->max(100)->run();
            $validate->setItem(@$inputOption['email'])->Required()->email();
            $validate->setItem(@$inputOption['height'])->Required()->integer()->min(100)->max(250)->run();
            $validate->setItem(@$inputOption['weight'])->Required()->integer()->min(25)->run();
            $validate->setItem(@$inputOption['target_weight'])->Required()->integer()->min(25)->run();
            $validate->setItem(@$inputOption['gender'])->boolean()->run();
            $validate->setItem(@$inputOption['age'])->Required()->integer()->min(13)->run();
            $validate->setItem(@$inputOption['password'])->Required()->string()->min(8)->max(25)->run();

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
            $validate->setItem($inputOption['username'])->Required()->string()->min(5)->max(30)->run();
            $validate->setItem($inputOption['password'])->Required()->string()->min(8)->max(25)->run();

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
            print_r(json_encode(['success' => false, 'error' => $e->getCode()]));
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
            $result = (new UserModel())->userRead($token);

            return print_r(json_encode(['success'=>true, 'result' => $result, 'message' => 'Bilgileriniz başarıyla getirildi.']));
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }

    public static function editUser($token, $option){
        $inputOption = Helpers::inputFormat($option);
        $validate = new Validation();

        try{
            $validate->setItem(@$inputOption['name'])->string()->min(3)->max(50)->run();
            $validate->setItem(@$inputOption['username'])->string()->min(5)->max(30)->run();
            $validate->setItem(@$inputOption['surname'])->string()->min(2)->max(100)->run();
            $validate->setItem(@$inputOption['email'])->notRequired()->email();

            try{
                $sonuc = (new UserModel())->userUpdate($token, $inputOption);

                return print_r(json_encode(['success'=>true, 'result' => $sonuc, 'message' => 'Kullanıcı bilgileri başarıyla değiştirildi.']));
            }catch (\PDOException $e){
                if($e->errorInfo[0] == 23000){
                    return print_r(json_encode(['success'=>false, 'error' => "Bu kullanıcı adı kullanılıyor."]));
                }
                return print_r(json_encode(['success' => false, 'error' => 'Sunucu Hatası']));
            }
        }catch (\Exception $e){
            print_r(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}