<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 20.12.2018
 * Time: 17:31
 */

namespace App\Core;


use App\Helpers;

class Bot
{
    protected $table;
    protected $category_id;

    public function __construct($id, $name)
    {
        libxml_use_internal_errors(true);
        $ct = curl_init();
        //print_r("http://www.kaloricetveli.org/yiyecek/".Helpers::urlEncode($name)."\n");
        curl_setopt($ct, CURLOPT_URL, "http://www.kaloricetveli.org/yiyecek/".Helpers::urlEncode($name));
        curl_setopt($ct, CURLOPT_HEADER, 0);
        curl_setopt($ct, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ct);
        curl_close($ct);

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $this->category_id = $id;
        $this->table = $dom->getElementById('calories-table');
    }

    public function getData(){
        return $this->getBodyTrWithTd();
    }

    public function getIndex(){
        $index = [];

        foreach ($this->getHeadTr()->getElementsByTagName('td') as $value)
            $index[] = mb_strtolower($value->nodeValue);

        return $index;
    }

    public function getBodyTrWithTd(){
        $body = $this->table->getElementsByTagName('tbody');

        $tr = [];
        foreach ($body->item(0)->getElementsByTagName('tr') as $value){
           $td = [];
           $i = 0;
           foreach ($value->getElementsByTagName('td') as $item){
               if($item->getElementsByTagName('data')->length)
                   $td[$this->getIndex()[$i]] = intval($item->getElementsByTagName('data')[0]->nodeValue);
               else {
                   $td[$this->getIndex()[$i]] = $item->nodeValue;
                   $td['category_id'] = $this->category_id;
               }
               $i++;
           }

           $tr[] = $td;
        }

        return $tr;
    }
    public function getHeadTr(){
        $head = $this->table->getElementsByTagName('thead');

        return $head[0]->getElementsByTagName('tr')[0];
    }
}