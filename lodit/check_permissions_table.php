<?php
$conn = new mysqli('localhost', 'root', '', 'bendan');

// Check user_permissions table structure
echo "=== User Permissions Table ===\n";
$result = $conn->query('DESCRIBE user_permissions');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "Table does not exist yet\n";
}

echo "\n=== Sample Permissions ===\n";
$result = $conn->query('SELECT * FROM user_permissions LIMIT 5');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo print_r($row, true);
    }
} else {
    echo "No permissions data\n";
}

$conn->close();
?>
