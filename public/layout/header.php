<?php
// Setiap halaman public harus set $pageTitle sebelum include file ini
$pageTitle = $pageTitle ?? 'Yayasan Ihsanul Amal Alabio';
?>
<!DOCTYPE html>
<html dir="ltr" lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="icon" href="<?= PUBLIC_ASSETS ?>img/logoyysn.png" type="image/png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700">
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>css/font-awesome.min.css">
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>style.css">
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>css/responsive-style.css">
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>css/colors/theme-color-1.css">
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>css/custom.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>plugins/sweetalert2/css/sweetalert2.min.css">
    <!-- Select2 -->
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?= PUBLIC_ASSETS ?>plugins/select2/css/select2-bootstrap4.css">
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<div id="preloader">
    <div class="preloader bg--color-1--b" data-preloader="1">
        <div class="preloader--inner"></div>
    </div>
</div>

<div class="wrapper">
<header class="header--section header--style-1">
    <!-- Topbar -->
    <div class="header--topbar bg--color-2">
        <div class="container">
            <div class="float--left float--xs-none text-xs-center">
                <ul class="header--topbar-info nav">
                    <li><i class="fa fm fa-calendar"></i>
                        <?php
                            $nama_hari = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
                            echo "Hari ini adalah " . $nama_hari[date("w")] . ", " . date("j F Y");
                        ?>
                    </li>
                    <li><i class="fa fm fa-mixcloud"></i> Website Resmi Yayasan Ihsanul Amal Alabio</li>
                </ul>
            </div>
            <div class="float--right float--xs-none text-xs-center">
                <ul class="header--topbar-action nav">
                    <li><a href="<?= ADMIN_URL ?>auth/login.php" target="_blank"><i class="fa fm fa-user-o"></i>Login Admin</a></li>
                </ul>
                <ul class="header--topbar-social nav hidden-sm hidden-xxs">
                    <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="#"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="#"><i class="fa fa-youtube-play"></i></a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Logo bar -->
    <div class="header--mainbar">
        <div class="container">
            <div class="header--logo float--left float--sm-none text-sm-center">
                <h1 class="h1">
                    <a href="<?= PUBLIC_URL ?>home.php" class="btn-link">
                        <img src="<?= PUBLIC_ASSETS ?>img/logoyysn.png" alt="Ihsanul Amal Logo" width="120px">
                    </a>
                </h1>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <div class="header--navbar navbar bd--color-1 bg--color-1" data-trigger="sticky">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#headerNav"
                    aria-expanded="false" aria-controls="headerNav">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>

            <?php
                $sql_menu   = "SELECT * FROM menu_utama WHERE enable = 1 ORDER BY urutan";
                $res_menu   = $conn->query($sql_menu);

                if ($res_menu && $res_menu->num_rows > 0):
                    echo '<div id="headerNav" class="navbar-collapse collapse float--left">';
                    echo '<ul class="header--menu-links nav navbar-nav" data-trigger="hoverIntent">';
                    while ($menu_row = $res_menu->fetch_assoc()):
                        $nama_menu      = $menu_row['nama_menu'];
                        $sql_sub        = "SELECT * FROM sub_menu WHERE enable = 1 AND menu_utama = '$nama_menu' ORDER BY urutan";
                        $res_sub        = $conn->query($sql_sub);
                        $has_sub        = $res_sub && $res_sub->num_rows > 0;
                        $var_class      = $has_sub ? 'class="dropdown"' : '';
                        $komponen_a     = $has_sub ? 'href="#" class="dropdown-toggle" data-toggle="dropdown"' : 'href="' . PUBLIC_URL . $menu_row['link_menu'] . '"';
                        $komponen_i     = $has_sub ? '<i class="fa flm fa-angle-down"></i>' : '';
                        echo '<li ' . $var_class . '>';
                        echo '<a style="padding-left:20px;padding-right:20px;" ' . $komponen_a . '>' . htmlspecialchars($nama_menu) . $komponen_i . '</a>';
                        if ($has_sub) {
                            echo '<ul class="dropdown-menu">';
                            while ($sub_row = $res_sub->fetch_assoc()) {
                                echo '<li><a href="' . PUBLIC_URL . htmlspecialchars($sub_row['link_menu']) . '">' . htmlspecialchars($sub_row['nama_menu']) . '</a></li>';
                            }
                            echo '</ul>';
                        }
                        echo '</li>';
                    endwhile;
                    echo '</ul></div>';
                endif;
            ?>

            <form action="<?= PUBLIC_URL ?>artikel-search.php" method="GET" class="header--search-form float--right" data-form="validate">
                <input type="search" name="search" placeholder="Cari berita..." class="header--search-control form-control" required>
                <button type="submit" class="header--search-btn btn"><i class="header--search-icon fa fa-search"></i></button>
            </form>
        </div>
    </div>
</header>
