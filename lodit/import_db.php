<?php

$db_path = __DIR__ . '/database/database.sqlite';
$pdo = new PDO('sqlite:' . $db_path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Read SQL dump
$sql = file_get_contents(__DIR__ . '/lodit.sql');

// Remove MySQL-specific syntax
$sql = preg_replace('/AUTO_INCREMENT=[0-9]+/i', '', $sql);
$sql = preg_replace('/ENGINE=MyISAM[^;]*/i', '', $sql);
$sql = preg_replace('/CHARACTER SET [^ ]+/i', '', $sql);
$sql = preg_replace('/COLLATE [^ ]+/i', '', $sql);
$sql = str_replace('`', '"', $sql);

// Split and execute statements
$statements = array_filter(array_map('trim', explode(';', $sql)));
$count = 0;
foreach ($statements as $stmt) {
    if (!empty($stmt)) {
        try {
            $pdo->exec($stmt);
            $count++;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\nStatement: " . substr($stmt, 0, 100) . "\n";
        }
    }
}
echo "✓ Imported $count statements successfully!\n";
