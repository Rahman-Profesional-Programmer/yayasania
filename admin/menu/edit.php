<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$id   = (int) ($_GET['id'] ?? 0);
if (!$id) redirect(ADMIN_URL . 'menu/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['urutan_menu'] ?? '';
    $n = $_POST['nama_menu']   ?? '';
    $l = $_POST['link_menu']   ?? '';
    $stmt = $conn->prepare("UPDATE menu_utama SET urutan=?, nama_menu=?, link_menu=? WHERE id=?");
    $stmt->bind_param("issi", $u, $n, $l, $id);
    $stmt->execute();
    $stmt->close();
    redirect(ADMIN_URL . 'menu/index.php');
}

$stmt = $conn->prepare("SELECT * FROM menu_utama WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$menu = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$menu) redirect(ADMIN_URL . 'menu/index.php');

$pageTitle = "Edit Menu Utama";
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Menu</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>menu/index.php">Menu</a></li>
                    <li class="breadcrumb-item active">Edit Menu Utama</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-header py-3"><h6 class="mb-0">Edit Menu Utama #<?= $id ?></h6></div>
        <div class="card-body">
            <form class="row g-3" method="POST">
                <div class="col-12 col-md-4">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan_menu" class="form-control" value="<?= $menu['urutan'] ?>">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Nama Menu</label>
                    <input type="text" name="nama_menu" class="form-control" value="<?= e($menu['nama_menu']) ?>" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Link Menu</label>
                    <input type="text" name="link_menu" class="form-control" value="<?= e($menu['link_menu']) ?>">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= ADMIN_URL ?>menu/index.php" class="btn btn-secondary ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
