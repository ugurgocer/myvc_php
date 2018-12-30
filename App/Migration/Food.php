<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-20
 * Time: 23:05
 */

namespace App\Migration;

use App\Core\Model;
use App\Helpers;

class Food extends Model
{
    protected $tableName = 'foods';

    public function __construct(){
        parent::__construct();
    }

    public function create(){
        if(!$this->existsTable($this->tableName)) {
            $food = json_decode(file_get_contents('foods.json'), true);

            $sorgu = "
                CREATE TABLE {$this->tableName} (
                      food_id int AUTO_INCREMENT PRIMARY KEY,
                      yiyecek varchar(300) NOT NULL,
                      porsiyon int(4) DEFAULT 100,
                      porsiyon_unit varchar(300),
                      kalori int(10) NOT NULL
                ) DEFAULT CHARACTER SET utf8;
            ";

            foreach ($food as $ke => $item) {
                foreach ($item as $key => $value) {
                    $value['porsiyon'] = 100;
                    unset($value['category_id']);
                    unset($value['kilojul']);
                    unset($value['kilojul_unit']);
                    unset($value['kalori_unit']);
                    $indexOf = strpos($value['porsiyon_unit'], '(');
                    $value['porsiyon_unit'] = substr($value['porsiyon_unit'], $indexOf+1, strlen($value['porsiyon_unit']) - 2 - $indexOf);
                    $query = Helpers::optionToQuery((array)$value);

                    $item[$key] = $value;
                }
                $food[$ke] = $item;
            }

            $insert = "INSERT INTO {$this->tableName} ({$query[0]}) VALUES ({$query[1]});";

            try{
                $this->db->beginTransaction();

                $this->db->exec($sorgu);

                foreach ($food as $item)
                    foreach ($item as $value)
                        $this->db->prepare($insert)->execute((array)$value);

                $this->db->commit();
            }catch (\PDOException $e){
                $this->db->rollBack();
                throw new $e;
            }
        }
    }
}