<?php
use Dotenv\Dotenv;


$dotenv = Dotenv::createImmutable( '../');
$dotenv->load();
define('DSN', $_ENV['DSN']);
define('DB_USER', $_ENV['DB_USER']);
define('APP_URL', $_ENV['APP_URL']);
define('DB_PASSWORD', $_ENV['DB_PASSWORD']);
define('IMG_DIR', $_ENV['IMG_DIR']);