<?php
$db_path = __DIR__ . '/database/database.sqlite';
$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->query('SELECT id, username, password FROM login');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$updated = 0;
foreach ($rows as $r) {
    $pw = $r['password'];
    // Detect bcrypt-like hash
    if (preg_match('/^\$2[ayb]\$/', $pw) === 0) {
        $hash = password_hash($pw, PASSWORD_BCRYPT);
        $upd = $pdo->prepare('UPDATE login SET password = ? WHERE id = ?');
        $upd->execute([$hash, $r['id']]);
        $updated++;
    }
}

echo "Updated $updated passwords to bcrypt\n";
