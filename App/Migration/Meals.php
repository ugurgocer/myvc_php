<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 27.12.2018
 * Time: 11:02
 */

namespace App\Migration;

use App\Core\Model;
class Meals extends Model
{
    protected $tableName = 'meals';

    public function __construct(){
        parent::__construct();
    }

    public function create(){
        if(!$this->existsTable($this->tableName)) {
            $sorgu = "
                CREATE TABLE {$this->tableName}(
                      meal_id int AUTO_INCREMENT PRIMARY KEY,
                      meal_types_id int NOT NULL,
                      total_calories int NOT NULL,
                      user_id int NOT NULL,
                      creation_date DATE NOT NULL,
                      FOREIGN KEY (user_id) REFERENCES users (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      FOREIGN KEY (meal_types_id) REFERENCES meal_types (`meal_types_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) DEFAULT CHARACTER SET utf8;
            ";

            try{
                $this->db->exec($sorgu);
            }catch (\PDOException $e){
                throw $e;
            }
        }
    }

    public function createMealFoods(){
        $tableName = 'meal_foods';
        if(!$this->existsTable($tableName)) {
            $sorgu = "
                CREATE TABLE {$tableName}(
                      meal_id int NOT NULL,
                      food_id int NOT NULL,
                      amount int NOT NULL,
                      FOREIGN KEY (food_id) REFERENCES foods (`food_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                      FOREIGN KEY (meal_id) REFERENCES meals (`meal_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) DEFAULT CHARACTER SET utf8;
            ";

            try{
                $this->db->exec($sorgu);
            }catch (\PDOException $e){
                throw $e;
            }
        }
    }
}