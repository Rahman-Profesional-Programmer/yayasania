<?php
require_once __DIR__ . '/../config/database.php';
$r = $conn->query("SELECT id_artikel, gambar FROM artikel WHERE hapus=1 LIMIT 10");
while ($row = $r->fetch_assoc()) {
    echo $row['id_artikel'] . ' | ' . $row['gambar'] . "\n";
}
