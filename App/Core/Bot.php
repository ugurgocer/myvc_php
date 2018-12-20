<?php
/**
 * Created by PhpStorm.
 * User: TSOFT
 * Date: 20.12.2018
 * Time: 17:31
 */

namespace App\Core;


class Bot
{
    protected $table;

    public function __construct()
    {
        libxml_use_internal_errors(true);
        $ct = curl_init();
        curl_setopt($ct, CURLOPT_URL, "http://www.kaloricetveli.org/yiyecek/alkollue-ickiler-ve-icecekler");
        curl_setopt($ct, CURLOPT_HEADER, 0);
        curl_setopt($ct, CURLOPT_RETURNTRANSFER, true);
        $html = curl_exec($ct);
        curl_close($ct);

        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $this->table = $dom->getElementById('calories-table');
    }

    public function getTable(){
        print_r($this->getIndex());
    }

    public function getIndex(){
        $index = [];

        foreach ($this->getHeadTr()->getElementsByTagName('td') as $value)
            $index[] = $value->nodeValue;

        return $index;
    }

    public function getHeadTr(){
        $head = $this->table->getElementsByTagName('thead');

        return $head[0]->getElementsByTagName('tr')[0];
    }
}