<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-26
 * Time: 23:00
 */

namespace App\Model;

use App\Core\Model;
use App\Migration\MealTypes as MTMigration;

class MealTypes extends Model
{
    protected $tableName = 'meal_types';

    public function __construct(){
        parent::__construct();

        (new MTMigration())->create();
    }

    public function setTypes($user_id, $calory){
        try{
            $this->db
                ->prepare("INSERT INTO {$this->tableName} (title, calories, user_id)
                                    VALUES('Kahvaltı', :calory2x, :user_id),
                                          ('Öğle Yemeği', :calory2x, :user_id),
                                          ('Akşam Yemeği', :calory2x, :user_id),
                                          ('1.Ara Öğün', :calory, :user_id),
                                          ('2.Ara Öğün', :calory, :user_id),
                                          ('3.Ara Öğün', :calory, :user_id),
                                          ('4.Ara Öğün', :calory, :user_id);")
                ->execute(['user_id' => $user_id, 'calory2x' => $calory * 2, 'calory' => $calory]);
        }catch (\PDOException $e){
            throw $e;
        }
    }
}