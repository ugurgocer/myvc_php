<?php
/**
 * Created by PhpStorm.
 * User: ugurgucer
 * Date: 2018-12-06
 * Time: 22:59
 */

namespace App\Validate;

use App\Core\Validation;

class StringValidate
{
    protected $item;

    public function __construct($item)
    {
        $this->item = $item;
    }

    public function min($length)
    {
        if(Validation::$is_validate && Validation::$required) {
            Validation::$is_validate = strlen($this->item) >= $length;

            if (!Validation::$is_validate)
                Validation::$error = "alanının uzunluğu en az {$length} karakter olmalıdır.";
        }
        return $this;
    }

    public function max($length)
    {
        if(Validation::$is_validate && Validation::$required){
            Validation::$is_validate = strlen($this->item) <= $length;

            if (!Validation::$is_validate)
                Validation::$error = "alanının uzunluğu en fazla {$length} karakter olmalıdır.";

        }
        return $this;
    }

    public function alphaNumeric(){
        if(Validation::$is_validate && Validation::$required) {
            Validation::$is_validate = ctype_alnum($this->item);

            if (!Validation::$is_validate)
                Validation::$error = "alanı yalnızca sayılardan ve harflerden oluşmalıdır.";
        }

        return $this;
    }

    /**
     * @return bool
     * @throws Exception
     */

    public function run(){
        if(!Validation::$is_validate)
            throw new \Exception(Validation::$name." ".Validation::$error);
        return Validation::$is_validate;
    }
}