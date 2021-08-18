<?php
require 'class/Medoo.php';
use Medoo\Medoo;
$data = [
    'database_type' => 'sqlite',
    'database_file' => 'db/user.db3'
];
$db = new medoo($data);

define('TOKEN','attack');
define('TEMPLATE','default');
define("USER","Happy");
define("Log","index.php");