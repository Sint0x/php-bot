<?php

require 'vendor/autoload.php';
require_once 'bot.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$TOKEN = $_ENV['TOKEN'];
$DATABASE_URL = $_ENV['DATABASE_URL'];
$DB_USER_NAME = $_ENV['DB_USER_NAME'];
$DB_USER_PASSWORD = $_ENV['DB_USER_PASSWORD'];

try {
    $pdo = new PDO($DATABASE_URL, $DB_USER_NAME, $DB_USER_PASSWORD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die('Подключение не удалось: ' . $e->getMessage());
}


$telegramBot = new TelegramBot($TOKEN, $pdo);
$telegramBot->run();