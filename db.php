<?php
require_once('config/db_config.php');

require(str_replace('//', '/', dirname(__FILE__) . '/') . '/libs/rb-mysql.php');
R::setup('mysql:host=' . $host . ';dbname=' . $dbname, $login, $password);
if (!R::testConnection()) die('No DB connection!');
