<?php

require "redbean/rb-mysql.php";

use App\Core\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$dbconfig = require "config/database.php";

$database = new Database(
  $dbconfig['host'],
  $dbconfig['dbname'],
  $dbconfig['username'],
  $dbconfig['password']
);

$database->setup();
