<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) redirect(ADMIN_URL . 'artikel/index.php');

$stmt = $conn->prepare("SELECT * FROM artikel WHERE id_artikel = ? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$artikel = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$artikel) redirect(ADMIN_URL . 'artikel/index.php');

// Ambil tags artikel ini
$stmt_tag = $conn->prepare("SELECT GROUP_CONCAT(tag SEPARATOR ', ') as tags FROM artikel_tag WHERE id_artikel = ?");
$stmt_tag->bind_param("i", $id);
$stmt_tag->execute();
$row_tag  = $stmt_tag->get_result()->fetch_assoc();
$stmt_tag->close();
$tags_string = $row_tag['tags'] ?? '';

// Ambil kategori
$res_kat = $conn->query("SELECT DISTINCT kategori FROM artikel ORDER BY kategori");
$pageTitle = "Edit Artikel";
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Artikel</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>artikel/index.php">Artikel</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">Edit Artikel #<?= $id ?></h6>
        </div>
        <div class="card-body">
            <form class="row g-3" action="<?= ADMIN_URL ?>artikel/update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_artikel" value="<?= $id ?>">

                <div class="col-12">
                    <label class="form-label">Judul Artikel</label>
                    <input type="text" name="judul_artikel" class="form-control" value="<?= e($artikel['judul_artikel']) ?>" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($k = $res_kat->fetch_assoc()): ?>
                            <option value="<?= e($k['kategori']) ?>" <?= $k['kategori'] === $artikel['kategori'] ? 'selected' : '' ?>>
                                <?= e($k['kategori']) ?>
                            </option>
                        <?php endwhile; ?>
                        <option value="lain">+ Kategori Baru</option>
                    </select>
                </div>

                <div class="col-12 col-md-6" id="kategori-baru-wrap" style="display:none">
                    <label class="form-label">Nama Kategori Baru</label>
                    <input type="text" name="kategori_baru" class="form-control" placeholder="Nama kategori baru">
                </div>

                <div class="col-12">
                    <label class="form-label">Ganti Foto <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small></label>
                    <?php if ($artikel['gambar']): ?>
                        <div class="mb-2">
                            <img src="<?= BASE_URL . e($artikel['gambar']) ?>" height="80" alt="foto saat ini">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>

                <div class="col-12">
                    <label class="form-label">Tag <small class="text-muted">(pisahkan dengan koma)</small></label>
                    <input type="text" name="tag" class="form-control" value="<?= e($tags_string) ?>">
                </div>

                <div class="col-12">
                    <label class="form-label">Isi Artikel</label>
                    <textarea name="isi_artikel" class="form-control" rows="14" required><?= e($artikel['konten_artikel']) ?></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-5">Update Artikel</button>
                    <a href="<?= ADMIN_URL ?>artikel/index.php" class="btn btn-secondary ms-2">Batal</a>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
document.querySelector('select[name="kategori"]').addEventListener('change', function () {
    document.getElementById('kategori-baru-wrap').style.display = this.value === 'lain' ? 'block' : 'none';
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
