<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 27.12.2018
 * Time: 12:01
 */

namespace App\Model;

use App\Core\Model;
use App\Helpers;
use App\Migration\Meals as MealsMigration;

class Meals extends Model
{
    protected $tableName = 'meals';

    public function __construct(){
        parent::__construct();

        (new MealsMigration())->create();
        (new MealsMigration())->createMealFoods();
    }

    public function insertMeal($user_id, $option, $meal_foods)
    {
        try {
            $this->db->beginTransaction();
            $is_set = $this->db
                ->prepare("SELECT * FROM {$this->tableName} WHERE user_id = :user_id AND meal_types_id = :meal_types_id AND creation_date = :date");
            $is_set->execute(["user_id" => $user_id, "meal_types_id" => $option['meal_types_id'], "date" => date('Y-m-d')]);
            if(count($is_set->fetchAll(\PDO::FETCH_ASSOC)))
                throw new \Exception('Bu öğünü önceden oluşturdunuz, lütfen güncellemeyi deneyiniz.');

            $calories = $this->db->query('SELECT kalori, food_id FROM foods WHERE ' . Helpers::optionToWhereAndOr($meal_foods, ' OR '))->fetchAll(\PDO::FETCH_ASSOC);
            $sum = 0;

            foreach ($calories as $calory)
                foreach ($meal_foods as $meal_food) {
                    if ($meal_food['food_id'] === $calory['food_id'])
                        $sum += $calory['kalori'] * $meal_food['amount'];
                }

            $option['total_calories'] = $sum;
            $option['user_id'] = $user_id;
            $option['creation_date'] = date('Y-m-d');

            $mealParams = Helpers::optionToQuery($option);
            $sorgu = "INSERT INTO {$this->tableName}({$mealParams[0]}) VALUES({$mealParams[1]});";
            $this->db->prepare($sorgu)->execute($option);
            $meal_id = $this->db->lastInsertId();

            foreach ($meal_foods as $key => $meal_food) {
                $meal_food['meal_id'] = $meal_id;
                $meal_foods[$key] = $meal_food;
            }


            foreach ($meal_foods as $meal_food) {
                $foodsParams = Helpers::optionToQuery($meal_food);
                $this->db
                    ->prepare("INSERT INTO meal_foods({$foodsParams[0]}) VALUES({$foodsParams[1]});")
                    ->execute($meal_food);
            }

            $this->db->commit();
            
            return ['meal_id' => $meal_id];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getMealsWithDate($user_id, $date){
        try{
            $sorgu = "
                SELECT m.meal_id, mt.title, mt.calories, m.total_calories, m.creation_date
                FROM {$this->tableName} as m INNER JOIN meal_types as mt ON mt.meal_types_id = m.meal_types_id
                WHERE m.user_id = :user_id AND m.creation_date = :date
            ";

            $meals = $this->db->prepare($sorgu);
            $meals->execute(['user_id' => $user_id, 'date' => $date]);
            $meals = $meals->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($meals as $key => $meal){
                $food = $this->db->query("
                    SELECT f.yiyecek, f.porsiyon, f.porsiyon_unit, f.kalori, mf.amount
                    FROM meal_foods as mf
                    INNER JOIN foods as f ON f.food_id = mf.food_id
                    WHERE mf.meal_id = {$meal['meal_id']}
                ")->fetchAll(\PDO::FETCH_ASSOC);
                $meal['foods'] = $food;
                $meals[$key] = $meal;
            }

            return $meals;
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function getMeal($user_id, $meal_id){
        try{
            $sorgu = "
                SELECT m.meal_id, mt.title, mt.calories, m.total_calories, m.creation_date
                FROM {$this->tableName} as m INNER JOIN meal_types as mt ON mt.meal_types_id = m.meal_types_id
                WHERE m.user_id = :user_id AND m.meal_id = :meal_id
            ";

            $meals = $this->db->prepare($sorgu);
            $meals->execute(['user_id' => $user_id, 'meal_id' => $meal_id]);
            $meals = $meals->fetch(\PDO::FETCH_ASSOC);

            $meals['foods'] = $this->db->query("
                SELECT f.yiyecek, f.porsiyon, f.porsiyon_unit, f.kalori, mf.amount
                FROM meal_foods as mf
                INNER JOIN foods as f ON f.food_id = mf.food_id
                WHERE mf.meal_id = {$meal_id}
            ")->fetchAll(\PDO::FETCH_ASSOC);

            return $meals;
        }catch (\Exception $e){
            throw $e;
        }
    }
    
    public function isExists($user_id, $meal_types_id){
        try{
            $sorgu = "
                SELECT *
                FROM {$this->tableName}
                WHERE user_id = :user_id AND meal_types_id = :meal_types_id AND creation_date = :date
            ";

            $meals = $this->db->prepare($sorgu);
            $meals->execute(['user_id' => $user_id, 'meal_types_id' => $meal_types_id, 'date' => date("Y-m-d")]);
            $meals = $meals->fetch(\PDO::FETCH_ASSOC);

            return $meals;
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function updateMeals($user_id, $option, $meal_foods){
        try {

            $this->db->beginTransaction();

            $calories = $this->db->query('SELECT kalori, food_id FROM foods WHERE ' . Helpers::optionToWhereAndOr($meal_foods, ' OR '))->fetchAll(\PDO::FETCH_ASSOC);
            $sum = 0;

            foreach ($calories as $calory)
                foreach ($meal_foods as $meal_food) {
                    if ($meal_food['food_id'] === $calory['food_id'])
                        $sum += $calory['kalori'] * $meal_food['amount'];
                }

            $option['total_calories'] = $sum;
            $option['user_id'] = $user_id;
            $option['creation_date'] = date('Y-m-d');
            $meal_id = $option['meal_id'];
            unset($option['meal_id']);

            $sorgu = "UPDATE {$this->tableName} SET ".Helpers::optionToUpdate($option)." WHERE user_id={$user_id} AND meal_id={$meal_id}";
            $pre = $this->db->prepare($sorgu);
            $pre->execute($option);

            foreach ($meal_foods as $meal_food) {
                $query = '';
                if($meal_food['amount']) {
                    $is_exist = $this->db
                        ->query("SELECT * FROM meal_foods WHERE food_id = {$meal_food['food_id']} AND meal_id = {$meal_id}")
                        ->fetchObject();
                    if (!$is_exist) {
                        $foodsParams = Helpers::optionToQuery($meal_food);
                        $query = "INSERT INTO meal_foods({$foodsParams[0]}, meal_id) VALUES({$foodsParams[1]}, {$meal_id})";
                    } else {
                        $food_id = $meal_food['food_id'];
                        unset($meal_food['food_id']);

                        $query = "UPDATE meal_foods SET " . Helpers::optionToUpdate($meal_food) . " WHERE food_id = {$food_id} AND meal_id={$meal_id}";
                    }
                }else{
                    $query = "DELETE FROM meal_foods WHERE food_id = {$meal_food['food_id']} AND meal_id = $meal_id";
                }

                $this->db
                    ->prepare($query)
                    ->execute($meal_food);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
