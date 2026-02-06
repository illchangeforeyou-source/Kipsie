<?php
$conn = new mysqli('localhost', 'root', '', 'bendan');

// Check for level 1 users
$result = $conn->query('SELECT COUNT(*) as count FROM login WHERE level = 1');
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    echo "Creating level 1 user...\n";
    $username = 'patient_test';
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $email = 'patient@test.com';
    
    $stmt = $conn->prepare('INSERT INTO login (username, password, email, level, is_active) VALUES (?, ?, ?, 1, 1)');
    $stmt->bind_param('sss', $username, $password, $email);
    
    if ($stmt->execute()) {
        echo "✓ Created: $username (Level 1)\n";
    }
} else {
    echo "✓ Level 1 user already exists\n";
}

// Check for level 5 users
$result = $conn->query('SELECT COUNT(*) as count FROM login WHERE level = 5');
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    echo "Creating level 5 (SuperAdmin) user...\n";
    $username = 'superadmin_test';
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $email = 'admin@test.com';
    
    $stmt = $conn->prepare('INSERT INTO login (username, password, email, level, is_active) VALUES (?, ?, ?, 5, 1)');
    $stmt->bind_param('sss', $username, $password, $email);
    
    if ($stmt->execute()) {
        echo "✓ Created: $username (Level 5)\n";
    }
} else {
    echo "✓ Level 5 user already exists\n";
}

// Show all users by level
echo "\n=== Final User Status ===\n";
$result = $conn->query('SELECT level, COUNT(*) as count FROM login WHERE level IN (1,2,3,4,5) GROUP BY level ORDER BY level');
while ($row = $result->fetch_assoc()) {
    echo "Level " . $row['level'] . ": " . $row['count'] . " user(s)\n";
}

$conn->close();
?>
