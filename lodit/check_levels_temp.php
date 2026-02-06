<?php
$conn = new mysqli('localhost', 'root', '', 'bendan');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$result = $conn->query('SELECT id, username, level FROM login LIMIT 10');
echo "User Levels:\n";
echo str_repeat("-", 50) . "\n";
while ($row = $result->fetch_assoc()) {
    $level = $row['level'] ?? 'NULL';
    echo sprintf("ID: %d | User: %-15s | Level: %s\n", $row['id'], $row['username'], $level);
}
$conn->close();
?>
