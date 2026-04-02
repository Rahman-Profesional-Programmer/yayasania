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


    echo '<link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">';
    echo '<script src="assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>';
    echo '<script>Swal.fire({icon:"success",title:"Berhasil",text:"Menu berhasil ditambahkan",confirmButtonColor:"#0d6efd"}).then(function(){window.location.href="crud-menu.php";});</script>';

    // echo '<meta http-equiv="refresh" content="5; URL=crud-menu.php" />';
  } else {
    // menampilkan notifikasi gagal menggunakan SweetAlert
    echo '<link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">';
    echo '<script src="assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>';
    echo '<script>Swal.fire({icon:"error",title:"Gagal",text:"Menu gagal ditambahkan",confirmButtonColor:"#0d6efd"}).then(function(){window.location.href="crud-menu.php";});</script>';
  }

mysqli_close($conn);

}

?>

