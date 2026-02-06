<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bendan', 'root', '');
    $stmt = $pdo->query('DESCRIBE login');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Login Table Columns:\n";
    echo "====================\n";
    foreach($result as $row) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
