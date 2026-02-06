<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bendan', 'root', '');
    $stmt = $pdo->query('DESCRIBE orders');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Orders Table Columns:\n";
    echo "====================\n";
    foreach($result as $row) {
        echo $row['Field'] . " - " . $row['Type'] . " (Null: " . $row['Null'] . ", Default: " . ($row['Default'] ?? 'None') . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
