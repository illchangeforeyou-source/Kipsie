<?php
$conn = new mysqli('localhost', 'root', '', 'bendan');

// Get one user from each level
echo "=== Users by Level ===\n";
for ($level = 1; $level <= 5; $level++) {
    $result = $conn->query("SELECT id, username FROM login WHERE level = $level LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        echo "Level $level: " . $row['username'] . " (ID: " . $row['id'] . ")\n";
    } else {
        echo "Level $level: No user\n";
    }
}

echo "\n=== Testing Expected Sidebar Visibility ===\n";
echo "Level 1: Should see 'My History' only\n";
echo "Level 2: Should see basic items only\n";
echo "Level 3: Should see Admin Panel (Dashboard, Users, Reports, Settings) - NO Permissions\n";
echo "Level 4: Should see all Admin Panel items including Permissions\n";
echo "Level 5: Should see Admin Panel + SuperAdmin section\n";

$conn->close();
?>
