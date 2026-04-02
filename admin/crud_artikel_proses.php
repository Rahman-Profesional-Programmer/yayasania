<?php

// koneksi DB
include('../admin/koneksi_db.php');
// koneksi ID USER
include('../admin/koneksi_user.php');
// koneksi url
include('../admin/koneksi_url.php');

// cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
      echo "koneksi DB gagal. <br>";
  }
  
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // mengambil nilai inputan dari form
    $isi_artikel = $_POST['isi_artikel'];
    $judul_artikel = $_POST['judul_artikel'];
    $kategori = $_POST['kategori'];

    if($_POST['kategori'] == "lain"){
      $kategori = $_POST['kategori_baru'];
    } 

    $nama_file = $_FILES["foto"]["name"];
    $ukuran_file = $_FILES["foto"]["size"];
    $tmp_file = $_FILES["foto"]["tmp_name"];
    $ekstensi_file = pathinfo($nama_file, PATHINFO_EXTENSION); // Mendapatkan ekstensi file

    // echo "<br>"; 
    // buat nama file unik dengan timestamp
    $nama_baru = time();
    
    // echo "<br>"; 
    // echo $nama_baru. " <---- nama_baru. <br>";
    // echo $ekstensi_file. " <----  ekstensi file. <br>";

    $path = $url.'admin/upload_foto/' .$nama_baru.".".$ekstensi_file;

    // echo "<br>"; 
    // echo $path; echo "<---- path. <br>";

    // Mengambil nilai inputan tag
    $tagInput = $_POST['tag'];

    // Memisahkan tag yang dipisahkan oleh koma menjadi array tag
    $tagArray = explode(',', $tagInput);

    // Menghilangkan spasi ekstra dari setiap tag
    $tags = array_map('trim', $tagArray);

    // cek ukuran file
    if ($ukuran_file > 5000000) {
      echo "Ukuran file terlalu besar";
      exit;
    }

	  // pindahkan file ke direktori uploads
    if (move_uploaded_file($tmp_file, $path)) {
        
        // echo "File berhasil diunggah.";
        // simpan url ke database
        $link_foto = 'admin/upload_foto/' .$nama_baru.".".$ekstensi_file;

        $judul_artikel = addslashes($judul_artikel);
        $isi_artikel = addslashes($isi_artikel);
        $kategori = addslashes($kategori);
        $judul_artikel = addslashes($judul_artikel);
      
        // echo "<br>";
        // echo $judul_artikel; echo "<br>";  echo "<br>";
        // echo $isi_artikel ; echo "<br>";  echo "<br>";
        // echo $link_foto; echo "<br>";  echo "<br>";
        // echo $kategori; echo "<br>";  echo "<br>";

      // melakukan proses insert data ke dalam tabel menu
      $sql = "INSERT INTO artikel
          (judul_artikel, konten_artikel, gambar, 
          penulis, kategori, tanggal_update, 
          viewer, enable, hapus) 
          VALUES 
          ('$judul_artikel', '$isi_artikel', '$link_foto',
          '$id_email_user','$kategori',CURRENT_TIMESTAMP(), 
          '0','1','1')";

          // echo "<br>";
          // echo "<br>";

          // echo var_dump($sql);
          // echo "<br>"; 

        if (mysqli_query($conn, $sql)) {

          // ambi ID terkahir
          $id_artikel = "SELECT * FROM artikel ORDER BY id_artikel DESC LIMIT 1";
          // eksekusi query dan simpan hasilnya ke dalam variabel $result
          $result_id = mysqli_query($conn, $id_artikel);

          // tampilkan data yang ditemukan dalam bentuk tabel
          // masukan tag kedalam DB
          while ($row = mysqli_fetch_assoc($result_id)) {
              $last_id_artikel = $row['id_artikel'];
            }

            // echo  $last_id_artikel . " <-- last_id_artikel";

            // pengulangan memasukan tag ke DB
            foreach ($tags as $tag) {
                // echo "<br>";
                // echo $tag . "<br>";

                $tag = addslashes($tag);

                $sql_tag = "INSERT INTO artikel_tag
                  (id_artikel, tag) VALUES ('$last_id_artikel', '$tag')";
                mysqli_query($conn, $sql_tag);
            }

            echo '<link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">';
            echo '<script src="assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>';
            echo '<script>Swal.fire({icon:"success",title:"Berhasil",text:"Artikel berhasil ditambahkan",confirmButtonColor:"#0d6efd"}).then(function(){window.location.href="crud-artikel.php";});</script>';

        } else {

            echo '<link rel="stylesheet" href="assets/plugins/sweetalert2/css/sweetalert2.min.css">';
            echo '<script src="assets/plugins/sweetalert2/js/sweetalert2.min.js"></script>';
            echo '<script>Swal.fire({icon:"error",title:"Gagal",text:"Artikel gagal ditambahkan",confirmButtonColor:"#0d6efd"}).then(function(){window.location.href="crud-artikel.php";});</script>';
        }

    } else {
        echo "Gagal mengunggah file.";
    }

  mysqli_close($conn);

}

?>