<?php
require_once __DIR__ . '/../config/database.php';
$r = $conn->query("DESCRIBE artikel");
while ($row = $r->fetch_assoc()) {
    echo $row['Field'] . "\t" . $row['Type'] . "\n";
}
