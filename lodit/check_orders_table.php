<?php
$conn = new mysqli('localhost', 'root', '', 'bendan');

// Check if orders table exists
$tables = $conn->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'bendan'");
$tableList = [];
while ($row = $tables->fetch_assoc()) {
    $tableList[] = $row['TABLE_NAME'];
}

echo "=== Database Tables ===\n";
if (in_array('orders', $tableList)) {
    echo "✓ 'orders' table EXISTS\n";
    
    // Check table structure
    $result = $conn->query("DESCRIBE orders");
    echo "\nTable structure:\n";
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    // Check for sample data
    $count = $conn->query("SELECT COUNT(*) as cnt FROM orders")->fetch_assoc();
    echo "\nTotal records: " . $count['cnt'] . "\n";
    
} else {
    echo "✗ 'orders' table DOES NOT EXIST\n";
}

echo "\n=== Available Tables ===\n";
foreach ($tableList as $table) {
    echo "  - $table\n";
}

$conn->close();
?>
