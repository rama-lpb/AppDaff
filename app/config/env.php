<?php



$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../', null, false);
$dotenv->safeLoad();


define('DB_USER', $_ENV['DB_USER']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);

define('DSN', $_ENV['DSN'] );