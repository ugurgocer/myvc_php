<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-20
 * Time: 23:05
 */

namespace App\Migration;

use App\Core\Bot;
use App\Core\Model;
use App\Helpers;
use App\Model\FoodCategory as FCModel;

class Food extends Model
{
    protected $tableName = 'foods';

    public function __construct(){
        parent::__construct();
    }

    public function create(){
        if(!$this->existsTable($this->tableName)) {

            $food = [];
            $category = (new FCModel())->getCategories(true);

            foreach ($category as $value){
                $food[] = (new Bot($value['category_id'], $value['name']))->getData();
            }

            $sorgu = "
                CREATE TABLE {$this->tableName} (
                      food_id int AUTO_INCREMENT PRIMARY KEY,
                      yiyecek varchar(300) NOT NULL,
                      porsiyon int(4) NOT NULL,
                      kalori int(10) NOT NULL,
                      kilojul int(10) NOT NULL,
                      category_id int NOT NULL,
                      FOREIGN KEY (category_id) REFERENCES food_category (`category_id`)
                ) DEFAULT CHARACTER SET utf8;
            ";

            foreach ($food as $item)
                foreach ($item as $value)
                 $query = Helpers::optionToQuery($value);

            $insert = "INSERT INTO {$this->tableName} ({$query[0]}) VALUES ({$query[1]});";

            try{
                $this->db->beginTransaction();

                $this->db->exec($sorgu);

                foreach ($food as $item)
                    foreach ($item as $value)
                        $this->db->prepare($insert)->execute($value);

                $this->db->commit();
            }catch (\PDOException $e){
                $this->db->rollBack();
                throw new $e;
            }
        }
    }
}