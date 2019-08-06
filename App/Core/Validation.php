<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 6.12.2018
 * Time: 13:27
 */

namespace App\Core;

use App\Validate\StringValidate;
use App\Validate\IntValidate;
use App\Validate\BooleanValidate;

class Validation
{
    protected static $item;
    public static $is_validate = true;
    public static $required = false;
    public static $error;
    public static $name;

    public function setItem($item, $name)
    {
        self::$item = $item;
        self::$name = $name;

        return $this;
    }

    public function Required()
    {
        self::$required = true;

        if(!isset(self::$item))
            self::$error = 'İfadesinin girilmesi zorunludur.';
        return $this;
    }

    public function notRequired(){
        self::$required = false;

        if(isset(self::$item)){
            self::$required = true;
        }

        return $this;
    }
    public function string(){
        if(self::$is_validate && self::$required) {
            self::$is_validate = is_string(self::$item);

            if (!self::$is_validate)
                self::$error = ' alanı metin olmalıdır';
        }

        return new StringValidate(self::$item);
    }

    public function integer(){
        if(self::$is_validate && self::$required){
            self::$is_validate = filter_var(floatval(self::$item), FILTER_VALIDATE_FLOAT);
            if (!self::$is_validate)
                self::$error = 'alanı sayısal olmalıdır.';
        }
        return new IntValidate(floatval(self::$item));
    }

    public function email(){
        if(self::$is_validate && self::$required){
            self::$is_validate = filter_var(self::$item, FILTER_VALIDATE_EMAIL);
            if (!self::$is_validate)
                self::$error = 'geçerli bir email olmalıdır.';

        }
        return self::run();
    }

    public function boolean(){
        if(self::$is_validate && self::$required){
            self::$is_validate = is_bool(self::$item);

            if(!self::$is_validate)
                self::$error = 'yanlış bir değere sahiptir.';
        }
        return new BooleanValidate(self::$item);
    }

    public function date(){
        if(self::$is_validate && self::$required) {
            self::$is_validate = \DateTime::createFromFormat('d-m-Y', self::$item);

            if(!self::$is_validate)
                self::$error = 'tarih formatında olmalıdır.';
        }

        return self::run();
    }

    public static function run(){
        if(!self::$is_validate)
            throw new \Exception(self::$name." ".self::$error);
        return self::$is_validate;
    }
}