<?php

define('SEP', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__ . SEP);
define('APP', ROOT.'App'.SEP);
define('CONFIG', APP.'Config'.SEP);
define('CONTROLLER', APP.'Controller'.SEP);
define('CORE', APP.'Core'.SEP);
define('MODEL', APP.'Model'.SEP);
 
spl_autoload_register(function ($class) {
    $file = ROOT . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});