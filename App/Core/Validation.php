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

    public function setItem($item)
    {
        self::$item = $item;

        return $this;
    }

    public function string(){
        if(self::$is_validate)
            self::$is_validate = is_string(self::$item);
        return new StringValidate(self::$item);
    }

    public function integer(){
        if (self::$is_validate)
            self::$is_validate = ctype_digit(self::$item);
        return new IntValidate(intval(self::$item));
    }

    public function email(){
        if(self::$is_validate)
            self::$is_validate = filter_var(self::$item, FILTER_VALIDATE_EMAIL);
        return self::run();
    }

    public function boolean(){
        if(self::$is_validate)
            self::$is_validate = is_bool(self::$item);
        return new BooleanValidate(self::$item);
    }

    public function date(){
        if(self::$is_validate)
            self::$is_validate = \DateTime::createFromFormat('d-m-Y', self::$item);
        return self::run();
    }

    public static function run(){
        if(!self::$is_validate)
            throw new \Exception(self::$item." not valid");
        return self::$is_validate;
    }
}