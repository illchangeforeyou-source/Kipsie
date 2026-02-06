<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bendan', 'root', '');
    $stmt = $pdo->query('DESCRIBE transactions');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Transactions Table Columns:\n";
    echo "============================\n";
    foreach($result as $row) {
        echo $row['Field'] . " - " . $row['Type'] . " (Null: " . $row['Null'] . ", Default: " . ($row['Default'] ?? 'NULL') . ", Key: " . ($row['Key'] ?? '') . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
