<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3307;dbname=inventory_jovanni', 'jovanni', 'secret');
    echo "Connection successful!\n";
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    echo "Query result: " . $result['test'] . "\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

