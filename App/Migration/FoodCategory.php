<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 20.12.2018
 * Time: 14:08
 */

namespace App\Migration;

use App\Core\Model;

class FoodCategory extends Model
{
    protected $tableName = 'food_category';

    public function __construct(){
        parent::__construct();
    }

    public function create(){
        if(!$this->existsTable($this->tableName)) {

            $category = [
                "Alkollü İçkiler ve İçecekler",
                "Alkolsüz İçecekler",
                "Baklagiller",
                "Balık ve Deniz Ürünleri",
                "Bira",
                "Bitkisel Sıvıyağlar",
                "Çerez ve Çekirdekler",
                "Çorbalar",
                "Dilimlenmiş Peynir",
                "Dip Soslar Ezmeler",
                "Domuz Eti",
                "Dondurma Donmuş Tatlılar",
                "Et ve Et Ürünleri",
                "Fast Food",
                "Geyik ve Av Etleri",
                "Kek ve Tartlar",
                "Konserve Meyveler",
                "Krem Peynir",
                "Kümes Hayvanları",
                "Makarna ve Noodle",
                "Meyve Suları",
                "Meyveler",
                "Otlar ve Baharatlar",
                "Pasta Malzemeleri",
                "Pastalar, Ekmek ve Unlu Mamuller",
                "Patates Ürünleri",
                "Peynir",
                "Pizza",
                "Sakatat ve İç Organları",
                "Şarap",
                "Sebzeler",
                "Şekerleme ve Tatlılar",
                "Sığır ve Dana Eti",
                "Sıvı ve Katı Yağlar",
                "Soda ve Meşrubatlar",
                "Söğüş Et ve Şarküteri Ürünleri",
                "Sosis ve Sucuk",
                "Soslar ve Salata Sosları",
                "Süt ve Süt Ürünleri",
                "Tahıllar ve Tahıllı Ürünler",
                "Tropik ve Egzotik Meyveler",
                "Yemekler ve Öğünler",
                "Yoğurt",
                "Yulaf Ezmesi, Müsli ve Tahıl Gevrekleri"
            ];

            $sorgu = "
                CREATE TABLE {$this->tableName} (
                      category_id int AUTO_INCREMENT PRIMARY KEY,
                      name varchar(200) NOT NULL UNIQUE
                ) DEFAULT CHARACTER SET utf8;
            ";

            foreach ($category as $key => $value)
                $category[$key] = "('".$value."')";

            $insert = "INSERT INTO {$this->tableName} (name) VALUES ".implode(', ', array_values($category)).";";


            try{
                $this->db->beginTransaction();

                $this->db->exec($sorgu);
                $this->db->exec($insert);

                $this->db->commit();
            }catch (\PDOException $e){
                $this->db->rollBack();
                throw new $e;
            }
        }
    }
}