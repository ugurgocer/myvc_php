<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-26
 * Time: 22:51
 */

namespace App\Migration;

use App\Core\Model;

class MealTypes extends Model
{
    protected $tableName = 'meal_types';

    public function __construct(){
        parent::__construct();
    }

    public function create(){
        if(!$this->existsTable($this->tableName)) {
            $sorgu = "
                CREATE TABLE {$this->tableName} (
                      meal_types_id int AUTO_INCREMENT PRIMARY KEY,
                      title varchar(300) NOT NULL,
                      calories int(4) NOT NULL,
                      calories_unit varchar(300) DEFAULT 'kcal',
                      user_id int NOT NULL,
                      FOREIGN KEY (user_id) REFERENCES users (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) DEFAULT CHARACTER SET utf8;
            ";

            try{
                $this->db->exec($sorgu);
            }catch (\PDOException $e){
                throw new $e;
            }
        }
    }
}