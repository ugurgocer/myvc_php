<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-20
 * Time: 23:05
 */

namespace App\Model;

use App\Core\Model;
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

            print_r($sorgu);
            return $this->db->query($sorgu)->fetchAll(\PDO::FETCH_ASSOC);
        }catch (\Exception $e){
            return $e;
        }
    }
}