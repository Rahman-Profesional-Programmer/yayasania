<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$pageTitle      = "Pengaturan Menu";
$menu_utama     = $conn->query("SELECT * FROM menu_utama ORDER BY urutan");
$sub_menu       = $conn->query("SELECT * FROM sub_menu ORDER BY menu_utama, urutan");
$menu_for_select = $conn->query("SELECT * FROM menu_utama ORDER BY urutan");
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Konten</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>menu/index.php"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active">Pengaturan Menu</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills nav-pills-danger mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" data-bs-toggle="pill" href="#tab-menu-utama" role="tab">Menu Utama</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" data-bs-toggle="pill" href="#tab-sub-menu" role="tab">Sub Menu</a>
                </li>
            </ul>

            <div class="tab-content">

                <!-- ===== TAB MENU UTAMA ===== -->
                <div class="tab-pane fade show active" id="tab-menu-utama">
                    <div class="row">
                        <!-- Form Tambah -->
                        <div class="col-12 col-lg-4 d-flex">
                            <div class="card border shadow-none w-100">
                                <div class="card-body">
                                    <h6 class="mb-3">Tambah Menu Utama</h6>
                                    <form class="row g-3" action="<?= ADMIN_URL ?>menu/store.php" method="POST">
                                        <div class="col-12">
                                            <label class="form-label">Urutan</label>
                                            <input type="number" name="urutan_menu" class="form-control" placeholder="1, 2, 3 ..." required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Nama Menu</label>
                                            <input type="text" name="nama_menu" class="form-control" placeholder="Nama Menu" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Link Menu</label>
                                            <input type="text" name="link_menu" class="form-control" placeholder="home.php">
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100">+ Tambah Menu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Menu Utama -->
                        <div class="col-12 col-lg-8 d-flex">
                            <div class="card border shadow-none w-100">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th><th>Urutan</th><th>Nama Menu</th><th>Link</th><th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php while ($row = $menu_utama->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= $row['urutan'] ?></td>
                                                    <td><?= e($row['nama_menu']) ?></td>
                                                    <td><?= e($row['link_menu']) ?></td>
                                                    <td>
                                                        <div class="d-flex gap-3 fs-6">
                                                            <?php if ($row['enable'] == 1): ?>
                                                                <a onclick="toggleMenu(<?= $row['id'] ?>, 'disable')" class="text-primary" style="cursor:pointer" title="Nonaktifkan"><i class="bi bi-eye-fill"></i></a>
                                                            <?php else: ?>
                                                                <a onclick="toggleMenu(<?= $row['id'] ?>, 'enable')" class="text-secondary" style="cursor:pointer" title="Aktifkan"><i class="bi bi-eye-slash-fill"></i></a>
                                                            <?php endif; ?>
                                                            <a href="<?= ADMIN_URL ?>menu/edit.php?id=<?= $row['id'] ?>" class="text-warning" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                                            <a onclick="hapusMenu(<?= $row['id'] ?>)" class="text-danger" style="cursor:pointer" title="Hapus"><i class="bi bi-trash-fill"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== TAB SUB MENU ===== -->
                <div class="tab-pane fade" id="tab-sub-menu">
                    <div class="row">
                        <!-- Form Tambah Sub Menu -->
                        <div class="col-12 col-lg-4 d-flex">
                            <div class="card border shadow-none w-100">
                                <div class="card-body">
                                    <h6 class="mb-3">Tambah Sub Menu</h6>
                                    <form class="row g-3" action="<?= ADMIN_URL ?>menu/sub/store.php" method="POST">
                                        <div class="col-12">
                                            <label class="form-label">Urutan</label>
                                            <input type="number" name="urutan_sub_menu" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Nama Sub Menu</label>
                                            <input type="text" name="nama_sub_menu" class="form-control" required>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Link Sub Menu</label>
                                            <input type="text" name="link_sub_menu" class="form-control">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Menu Utama (Parent)</label>
                                            <select name="menu_utama" class="form-select" required>
                                                <option value="">-- Pilih Menu Utama --</option>
                                                <?php while ($m = $menu_for_select->fetch_assoc()): ?>
                                                    <option value="<?= e($m['nama_menu']) ?>"><?= e($m['nama_menu']) ?></option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <button class="btn btn-primary w-100">+ Tambah Sub Menu</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Sub Menu -->
                        <div class="col-12 col-lg-8 d-flex">
                            <div class="card border shadow-none w-100">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th><th>Urutan</th><th>Nama</th><th>Link</th><th>Parent</th><th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php while ($row = $sub_menu->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?= $row['id'] ?></td>
                                                    <td><?= $row['urutan'] ?></td>
                                                    <td><?= e($row['nama_menu']) ?></td>
                                                    <td><?= e($row['link_menu']) ?></td>
                                                    <td><?= e($row['menu_utama']) ?></td>
                                                    <td>
                                                        <div class="d-flex gap-3 fs-6">
                                                            <?php if ($row['enable'] == 1): ?>
                                                                <a onclick="toggleSub(<?= $row['id'] ?>, 'disable')" class="text-primary" style="cursor:pointer"><i class="bi bi-eye-fill"></i></a>
                                                            <?php else: ?>
                                                                <a onclick="toggleSub(<?= $row['id'] ?>, 'enable')" class="text-secondary" style="cursor:pointer"><i class="bi bi-eye-slash-fill"></i></a>
                                                            <?php endif; ?>
                                                            <a href="<?= ADMIN_URL ?>menu/sub/edit.php?id=<?= $row['id'] ?>" class="text-warning"><i class="bi bi-pencil-fill"></i></a>
                                                            <a onclick="hapusSub(<?= $row['id'] ?>)" class="text-danger" style="cursor:pointer"><i class="bi bi-trash-fill"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
function toggleMenu(id, aksi) {
    if (!confirm('Ubah status menu ini?')) return;
    let url = aksi === 'enable' ? '<?= ADMIN_URL ?>menu/enable.php' : '<?= ADMIN_URL ?>menu/disable.php';
    fetch(url, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+id}).then(() => location.reload());
}
function hapusMenu(id) {
    if (!confirm('Hapus menu ini?')) return;
    fetch('<?= ADMIN_URL ?>menu/delete.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+id}).then(() => location.reload());
}
function toggleSub(id, aksi) {
    if (!confirm('Ubah status sub menu ini?')) return;
    let url = aksi === 'enable' ? '<?= ADMIN_URL ?>menu/sub/enable.php' : '<?= ADMIN_URL ?>menu/sub/disable.php';
    fetch(url, {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+id}).then(() => location.reload());
}
function hapusSub(id) {
    if (!confirm('Hapus sub menu ini?')) return;
    fetch('<?= ADMIN_URL ?>menu/sub/delete.php', {method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+id}).then(() => location.reload());
}
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
