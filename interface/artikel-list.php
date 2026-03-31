

<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ==== Document Title ==== -->
    <title>Yayasan Ihsanul Amal</title>

    <!-- ==== Document Meta ==== -->
    <meta name="author" content="">
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- ==== Favicons ==== -->
    <link rel="icon" href="img/logoyysn.png" type="image/png">

    <!-- ==== Google Font ==== -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700">

    <!-- ==== Font Awesome ==== -->
    <link rel="stylesheet" href="css/font-awesome.min.css">
    
    <!-- ==== Bootstrap Framework ==== -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    
    <!-- ==== Bar Rating Plugin ==== -->
    <link rel="stylesheet" href="css/fontawesome-stars-o.min.css">
    
    <!-- ==== Main Stylesheet ==== -->
    <link rel="stylesheet" href="style.css">
    
    <!-- ==== Responsive Stylesheet ==== -->
    <link rel="stylesheet" href="css/responsive-style.css">

    <!-- ==== Theme Color Stylesheet ==== -->
    <link rel="stylesheet" href="css/colors/theme-color-1.css" id="changeColorScheme">
    
    <!-- ==== Custom Stylesheet ==== -->
    <link rel="stylesheet" href="css/custom.css">

    <!-- ==== HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries ==== -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <!-- Preloader Start -->
    <div id="preloader">
        <div class="preloader bg--color-1--b" data-preloader="1">
            <div class="preloader--inner"></div>
        </div>
    </div>
    <!-- Preloader End -->

    <!-- Wrapper Start -->
    <div class="wrapper">
        <!-- Header Section Start -->
        <header class="header--section header--style-1">
            <!-- Header Topbar Start -->
            <div class="header--topbar bg--color-2">
                <div class="container">
                    <div class="float--left float--xs-none text-xs-center">
                        <!-- Header Topbar Info Start -->
                        <ul class="header--topbar-info nav">
                            <li><i class="fa fm fa-calendar"></i>
                                <?php 
                            
                                    $nama_hari = array("Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu");
                                    $hari = date("N"); // mengambil nomor hari dalam seminggu (1-7)
                                    $hari_ini = $nama_hari[$hari]; // mengambil nama hari dari array
                                    $tanggal = date("j");
                                    $bulan = date("F");
                                    $tahun = date("Y");
                                    echo "Hari ini adalah " . $hari_ini . ", " . $tanggal . " " . $bulan . " " . $tahun;
                                    
                                ?>
                            </li>
                            <li><i class="fa fm fa-mixcloud"></i> Website Resmi Yayasan Ihsanul Amal Alabio</li> 
                        </ul>
                        <!-- Header Topbar Info End -->
                    </div>

                    <div class="float--right float--xs-none text-xs-center">
                        <!-- Header Topbar Action Start -->
                        <ul class="header--topbar-action nav">
                            <li><a href="../admin/authentication-signin.php" target="_blank"><i class="fa fm fa-user-o"></i>Login Admin</a></li>
                        </ul>
                        <!-- Header Topbar Action End -->
                        
                        <!-- Header Topbar Language Start -->
                        <!-- <ul class="header--topbar-lang nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fm fa-language"></i>English<i class="fa flm fa-angle-down"></i></a>

                                <ul class="dropdown-menu">
                                    <li><a href="#">English</a></li>
                                    <li><a href="#">Spanish</a></li>
                                    <li><a href="#">French</a></li>
                                </ul>
                            </li>
                        </ul> -->
                        <!-- Header Topbar Language End -->

                        <!-- Header Topbar Social Start -->
                        <ul class="header--topbar-social nav hidden-sm hidden-xxs">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <!-- <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>-->
                            <li><a href="#"><i class="fa fa-instagram"></i></a></li> 
                            <li><a href="#"><i class="fa fa-youtube-play"></i></a></li>
                        </ul>
                        <!-- Header Topbar Social End -->
                    </div>
                </div>
            </div>
            <!-- Header Topbar End -->

            <!-- Header Mainbar Start -->
            <div class="header--mainbar">
                <div class="container">

                    <!-- Header Logo Start -->
                    <div class="header--logo float--left float--sm-none text-sm-center">
                        <h1 class="h1">
                            <a href="home-1.html" class="btn-link">
                            <img src="img/logoyysn.png" alt="Ihsanul Amal Logo" width="120px">
                                <span class="hidden">Yayasan IA Logo</span>
                            </a>
                        </h1>
                    </div>
                    <!-- Header Logo End -->

                    <!-- Header Ad Start -->
                    <div class="header--ad float--right float--sm-none hidden-xs">
                        <a href="#">
                            <img src="img/ads-img/ad-728x90-01.jpg" alt="Advertisement">
                        </a>
                    </div>
                    <!-- Header Ad End -->

                </div>
            </div>
            <!-- Header Mainbar End -->

            <!-- Header Navbar Start -->
            <div class="header--navbar navbar bd--color-1 bg--color-1" data-trigger="sticky">
                <div class="container">
                    
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#headerNav" aria-expanded="false" aria-controls="headerNav">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <!-- <div id="headerNav" class="navbar-collapse collapse float--left"> -->
                    <?php

                        // require_once '../interface/koneksi_db.php';
                        include('../admin/koneksi_db.php');

                        // koneksi url
                        include('../admin/koneksi_url.php');

                        // Memeriksa koneksi
                        if ($conn->connect_error) {
                            die("Koneksi gagal: " . $conn->connect_error);
                        }

                        // Mengambil data dari tabel menu_utama
                        $sql = "SELECT * FROM menu_utama WHERE enable = 1 ORDER BY urutan";
                        $result = $conn->query($sql);

                        // if ($result->num_rows > 0) {

                        //     echo '<div id="headerNav" class="navbar-collapse collapse float--left">
                        //     <ul class="header--menu-links nav navbar-nav" data-trigger="hoverIntent">';
                            
                        //     while($row = $result->fetch_assoc()) {

                        //         // cek anak
                        //         $nama_menu = $row["nama_menu"];

                        //         $sql_sub_menu = "SELECT * FROM sub_menu WHERE enable = 1 AND menu_utama = '$nama_menu' ORDER BY urutan";
                        //         $result_2 = $conn->query($sql_sub_menu);

                        //         // Periksa apakah query berhasil
                        //         if ($result_2->num_rows > 0) {
                        //             $var_class = 'class="dropdown active"';
                        //             $komponen_a = 'href="#" class="dropdown-toggle" data-toggle="dropdown"';
                        //             $komponen_i = '<i class="fa flm fa-angle-down"></i>';
                        //             $komponen_ul = '<ul class="dropdown-menu">';

                        //         } else {
                        //             // echo "";
                        //             $var_class = '';
                        //             $komponen_a = 'href="'. $row["link_menu"].'"';
                        //             $komponen_i = '';
                        //             $komponen_ul = '';
                        //         }

                        //         echo '<li '.$var_class.'>
                        //             <a style = "padding-left: 30px; padding-right: 30px;" '.$komponen_a.'>' . $row["nama_menu"].$komponen_i. '</a>'.$komponen_ul;
                        //             // Looping untuk membaca data yang dipilih
                        //             while($row_2 = $result_2->fetch_assoc()) {
                        //                 echo '<li><a href="'.$row_2["link_menu"].'">'.$row_2["nama_menu"].'</a></li>';
                        //             }

                        //             if ($komponen_ul != '') {
                        //                 echo '</ul>';
                        //             }
                        //         echo '</li>';

                        //     }

                        //     echo '</ul>
                        //     </div>';
                        //     // echo "</table>";
                        // } else {
                        //     echo "0 results";
                        // }

                        // Menutup koneksi
                        $conn->close();
                    ?>

                    <!-- </div> -->

                    <!-- Header Search Form Start -->
                    <form action="#" class="header--search-form float--right" data-form="validate">
                        <input type="search" name="search" placeholder="Search..." class="header--search-control form-control" required>

                        <button type="submit" class="header--search-btn btn"><i class="header--search-icon fa fa-search"></i></button>
                    </form>
                    <!-- Header Search Form End -->
                </div>
            </div>
            <!-- Header Navbar End -->

        </header>
        <!-- Header Section End -->

        <!-- Posts Filter Bar Start -->
        <!-- <div class="posts--filter-bar style--1 hidden-xs">
            <div class="container">
                <ul class="nav">
                    <li>
                        <a href="#">
                            <i class="fa fa-star-o"></i>
                            <span>Featured News</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-heart-o"></i>
                            <span>Most Popular</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-fire"></i>
                            <span>Hot News</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-flash"></i>
                            <span>Trending News</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="fa fa-eye"></i>
                            <span>Most Watched</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div> -->
        <!-- Posts Filter Bar End -->

        <!-- News Ticker Start -->
        <div class="news--ticker">
            <div class="container">
                <div class="title">
                    <h2>News Updates</h2>
                    <span>(Update 12 minutes ago)</span>
                </div>

                <div class="news-updates--list" data-marquee="true">
                    <ul class="nav">
                        <li>
                            <h3 class="h3"><a href="#">Contrary to popular belief Lorem Ipsum is not simply random text.</a></h3>
                        </li>
                        <li>
                            <h3 class="h3"><a href="#">Education to popular belief Lorem Ipsum is not simply</a></h3>
                        </li>
                        <li>
                            <h3 class="h3"><a href="#">Lorem ipsum dolor sit amet consectetur adipisicing elit.</a></h3>
                        </li>
                        <li>
                            <h3 class="h3"><a href="#">Corporis repellendus perspiciatis reprehenderit.</a></h3>
                        </li>
                        <li>
                            <h3 class="h3"><a href="#">Deleniti consequatur laudantium sit aspernatur?</a></h3>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- News Ticker End -->

        <!-- Main Breadcrumb Start -->
        <div class="main--breadcrumb">
            <div class="container">
                <ul class="breadcrumb">
                    <li><a href="home.php" class="btn-link"><i class="fa fm fa-home"></i>Home</a></li>
                    <li class="active"><span>List Artikel</span></li>
                </ul>
            </div>
        </div>
        <!-- Main Breadcrumb End -->

        <!-- Main Content Section Start -->
        <div class="main-content--section pbottom--30">
            <div class="container">
                <div class="row">
                    <!-- Main Content Start -->
                    <div class="main--content col-md-8 col-sm-7" data-sticky-content="true">
                        <div class="sticky-content-inner">
                            <!-- Post Items Start -->
                            <div class="post--items post--items-5 pd--30-0">
                                <ul class="nav">

                                <?php

                                    // // require_once '../interface/koneksi_db.php';
                                    // include('../admin/koneksi_db.php');

                                    // // Memeriksa koneksi
                                    // if ($conn->connect_error) {
                                    //     die("Koneksi gagal: " . $conn->connect_error);
                                    // }
                                
                                ?>

                                <?php

                                    // //query untuk mengambil data dari tabel Artikel
                                    // $query_artikel = "SELECT * FROM Artikel WHERE disable = 1 AND hapus = 1";

                                    // //eksekusi query dan simpan hasilnya ke dalam variabel $result
                                    // $result_artikel = mysqli_query($conn, $query_artikel);

                                    // //tampilkan data yang ditemukan dalam bentuk tabel
                                    // while ($row = mysqli_fetch_assoc($result_artikel)) {

                                    //     $url_foto = $url_show.$row['gambar'];
                                       
                                    //     echo '<li>';

                                    //     echo '<div class="post--item post--title-larger">
                                    //             <div class="row">
                                    //                 <div class="col-md-4 col-sm-12 col-xs-4 col-xxs-12">
                                    //                     <div class="post--img">';

                                    //     echo '<a href="news-single-v1.html" class="thumb">
                                    //                 <img src="'.$url_foto.'" alt=""></a>
                                    //             <a href="#" class="cat">'.$row['kategori'].'</a>
                                    //             </div>
                                    //             </div>

                                    //             <div class="col-md-8 col-sm-12 col-xs-8 col-xxs-12">
                                    //                 <div class="post--info">
                                    //                     <ul class="nav meta">';

                                    //     echo '<li><a href="#">'.$row['penulis'].'</a></li>
                                    //             <li><a href="#">'.$row['tanggal_update'].'</a></li>
                                    //         </ul>';

                                    //     echo '<div class="title">
                                    //                 <h3 class="h4"><a href="news-single-v1.html" class="btn-link">
                                    //                 '.$row['judul_artikel'].'
                                    //                 </a></h3>
                                    //             </div>
                                    //         </div>';

                                    //         $potong_string = substr($row['konten_artikel'], 0, 200);

                                    //     echo '<div class="post--content">
                                    //             <p>'.$potong_string.'...</p>
                                    //         </div>';

                                    //     echo '<div class="post--action">
                                    //                     <a href="artikel-show.php?id_artikel='.$row['id_artikel'].'">Continue Reading...</a>
                                    //                 </div>
                                    //             </div>
                                    //         </div>
                                    //     </div>';

                                    //     echo "</li>";
                                    // }

                                ?>

                                    <!-- <li>
                                        
                                        <div class="post--item post--title-larger">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12 col-xs-4 col-xxs-12">
                                                    <div class="post--img">
                                                        <a href="news-single-v1.html" class="thumb"><img src="img/blog-img/post-02.jpg" alt=""></a>
                                                        <a href="#" class="cat">War</a>
                                                    </div>
                                                </div>

                                                <div class="col-md-8 col-sm-12 col-xs-8 col-xxs-12">
                                                    <div class="post--info">
                                                        <ul class="nav meta">
                                                            <li><a href="#">Chemosh</a></li>
                                                            <li><a href="#">05 March 2016</a></li>
                                                        </ul>

                                                        <div class="title">
                                                            <h3 class="h4"><a href="news-single-v1.html" class="btn-link">Credibly pontificate highly efficient manufactured products and enabled data.</a></h3>
                                                        </div>
                                                    </div>

                                                    <div class="post--content">
                                                        <p>Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus.</p>
                                                    </div>

                                                    <div class="post--action">
                                                        <a href="news-single-v1.html">Continue Reading...</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                 
                                    </li> -->

                                </ul>
                            </div>
                            <!-- Post Items End -->

                            <!-- Advertisement Start -->
                            <div class="ad--space">
                                <a href="#">
                                    <img src="img/ads-img/ad-728x90-03.jpg" alt="" class="center-block">
                                </a>
                            </div>
                            <!-- Advertisement End -->


                            <!-- Pagination Start -->
                            <div class="pagination--wrapper clearfix bdtop--1 bd--color-2 ptop--60 pbottom--30">
                                <p class="pagination-hint float--left">Page 02 of 03</p>

                                <ul class="pagination float--right">
                                    <li><a href="#"><i class="fa fa-long-arrow-left"></i></a></li>
                                    <li><a href="#">01</a></li>
                                    <li class="active"><span>02</span></li>
                                    <li><a href="#">03</a></li>
                                    <li>
                                        <i class="fa fa-angle-double-right"></i>
                                        <i class="fa fa-angle-double-right"></i>
                                        <i class="fa fa-angle-double-right"></i>
                                    </li>
                                    <li><a href="#">20</a></li>
                                    <li><a href="#"><i class="fa fa-long-arrow-right"></i></a></li>
                                </ul>
                            </div>

                            <!-- Pagination End -->
                        </div>
                    </div>
                    <!-- Main Content End -->

                    <!-- Main Sidebar Start -->
                    <div class="main--sidebar col-md-4 col-sm-5 ptop--30 pbottom--30" data-sticky-content="true">
                        <div class="sticky-content-inner">
                            <!-- Widget Start -->
                            <div class="widget">
                                <!-- Search Widget Start -->
                                <div class="search--widget">
                                    <form action="artikel-search.php" data-form="validate">
                                        <div class="input-group">
                                            <input type="search" name="search" placeholder="Search..." class="form-control" required>

                                            <div class="input-group-btn">
                                                <button type="submit" class="btn-link"><i class="fa fa-search"></i></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- Search Widget End -->
                            </div>
                            <!-- Widget End -->

                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">Catagory</h2>
                                    <i class="icon fa fa-folder-open-o"></i>
                                </div>

                                <!-- Nav Widget Start -->
                                <div class="nav--widget">
                                    <ul class="nav">

                                    <?php

                                        // //query untuk mengambil data dari tabel Artikel
                                        // $query_kategori = "SELECT DISTINCT kategori FROM artikel WHERE disable = 1 AND hapus = 1";

                                        // //eksekusi query dan simpan hasilnya ke dalam variabel $result
                                        // $result_kategori = mysqli_query($conn, $query_kategori);

                                        // //tampilkan data yang ditemukan dalam bentuk tabel
                                        // while ($row_kete = mysqli_fetch_assoc($result_kategori)) 
                                        // {

                                        //     $cari_kategory = $row_kete['kategori'];
                                        //     $cari_jumlah =  "SELECT COUNT(*) as jumlah_baris FROM artikel WHERE kategori = '$cari_kategory'";

                                        //     // eksekusi query dan simpan hasilnya ke dalam variabel $result
                                        //     $result_cari_jumlah = mysqli_query($conn, $cari_jumlah);

                                        //     // echo "<br>";
                                        //     $row = mysqli_fetch_assoc($result_cari_jumlah);

                                        //     $jumlah_baris = $row['jumlah_baris'];
                                        //     // echo $jumlah_baris. "<----- <br>";

                                        //     echo " <li><a href='artikel-search.php?cari_ketegori=".$cari_kategory."'><span>".$cari_kategory."</span><span>(".$jumlah_baris.")</span></a></li>";

                                        // }

                                    ?>
                                    </ul>
                                </div>
                                <!-- Nav Widget End -->

                            </div>
                            <!-- Widget End -->

                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">Tags</h2>
                                    <i class="icon fa fa-tags"></i>
                                </div>

                                <!-- Tags Widget Start -->
                                <div class="tags--widget style--1">
                                    <ul class="nav">

                                    <?php

                                        // //query untuk mengambil data dari tabel Artikel
                                        // $query_tag = "SELECT DISTINCT tag FROM artikel_tag";

                                        // //eksekusi query dan simpan hasilnya ke dalam variabel $result
                                        // $result_tag = mysqli_query($conn, $query_tag);

                                        // //tampilkan data yang ditemukan dalam bentuk tabel
                                        // while ($row_tag = mysqli_fetch_assoc($result_tag)){
                                    
                                        //     $cari_tag = $row_tag['tag'];
                                        //     $search_tag = str_replace("#", "", $cari_tag);

                                        //     echo "<li><a href='artikel-search.php?cari_tag=".$search_tag."'>".$cari_tag."</a></li>";

                                        // }
                                    ?>

                                    </ul>
                                </div>
                                <!-- Tags Widget End -->

                            </div>
                            <!-- Widget End -->

                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">Archives</h2>
                                    <i class="icon fa fa-folder-open-o"></i>
                                </div>

                                <!-- Nav Widget Start -->
                                <div class="nav--widget">
                                    <ul class="nav">

                                        <?php

                                            // // Mendapatkan bulan dan tahun sekarang
                                            // $bulanSekarang = date('m');
                                            // $tahunSekarang = date('Y');

                                            // // Perulangan untuk mengambil data dari bulan ini dan 9 bulan sebelumnya
                                            // for ($i = 0; $i < 10; $i++) {
                                            //     // Mengatur bulan dan tahun berdasarkan iterasi
                                            //     $bulan = date('m', strtotime("-$i months", strtotime("$tahunSekarang-$bulanSekarang-01")));
                                            //     $tahun = date('Y', strtotime("-$i months", strtotime("$tahunSekarang-$bulanSekarang-01")));

                                            //     // Array penamaan bulan dalam bahasa Indonesia
                                            //     $namaBulan = array(
                                            //         "01" => 'Januari',
                                            //         "02" => 'Februari',
                                            //         "03" => 'Maret',
                                            //         "04" => 'April',
                                            //         "05" => 'Mei',
                                            //         "06" => 'Juni',
                                            //         "07" => 'Juli',
                                            //         "08" => 'Agustus',
                                            //         "09" => 'September',
                                            //         "10" => 'Oktober',
                                            //         "11" => 'November',
                                            //         "12" => 'Desember'
                                            //     );

                                            //     // Mendapatkan penamaan bulan berdasarkan nilai bulan
                                            //     $namaBulanIndonesia = $namaBulan[$bulan];

                                            //     // echo "<br>";
                                            //     // echo "bulan : ". $namaBulanIndonesia. ", tahun : ".$tahun; echo "<br>";
                                            //     $jumlah_artikel =  "SELECT COUNT(*) as jumlah_artikel FROM artikel WHERE MONTH(tanggal_update) = $bulan AND YEAR(tanggal_update) = $tahun";

                                            //     // eksekusi query dan simpan hasilnya ke dalam variabel $result
                                            //     $result_jumlah_artikel = mysqli_query($conn, $jumlah_artikel);
    
                                            //     // echo "<br>";
                                            //     $row_artikel = mysqli_fetch_assoc($result_jumlah_artikel);
                                            //     $jumlah_artikel = $row_artikel['jumlah_artikel'];
                                            //     echo "<li><a href='artikel-search.php?cari_by_date=".$bulan.",".$tahun."'><span> ". $namaBulanIndonesia." ".$tahun." </span><span>(".$jumlah_artikel.")</span></a></li>";

                                            // }

                                        ?>

                                    </ul>
                                </div>
                                <!-- Nav Widget End -->

                            </div>
                            <!-- Widget End -->

                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">Stay Connected</h2>
                                    <i class="icon fa fa-share-alt"></i>
                                </div>

                                <!-- Social Widget Start -->
                                <div class="social--widget style--1">
                                    <ul class="nav">
                                        <li class="facebook">
                                            <a href="#">
                                                <span class="icon"><i class="fa fa-facebook-f"></i></span>
                                                <span class="count">521</span>
                                                <span class="title">Likes</span>
                                            </a>
                                        </li>
                                        <li class="twitter">
                                            <a href="#">
                                                <span class="icon"><i class="fa fa-twitter"></i></span>
                                                <span class="count">3297</span>
                                                <span class="title">Followers</span>
                                            </a>
                                        </li>
                                        <li class="google-plus">
                                            <a href="#">
                                                <span class="icon"><i class="fa fa-google-plus"></i></span>
                                                <span class="count">596282</span>
                                                <span class="title">Followers</span>
                                            </a>
                                        </li>
                                        <li class="rss">
                                            <a href="#">
                                                <span class="icon"><i class="fa fa-rss"></i></span>
                                                <span class="count">521</span>
                                                <span class="title">Subscriber</span>
                                            </a>
                                        </li>
                                        <li class="vimeo">
                                            <a href="#">
                                                <span class="icon"><i class="fa fa-vimeo"></i></span>
                                                <span class="count">3297</span>
                                                <span class="title">Followers</span>
                                            </a>
                                        </li>
                                        <li class="youtube">
                                            <a href="#">
                                                <span class="icon"><i class="fa fa-youtube-square"></i></span>
                                                <span class="count">596282</span>
                                                <span class="title">Subscriber</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- Social Widget End -->
                            </div>
                            <!-- Widget End -->

                          
                        </div>
                    </div>
                    <!-- Main Sidebar End -->
                </div>
            </div>
        </div>
        <!-- Main Content Section End -->

        <!-- Footer Section Start -->
        <footer class="footer--section">
            <!-- Footer Widgets Start -->
            <div class="footer--widgets pd--30-0 bg--color-2">
                <div class="container">
                    <div class="row AdjustRow">
                        <div class="col-md-3 col-xs-6 col-xxs-12 ptop--30 pbottom--30">
                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">About Us</h2>

                                    <i class="icon fa fa-exclamation"></i>
                                </div>

                                <!-- About Widget Start -->
                                <div class="about--widget">
                                    <div class="content">
                                        <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium laborum et dolorum fuga.</p>
                                    </div>

                                    <div class="action">
                                        <a href="#" class="btn-link">Read More<i class="fa flm fa-angle-double-right"></i></a>
                                    </div>

                                    <ul class="nav">
                                        <li>
                                            <i class="fa fa-map"></i>
                                            <span>143/C, Fake Street, Melborne, Australia</span>
                                        </li>
                                        <li>
                                            <i class="fa fa-envelope-o"></i>
                                            <a href="mailto:example@example.com">example@example.com</a>
                                        </li>
                                        <li>
                                            <i class="fa fa-phone"></i>
                                            <a href="tel:+123456789">+123 456 (789)</a>
                                        </li>
                                    </ul>
                                </div>
                                <!-- About Widget End -->
                            </div>
                            <!-- Widget End -->
                        </div>

                        <div class="col-md-3 col-xs-6 col-xxs-12 ptop--30 pbottom--30">
                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">Usefull Info Links</h2>

                                    <i class="icon fa fa-expand"></i>
                                </div>

                                <!-- Links Widget Start -->
                                <div class="links--widget">
                                    <ul class="nav">
                                        <li><a href="#" class="fa-angle-right">Gadgets</a></li>
                                        <li><a href="#" class="fa-angle-right">Shop</a></li>
                                        <li><a href="#" class="fa-angle-right">Term and Conditions</a></li>
                                        <li><a href="#" class="fa-angle-right">Forums</a></li>
                                        <li><a href="#" class="fa-angle-right">Top News of This Week</a></li>
                                        <li><a href="#" class="fa-angle-right">Special Recipes</a></li>
                                        <li><a href="#" class="fa-angle-right">Sign Up</a></li>
                                    </ul>
                                </div>
                                <!-- Links Widget End -->
                            </div>
                            <!-- Widget End -->
                        </div>

                        <div class="col-md-3 col-xs-6 col-xxs-12 ptop--30 pbottom--30">
                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">Advertisements</h2>

                                    <i class="icon fa fa-bullhorn"></i>
                                </div>

                                <!-- Links Widget Start -->
                                <div class="links--widget">
                                    <ul class="nav">
                                        <li><a href="#" class="fa-angle-right">Post an Add</a></li>
                                        <li><a href="#" class="fa-angle-right">Adds Renew</a></li>
                                        <li><a href="#" class="fa-angle-right">Price of Advertisements</a></li>
                                        <li><a href="#" class="fa-angle-right">Adds Closed</a></li>
                                        <li><a href="#" class="fa-angle-right">Monthly or Yearly</a></li>
                                        <li><a href="#" class="fa-angle-right">Trial Adds</a></li>
                                        <li><a href="#" class="fa-angle-right">Add Making</a></li>
                                    </ul>
                                </div>
                                <!-- Links Widget End -->
                            </div>
                            <!-- Widget End -->
                        </div>

                        <div class="col-md-3 col-xs-6 col-xxs-12 ptop--30 pbottom--30">
                            <!-- Widget Start -->
                            <div class="widget">
                                <div class="widget--title">
                                    <h2 class="h4">Career</h2>

                                    <i class="icon fa fa-user-o"></i>
                                </div>

                                <!-- Links Widget Start -->
                                <div class="links--widget">
                                    <ul class="nav">
                                        <li><a href="#" class="fa-angle-right">Available Post</a></li>
                                        <li><a href="#" class="fa-angle-right">Career Details</a></li>
                                        <li><a href="#" class="fa-angle-right">How to Apply?</a></li>
                                        <li><a href="#" class="fa-angle-right">Freelence Job</a></li>
                                        <li><a href="#" class="fa-angle-right">Be a Member</a></li>
                                        <li><a href="#" class="fa-angle-right">Apply Now</a></li>
                                        <li><a href="#" class="fa-angle-right">Send Your Resume</a></li>
                                    </ul>
                                </div>
                                <!-- Links Widget End -->
                            </div>
                            <!-- Widget End -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Widgets End -->

            <!-- Footer Copyright Start -->
            <div class="footer--copyright bg--color-3">
                <div class="social--bg bg--color-1"></div>

                <div class="container">
                    <p class="text float--left">&copy; 2017 <a href="#">USNEWS</a>. All Rights Reserved.</p>

                    <ul class="nav social float--right">
                        <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                        <li><a href="#"><i class="fa fa-youtube-play"></i></a></li>
                    </ul>

                    <ul class="nav links float--right">
                        <li><a href="#">Home</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
            </div>
            <!-- Footer Copyright End -->
        </footer>
        <!-- Footer Section End -->
    </div>
    <!-- Wrapper End -->

    <!-- Sticky Social Start -->
    <div id="stickySocial" class="sticky--right">
        <ul class="nav">
            <li>
                <a href="#">
                    <i class="fa fa-facebook"></i>
                    <span>Follow Us On Facebook</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-twitter"></i>
                    <span>Follow Us On Twitter</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-google-plus"></i>
                    <span>Follow Us On Google Plus</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-rss"></i>
                    <span>Follow Us On RSS</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-vimeo"></i>
                    <span>Follow Us On Vimeo</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-youtube-play"></i>
                    <span>Follow Us On Youtube Play</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa fa-linkedin"></i>
                    <span>Follow Us On LinkedIn</span>
                </a>
            </li>
        </ul>
    </div>
    <!-- Sticky Social End -->

    <!-- Back To Top Button Start -->
    <div id="backToTop">
        <a href="#"><i class="fa fa-angle-double-up"></i></a>
    </div>
    <!-- Back To Top Button End -->

    <!-- ==== jQuery Library ==== -->
    <script src="js/jquery-3.2.1.min.js"></script>

    <!-- ==== Bootstrap Framework ==== -->
    <script src="js/bootstrap.min.js"></script>

    <!-- ==== StickyJS Plugin ==== -->
    <script src="js/jquery.sticky.min.js"></script>

    <!-- ==== HoverIntent Plugin ==== -->
    <script src="js/jquery.hoverIntent.min.js"></script>

    <!-- ==== Marquee Plugin ==== -->
    <script src="js/jquery.marquee.min.js"></script>

    <!-- ==== Validation Plugin ==== -->
    <script src="js/jquery.validate.min.js"></script>

    <!-- ==== Isotope Plugin ==== -->
    <script src="js/isotope.min.js"></script>

    <!-- ==== Resize Sensor Plugin ==== -->
    <script src="js/resizesensor.min.js"></script>

    <!-- ==== Sticky Sidebar Plugin ==== -->
    <script src="js/theia-sticky-sidebar.min.js"></script>

    <!-- ==== Zoom Plugin ==== -->
    <script src="js/jquery.zoom.min.js"></script>

    <!-- ==== Bar Rating Plugin ==== -->
    <script src="js/jquery.barrating.min.js"></script>

    <!-- ==== Countdown Plugin ==== -->
    <script src="js/jquery.countdown.min.js"></script>

    <!-- ==== RetinaJS Plugin ==== -->
    <script src="js/retina.min.js"></script>

    <!-- ==== Google Map API ==== -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBK9f7sXWmqQ1E-ufRXV3VpXOn_ifKsDuc"></script>

    <!-- ==== Main JavaScript ==== -->
    <script src="js/main.js"></script>

</body>
</html>
