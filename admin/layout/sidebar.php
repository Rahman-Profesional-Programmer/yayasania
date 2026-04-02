<!--start sidebar -->
<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="<?= ADMIN_ASSETS ?>images/logo-icon.png" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">Admin Yayasan</h4>
        </div>
        <div class="toggle-icon ms-auto"><i class="bi bi-list"></i></div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

        <li class="menu-label">Konten Web</li>

        <li>
            <a href="<?= ADMIN_URL ?>menu/index.php">
                <div class="parent-icon"><i class="lni lni-display"></i></div>
                <div class="menu-title">Pengaturan Menu</div>
            </a>
        </li>

        <li>
            <a href="<?= ADMIN_URL ?>artikel/index.php">
                <div class="parent-icon"><i class="lni lni-library"></i></div>
                <div class="menu-title">Artikel & Berita</div>
            </a>
        </li>

        <li class="menu-label">Sistem</li>

        <?php if (isAdmin()): ?>
        <li>
            <a href="<?= ADMIN_URL ?>users/index.php">
                <div class="parent-icon"><i class="bi bi-people-fill"></i></div>
                <div class="menu-title">Manajemen Pengguna</div>
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="<?= PUBLIC_URL ?>home.php" target="_blank">
                <div class="parent-icon"><i class="bi bi-globe"></i></div>
                <div class="menu-title">Lihat Website</div>
            </a>
        </li>

        <li>
            <a href="<?= ADMIN_URL ?>auth/logout.php">
                <div class="parent-icon"><i class="bi bi-lock-fill"></i></div>
                <div class="menu-title">Logout</div>
            </a>
        </li>

    </ul>
    <!--end navigation-->
</aside>
<!--end sidebar -->
