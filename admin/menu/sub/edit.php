<?php
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/functions.php';
session_start();
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) redirect(ADMIN_URL . 'menu/index.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u  = $_POST['urutan_menu']  ?? '';
    $n  = $_POST['nama_menu']    ?? '';
    $l  = $_POST['link_menu']    ?? '';
    $mu = $_POST['menu_utama']   ?? '';
    $stmt = $conn->prepare("UPDATE sub_menu SET urutan=?, nama_menu=?, link_menu=?, menu_utama=? WHERE id=?");
    $stmt->bind_param("isssi", $u, $n, $l, $mu, $id);
    $stmt->execute();
    $stmt->close();
    redirect(ADMIN_URL . 'menu/index.php');
}

$stmt = $conn->prepare("SELECT * FROM sub_menu WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$sub = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$sub) redirect(ADMIN_URL . 'menu/index.php');

$menu_utama = $conn->query("SELECT * FROM menu_utama ORDER BY urutan");
$pageTitle  = "Edit Sub Menu";
?>
<?php require_once __DIR__ . '/../../layout/header.php'; ?>
<?php require_once __DIR__ . '/../../layout/sidebar.php'; ?>

<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Menu</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>menu/index.php">Menu</a></li>
                    <li class="breadcrumb-item active">Edit Sub Menu</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="card">
        <div class="card-header py-3"><h6 class="mb-0">Edit Sub Menu #<?= $id ?></h6></div>
        <div class="card-body">
            <form class="row g-3" method="POST">
                <div class="col-12 col-md-3">
                    <label class="form-label">Urutan</label>
                    <input type="number" name="urutan_menu" class="form-control" value="<?= $sub['urutan'] ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Nama Sub Menu</label>
                    <input type="text" name="nama_menu" class="form-control" value="<?= e($sub['nama_menu']) ?>" required>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Link</label>
                    <input type="text" name="link_menu" class="form-control" value="<?= e($sub['link_menu']) ?>">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Menu Utama (Parent)</label>
                    <select name="menu_utama" class="form-select">
                        <?php while ($m = $menu_utama->fetch_assoc()): ?>
                            <option value="<?= e($m['nama_menu']) ?>" <?= $m['nama_menu'] === $sub['menu_utama'] ? 'selected' : '' ?>>
                                <?= e($m['nama_menu']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= ADMIN_URL ?>menu/index.php" class="btn btn-secondary ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
