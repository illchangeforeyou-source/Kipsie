<?php
$conn = new mysqli('localhost', 'root', '', 'bendan');

// Check for level 1 users
$result = $conn->query('SELECT COUNT(*) as count FROM login WHERE level = 1');
$row = $result->fetch_assoc();

if ($row['count'] === 0) {
    echo "No level 1 users found. Creating test user...\n";
    $username = 'patient_test';
    $password = password_hash('password123', PASSWORD_DEFAULT);
    $email = 'patient@test.com';
    
    $stmt = $conn->prepare('INSERT INTO login (username, password, email, level, is_active) VALUES (?, ?, ?, 1, 1)');
    $stmt->bind_param('sss', $username, $password, $email);
    
    if ($stmt->execute()) {
        echo "Created level 1 user: $username\n";
        echo "Email: $email\n";
        echo "Password: password123\n";
    } else {
        echo "Error: " . $stmt->error . "\n";
    }
} else {
    echo "Level 1 users exist:\n";
    $result = $conn->query('SELECT id, username, email FROM login WHERE level = 1');
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['username'] . " (" . $row['email'] . ")\n";
    }
}

$conn->close();
?>
