<?php
$host = "127.0.0.1";
$port = 3308;
$user = "mukhin0102";
$password = "CPN7UQ^TWTUXSaAD";
$database = "mukhin0102";

$connect = new mysqli($host, $user, $password, $database, $port);

if ($connect->connect_error) {
    die("Ошибка подключения: " . $connect->connect_error);
}

$connect->set_charset("utf8mb4");
?>