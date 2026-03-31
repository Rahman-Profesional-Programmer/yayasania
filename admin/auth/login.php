<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

session_start();
// Jika sudah login, langsung ke dashboard
if (!empty($_SESSION['email'])) {
    redirect(ADMIN_URL . 'menu/index.php');
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= PUBLIC_URL ?>assets/img/logoyysn.png" type="image/png" />
    <link href="<?= ADMIN_ASSETS ?>css/bootstrap.min.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/bootstrap-extended.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/style.css" rel="stylesheet" />
    <link href="<?= ADMIN_ASSETS ?>css/icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="<?= ADMIN_ASSETS ?>css/pace.min.css" rel="stylesheet" />
    <title>Login - Admin Yayasan IA</title>
</head>
<body>

<div class="wrapper">
    <main class="authentication-content">
        <div class="container-fluid">
            <div class="authentication-card">
                <div class="card shadow rounded-0 overflow-hidden">
                    <div class="row g-0">
                        <div class="col-lg-6 bg-login d-flex align-items-center justify-content-center">
                            <img src="<?= ADMIN_ASSETS ?>images/error/login-img.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="col-lg-6">
                            <div class="card-body p-4 p-sm-5">
                                <h5 class="card-title">Sign In</h5>
                                <p class="card-text mb-5">Admin Yayasan Ihsanul Amal Alabio</p>

                                <?php if (isset($_SESSION['error'])): ?>
                                    <div class="alert alert-danger py-2">
                                        <?= htmlspecialchars($_SESSION['error']) ?>
                                    </div>
                                    <?php unset($_SESSION['error']); ?>
                                <?php endif; ?>

                                <form class="form-body" action="<?= ADMIN_URL ?>auth/proses.php" method="POST">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="inputEmailAddress" class="form-label">Email Address</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                                    <i class="bi bi-envelope-fill"></i>
                                                </div>
                                                <input type="email" name="email" class="form-control radius-30 ps-5"
                                                    id="inputEmailAddress" placeholder="Email Address" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="inputChoosePassword" class="form-label">Password</label>
                                            <div class="ms-auto position-relative">
                                                <div class="position-absolute top-50 translate-middle-y search-icon px-3">
                                                    <i class="bi bi-lock-fill"></i>
                                                </div>
                                                <input type="password" name="password" class="form-control radius-30 ps-5"
                                                    id="inputChoosePassword" placeholder="Enter Password" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary radius-30">Sign In</button>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <p class="mb-0">Butuh bantuan? Hubungi: 085281547464</p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="<?= ADMIN_ASSETS ?>js/bootstrap.bundle.min.js"></script>
<script src="<?= ADMIN_ASSETS ?>js/pace.min.js"></script>
</body>
</html>
