<?php

header('Content-type: application/json');

require_once "./autoload.php";
require_once APP."router.php";

use App\Core\Bot;

(new Bot())->getTable();