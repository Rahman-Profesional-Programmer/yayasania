<?php
// Setiap halaman admin harus set $pageTitle sebelum include file ini
$pageTitle = $pageTitle ?? 'Admin Yayasan IA';
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= ADMIN_ASSETS ?>images/favicon-32x32.png" type="image/png" />
    <!--plugins-->
    <link href="<?= ADMIN_ASSETS ?>plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="<?= ADMIN_ASSETS ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/bootstrap-extended.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/style.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <!-- loader -->
    <link href="<?= ADMIN_ASSETS ?>css/pace.min.css" rel="stylesheet" />
    <!-- Theme Styles -->
    <link href="<?= ADMIN_ASSETS ?>css/dark-theme.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/light-theme.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/semi-dark.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/header-colors.css" rel="stylesheet" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>

<!--start wrapper-->
<div class="wrapper">

<!--start top header-->
<header class="top-header">
    <nav class="navbar navbar-expand gap-3">
        <div class="mobile-toggle-icon fs-3">
            <i class="bi bi-list"></i>
        </div>
        <form class="searchbar">
            <div class="position-absolute top-50 translate-middle-y search-icon ms-3"><i class="bi bi-search"></i></div>
            <input class="form-control" type="text" placeholder="Type here to search">
            <div class="position-absolute top-50 translate-middle-y search-close-icon"><i class="bi bi-x-lg"></i></div>
        </form>
        <div class="top-navbar-right ms-auto">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item dropdown dropdown-user-setting">
                    <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown">
                        <div class="user-setting d-flex align-items-center gap-3">
                            <img src="<?= ADMIN_ASSETS ?>images/avatars/avatar-1.png" class="user-img" alt="">
                            <div class="d-none d-sm-block">
                                <p class="user-name mb-0"><?= htmlspecialchars($_SESSION['name'] ?? 'Admin') ?></p>
                                <small class="mb-0 dropdown-user-designation">Administrator</small>
                            </div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= ADMIN_URL ?>auth/logout.php">
                                <div class="d-flex align-items-center">
                                    <div class=""><i class="bi bi-lock-fill"></i></div>
                                    <div class="ms-3"><span>Logout</span></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
</header>
<!--end top header-->
