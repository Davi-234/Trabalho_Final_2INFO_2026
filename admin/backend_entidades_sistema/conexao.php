<?php
try {
    $pdo = new PDO(
        'mysql:host=localhost;dbname=mydb;charset=utf8mb4',
        'root',
        'root',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Erro ao conectar: " . $e->getMessage());
}
?>