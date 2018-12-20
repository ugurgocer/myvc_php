<?php

namespace App\Model;

use App\Core\Model;
use App\Helpers;
use App\Migration\User as UserMigration;

class User extends Model{
    protected $tableName = "users";

    public function __construct(){
        parent::__construct();

        (new UserMigration())->create();
        (new UserMigration())->createSession();
    }

    public function createUser($option){
        $params = Helpers::optionToQuery($option);
        try {
            $this->db->beginTransaction();
            $sorgu = "INSERT INTO {$this->tableName} ({$params[0]}) VALUES({$params[1]})";
            $this->db->prepare($sorgu)->execute($option);

            $token =  md5(uniqid());
            $user_id = $this->db->lastInsertId();
            $expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));

            $this->db
                ->prepare('INSERT INTO tokens (user_id, token, expiry_date) VALUES(:user_id, :token, :expiry_date)')
                ->execute(['user_id' => $user_id, 'token' => $token, 'expiry_date' => $expiry_date]);

            $this->db->commit();
            return [
                'user_id' => $user_id,
                'token' => $token,
                'expiry_date' => $expiry_date
            ];
        }catch (\PDOException $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function loginUser($option){

        $sorgu = "SELECT * FROM {$this->tableName} WHERE username = '{$option['username']}' AND password = '{$option['password']}'";


        $obj = $this->db->query($sorgu)->fetchObject();

        if(!$obj)
            throw new \PDOException('Kullanıcı Bulunamadı', 330320);
        else{
            try {
                $token = md5(uniqid());
                $user_id = $obj->user_id;
                $expiry_date = date('Y-m-d H:i:s', strtotime('+1 month'));

                $this->db
                    ->prepare('INSERT INTO tokens (user_id, token, expiry_date) VALUES(:user_id, :token, :expiry_date)')
                    ->execute(['user_id' => $user_id, 'token' => $token, 'expiry_date' => $expiry_date]);

                return [
                    'user_id' => $user_id,
                    'token' => $token,
                    'expiry_date' => $expiry_date
                ];
            }catch (\PDOException $e){
                return $e;
            }
        }
    }

    public function deleteToken($user_id){
        $sorgu = "SELECT * FROM {$this->tableName} LEFT JOIN tokens ON tokens.user_id = {$this->tableName}.user_id  WHERE tokens.user_id = {$user_id}";

        $obj = $this->db->query($sorgu)->fetchObject();

        if(!$obj)
            throw new \PDOException('Kullanıcı Bulunamadı', 330320);
        else{
            try{
                $this->db
                    ->prepare("DELETE FROM tokens WHERE user_id = :user_id")
                    ->execute(['user_id' => $user_id]);
            }catch (\PDOException $e){
                return $e;
            }
        }
    }

    public function userRead($token){
        try{
            $user_id = $this->isUsable($token);
            $sorgu = "SELECT username, name, surname, height, weight, target_weight, age, target_weight, gender, email FROM {$this->tableName} WHERE user_id = {$user_id}";


            return $this->db->query($sorgu)->fetchObject();
        }catch (\Exception $e){
            return $e;
        }
    }

    public function userUpdate($token, $option){
        try{
            $user_id = $this->isUsable($token);
            try{
                $sorgu = "UPDATE {$this->tableName} SET ".Helpers::optionToUpdate($option)." WHERE user_id={$user_id}";

                $query = $this->db->prepare($sorgu);
                $query->execute($option);

                return $this->userRead($token);
            }catch (\PDOException $e){
                return $e;
            }
        }catch (\Exception $e){
            return $e;
        }
    }
}