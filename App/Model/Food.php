<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-20
 * Time: 23:05
 */

namespace App\Model;

use App\Core\Model;
use App\Helpers;
use App\Migration\Food as FoodMigration;
use App\Migration\FoodCategory as FCMigration;

class Food extends Model
{
    protected $tableName = 'foods';

    public function __construct(){
        parent::__construct();

        (new FoodMigration())->create();
        (new FCMigration())->create();
    }

    public function getFoodWithCategoryID($token, $id){
        try{
            $this->isUsable($token);
            $sorgu = "SELECT * FROM {$this->tableName} as f INNER JOIN food_category as fc ON fc.category_id = f.category_id WHERE f.category_id = {$id}";

            return $this->db->query($sorgu)->fetchAll(\PDO::FETCH_ASSOC);
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function getFoodWithCaloryInterval($token, $min, $max){
        try{
            $this->isUsable($token);

            $sorgu = "SELECT * FROM {$this->tableName} as f INNER JOIN food_category as fc ON fc.category_id = f.category_id WHERE f.kalori BETWEEN {$min} AND {$max}";

            return $this->db->query($sorgu)->fetchAll(\PDO::FETCH_ASSOC);
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function insertFood($token, $option){
        $params = Helpers::optionToQuery($option);

;        try{
            $this->isUsable($token);

            $sorgu = "INSERT INTO {$this->tableName} ({$params[0]}) VALUES({$params[1]})";
            $this->db->prepare($sorgu)->execute($option);

        }catch (\Exception $e){
            throw $e;
        }
    }
}