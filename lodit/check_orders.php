<?php
$con = new mysqli('127.0.0.1', 'root', '', 'bendan');
$result = $con->query("DESC orders");
echo "Columns in orders table:\n";
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}
$con->close();
