<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 20.12.2018
 * Time: 15:26
 */

namespace App\Model;

use App\Migration\FoodCategory as FCMigration;
use App\Core\Model;

class FoodCategory extends Model
{
    protected $tableName = 'food_category';

    public function __construct(){
        parent::__construct();

        (new FCMigration())->create();
    }

    public function getCategories($special = false, $token = null){
        try{
            if(!$special)
                $this->isUsable($token);

            $sorgu = "SELECT * FROM {$this->tableName}";

            return $this->db->query($sorgu)->fetchAll(\PDO::FETCH_ASSOC);
        }catch (\Exception $e){
            return $e;
        }
    }
}