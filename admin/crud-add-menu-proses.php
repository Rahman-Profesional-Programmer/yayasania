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
    $urutanMenu = $_POST['urutan_menu'];
    $namaMenu = $_POST['nama_menu'];
    $linkMenu = $_POST['link_menu'];
  
    // echo "<br>";
    // echo $urutanMenu; echo "<br>";
    // echo $namaMenu; echo "<br>";
    // echo $linkMenu; echo "<br>";

      // melakukan proses insert data ke dalam tabel menu
  $sql = "INSERT INTO menu_utama (urutan, nama_menu, link_menu, link_menu_active, enable) 
  VALUES ('$urutanMenu', '$namaMenu', '$linkMenu', '1', '1')";

  // var_dump($sql);
  // echo "<---- sql. <br>";

  if (mysqli_query($conn, $sql)) {
    // menampilkan notifikasi sukses menggunakan SweetAlert


    echo '<script>
    window.onload = function() {
      alert("Menu berhasil ditambahkan");
      window.location.href = "crud-menu.php";
    };
    </script>';

    // echo '<meta http-equiv="refresh" content="5; URL=crud-menu.php" />';
  } else {
    // menampilkan notifikasi gagal menggunakan SweetAlert
    echo '<script>
    window.onload = function() {
      alert("Menu gagal ditambahkan");
      window.location.href = "crud-menu.php";
    };
    </script>';
  }

mysqli_close($conn);

}

?>

