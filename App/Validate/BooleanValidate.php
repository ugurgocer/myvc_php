<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-08
 * Time: 21:52
 */

namespace App\Validate;


use App\Core\Validation;

class BooleanValidate
{
    protected $item;
    public function __construct($item)
    {
        $this->item = $item;
    }

    public function typeFalse($type){
        if(Validation::$is_validate && Validation::$required)
            Validation::$is_validate = $type == false;
        return $this->run();
    }

    public function typeTrue($type){
        if(Validation::$is_validate && Validation::$required)
            Validation::$is_validate = $type == true;
        return $this->run();
    }

    public function run(){
        if(!Validation::$is_validate)
            throw new \Exception(Validation::$name." ".Validation::$error);
        return Validation::$is_validate;
    }
}