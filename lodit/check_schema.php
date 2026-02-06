<?php
$con = new mysqli('127.0.0.1', 'root', '', 'bendan');
$result = $con->query('SHOW TABLES');
echo "Tables in bendan DB:\n";
while($row = $result->fetch_row()) {
    echo $row[0] . "\n";
}

echo "\n--- Checking order_items table ---\n";
$result = $con->query("DESC order_items");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table 'order_items' does not exist\n";
}

echo "\n--- Checking orders table ---\n";
$result = $con->query("DESC orders");
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table 'orders' does not exist\n";
}

$con->close();
