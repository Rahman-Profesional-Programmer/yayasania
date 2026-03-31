<?php
// koneksi DB
include('../admin/koneksi_db.php');

// cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
      echo "koneksi DB gagal. <br>";
  }
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // mengambil nilai inputan dari form
     $id= $_POST['id'];

    // melakukan proses insert data ke dalam tabel menu
    $sql = "UPDATE sub_menu SET enable = 1 WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
      echo "success";
    } else {
      echo "gagal";
    }

}

?>

