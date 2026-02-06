<?php
$db_path = __DIR__ . '/database/database.sqlite';
$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function check($pdo, $username, $password) {
    $stmt = $pdo->prepare('SELECT id, username, password FROM login WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) return "no-user";
    if (password_verify($password, $user['password'])) return "ok";
    return "wrong-password";
}

$tests = [
    ['u' => 'y', 'p' => 'y'],
    ['u' => 'admin', 'p' => 'admin'],
    ['u' => 'kai', 'p' => 'chips']
];

foreach ($tests as $t) {
    $res = check($pdo, $t['u'], $t['p']);
    echo $t['u'] . ':' . $t['p'] . ' => ' . $res . "\n";
}
