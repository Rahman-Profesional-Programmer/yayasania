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


//     // melakukan proses insert data ke dalam tabel menu
  $sql = "DELETE FROM menu_utama WHERE id = $id";


  if (mysqli_query($conn, $sql)) {

    // echo '<script>
    // window.onload = function() {
    //   alert("Menu berhasil ditambahkan");
    //   window.location.href = "crud-menu.php";
    // };
    // </script>';

    echo "success";

  } else {
    // menampilkan notifikasi gagal menggunakan SweetAlert
    // echo '<script>
    // window.onload = function() {
    //   alert("Menu gagal ditambahkan");
    //   window.location.href = "crud-menu.php";
    // };
    // </script>';

    echo "gagal";
  }

mysqli_close($conn);

}

?>