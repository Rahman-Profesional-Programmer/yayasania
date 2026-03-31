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
    $menu_id = $_POST['id'];
    $urutanMenu = $_POST['urutan_menu'];
    $namaMenu = $_POST['nama_menu'];
    $linkMenu = $_POST['link_menu'];
    $menu_utama = $_POST['menu_utama'];
    
  
    // echo "<br>";
    // echo $menu_id; echo "<br>";
    // echo $urutanMenu; echo "<br>";
    // echo $namaMenu; echo "<br>";
    // echo $linkMenu; echo "<br>";
    // echo $menu_utama; echo "<br>";

    // melakukan proses insert data ke dalam tabel menu
    $sql = "UPDATE sub_menu SET  urutan = '$urutanMenu',
                                  nama_menu = '$namaMenu',
                                  link_menu = '$linkMenu',
                                  menu_utama = '$menu_utama'
                                WHERE id = $menu_id";

    // var_dump($sql);
    // echo "<---- sql. <br>";
    if (mysqli_query($conn, $sql)) {
      echo '<script>
        window.onload = function() {
          alert("Sub menu berhasil diedit");
          window.location.href = "crud-menu.php";
        };
      </script>';
    } else {
      echo '<script>
        window.onload = function() {
          alert("Sub menu gagal diedit");
          window.location.href = "crud-menu.php";
        };
      </script>';
    }

    mysqli_close($conn);

  }

?>

