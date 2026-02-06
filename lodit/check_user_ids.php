<?php

try {
    $pdo = new PDO('mysql:host=127.0.0.1;dbname=bendan', 'root', '');
    
    echo "Users in login table:\n";
    echo "====================\n";
    $stmt = $pdo->query('SELECT id, username, email FROM login ORDER BY id DESC LIMIT 15');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($result as $row) {
        echo "  ID: {$row['id']} - {$row['username']} ({$row['email']})\n";
    }
    
    echo "\n\nOrders with user_id values:\n";
    echo "===========================\n";
    $stmt = $pdo->query('SELECT DISTINCT user_id FROM orders WHERE user_id IS NOT NULL ORDER BY user_id');
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach($result as $row) {
        echo "  user_id: {$row['user_id']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
