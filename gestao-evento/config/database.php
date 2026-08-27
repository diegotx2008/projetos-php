<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestao_eventosss');
define('DB_USER', 'rootsss');
define('DB_PASS', '');

function getPDO() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro de conexão: " . $e->getMessage());
    }
}