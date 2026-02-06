<?php
$db_path = __DIR__ . '/database/database.sqlite';
$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$users = [
    ['username' => 'y', 'password' => 'y'],
    ['username' => 'admin', 'password' => 'admin'],
];

$inserted = 0;
foreach ($users as $u) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM login WHERE username = ?');
    $stmt->execute([$u['username']]);
    $exists = (int) $stmt->fetchColumn();
    if ($exists === 0) {
        $ins = $pdo->prepare('INSERT INTO login (username, password, created_at, updated_at) VALUES (?, ?, datetime("now"), datetime("now"))');
        $ins->execute([$u['username'], $u['password']]);
        $inserted++;
    }
}

echo "Inserted: $inserted\n";
