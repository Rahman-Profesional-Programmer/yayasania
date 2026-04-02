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
    $urutan_sub_menu = $_POST['urutan_sub_menu'];
    $nama_sub_menu = $_POST['nama_sub_menu'];
    $link_sub_menu = $_POST['link_sub_menu'];
    $menu_utama = $_POST['menu_utama'];
    
    // echo "<br>";
    // echo $urutan_sub_menu; echo "<br>";
    // echo $nama_sub_menu; echo "<br>";
    // echo $link_sub_menu; echo "<br>";
    // echo $menu_utama; echo "<br>";


  // melakukan proses insert data ke dalam tabel menu
  $sql = "INSERT INTO sub_menu 
  (urutan, nama_menu, link_menu, link_menu_active, menu_utama, enable) 
  VALUES 
  ('$urutan_sub_menu', '$nama_sub_menu', '$link_sub_menu', '1', '$menu_utama', '1')";

  // var_dump($sql);
  // echo "<---- sql. <br>";

  if (mysqli_query($conn, $sql)) {
    echo '<link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">';
    echo '<script src="assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>';
    echo '<script>Swal.fire({icon:"success",title:"Berhasil",text:"Sub Menu berhasil ditambahkan",confirmButtonColor:"#0d6efd"}).then(function(){window.location.href="crud-menu.php";});</script>';
  } else {
    echo '<link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">';
    echo '<script src="assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>';
    echo '<script>Swal.fire({icon:"error",title:"Gagal",text:"Sub Menu gagal ditambahkan",confirmButtonColor:"#0d6efd"}).then(function(){window.location.href="crud-menu.php";});</script>';
  }

mysqli_close($conn);

}

?>

