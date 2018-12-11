<?php

namespace App\Model;

use App\Core\Model;
use App\Migration\User as UserMigration;

class User extends Model{
    protected $tableName = "users";

    public function __construct(){
        parent::__construct();

        (new UserMigration())->create();
        (new UserMigration())->createSession();
    }

    public function createUser($option){
        $keys = implode(',', array_keys($option));

        $sorgu = "INSERT INTO {$this->tableName} ({$keys})VALUES(:name,:username,:surname,:email,:height,:weight,:target_weight,:gender,:age,:password)";

        try {
            $this->db->beginTransaction();
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
}