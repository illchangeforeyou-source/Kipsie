<?php
$conn = new mysqli('localhost', 'root', '', 'bendan');

// Check for level 4 users and verify they have super admin section access
$result = $conn->query('SELECT id, username FROM login WHERE level = 4');
if ($row = $result->fetch_assoc()) {
    echo "✓ Level 4 user (SuperAdmin) exists: " . $row['username'] . "\n";
} else {
    echo "Creating level 4 user...\n";
    $username = 'superadmin_test';
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $email = 'superadmin@test.com';
    
    $stmt = $conn->prepare('INSERT INTO login (username, password, email, level, is_active) VALUES (?, ?, ?, 4, 1)');
    $stmt->bind_param('sss', $username, $password, $email);
    
    if ($stmt->execute()) {
        echo "✓ Created: $username (Level 4 - SuperAdmin)\n";
    }
}

// Remove any level 5 users that might have been created
$conn->query('DELETE FROM login WHERE level = 5');
echo "✓ Cleared any level 5 test users\n";

// Show all users by level
echo "\n=== User Status ===\n";
$result = $conn->query('SELECT level, COUNT(*) as count FROM login WHERE level IN (1,2,3,4) GROUP BY level ORDER BY level');
while ($row = $result->fetch_assoc()) {
    echo "Level " . $row['level'] . ": " . $row['count'] . " user(s)\n";
}

$conn->close();
?>
