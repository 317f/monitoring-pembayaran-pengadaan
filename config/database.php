<?php
try {
    $pdo = new PDO("sqlite:" . __DIR__ . "/../database/procurement.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
