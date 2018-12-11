<?php

namespace App\Controller;

use App\Helpers;
use App\Core\Validation;
use App\Model\User as UserModel;

class User {
    
    public static function register($option){
        $inputOption = Helpers::inputFormat($option);
        $inputOption['gender'] = boolval($inputOption['gender']);
        $validate = new Validation();

        try{
            $validate->setItem($inputOption['name'])->string()->min(3)->max(50)->run();
            $validate->setItem($inputOption['username'])->string()->min(5)->max(30)->run();
            $validate->setItem($inputOption['surname'])->string()->min(2)->max(100)->run();
            $validate->setItem($inputOption['email'])->email();
            $validate->setItem($inputOption['height'])->integer()->min(100)->max(250)->run();
            $validate->setItem($inputOption['weight'])->integer()->min(25)->run();
            $validate->setItem($inputOption['target_weight'])->integer()->min(25)->run();
            $validate->setItem($inputOption['gender'])->boolean()->run();
            $validate->setItem($inputOption['age'])->integer()->min(13);
            $validate->setItem($inputOption['password'])->string()->min(8)->max(25)->run();

            try{
                $inputOption['password'] = md5($inputOption['password']);
                $sonuc = (new UserModel())->createUser($inputOption);

                return print_r(json_encode(['success'=>true, 'result' => $sonuc]));
            }catch (\PDOException $e){
                if($e->errorInfo[0] == 23000){
                    return print_r(json_encode(['success'=>false, 'error' => "Bu kullanıcı adı kullanılıyor."]));
                }
                return print_r(json_encode(['success' => false, 'error' => 'Sunucu Hatası']));
            }

        }catch (\Exception $e){
            print_r(json_encode(['success'=>false, 'error' => $e->getMessage()]));
        }
    }

    public static function login($option){
        $inputOption = Helpers::inputFormat($option);

        $validate = new Validation();

        try{
            $validate->setItem($inputOption['username'])->string()->min(5)->max(30)->run();
            $validate->setItem($inputOption['password'])->string()->min(8)->max(25)->run();

            try{
                $inputOption['password'] = md5($inputOption['password']);
                $sonuc = (new UserModel())->loginUser($inputOption);

                return print_r(json_encode(['success'=>true, 'result' => $sonuc]));
            }catch (\PDOException $e){
                if($e->getCode() == 330320){
                    return print_r(json_encode(['success'=>false, 'error' => $e->getMessage()]));
                }else if($e->getCode() == 2002){
                    return print_r(json_encode(['success'=>false, 'error' => 'Bağlantı yok.']));
                }
            }

        }catch (\Exception $e){
            printf(json_encode(['success' => false, 'error' => 'Yanlış format']));
        }
    }
}