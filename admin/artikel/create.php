<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$pageTitle = "Tambah Artikel";

// Ambil daftar kategori yang sudah ada
$sql_kat   = "SELECT DISTINCT kategori FROM artikel ORDER BY kategori";
$res_kat   = $conn->query($sql_kat);
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<!--start content-->
<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Artikel</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>artikel/index.php">Artikel</a></li>
                    <li class="breadcrumb-item active">Tambah</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card">
        <div class="card-header py-3">
            <h6 class="mb-0">Tambah Artikel Baru</h6>
        </div>
        <div class="card-body">
            <form class="row g-3" action="<?= ADMIN_URL ?>artikel/store.php" method="POST" enctype="multipart/form-data">

                <div class="col-12">
                    <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" name="judul_artikel" class="form-control" placeholder="Judul artikel" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($k = $res_kat->fetch_assoc()): ?>
                            <option value="<?= e($k['kategori']) ?>"><?= e($k['kategori']) ?></option>
                        <?php endwhile; ?>
                        <option value="lain">+ Kategori Baru</option>
                    </select>
                </div>

                <div class="col-12 col-md-6" id="kategori-baru-wrap" style="display:none">
                    <label class="form-label">Nama Kategori Baru</label>
                    <input type="text" name="kategori_baru" class="form-control" placeholder="Nama kategori baru">
                </div>

                <div class="col-12">
                    <label class="form-label">Foto Artikel <span class="text-danger">*</span></label>
                    <input type="file" name="foto" class="form-control" accept="image/*" required>
                    <small class="text-muted">Maksimal 5MB. Format: jpg, png, webp</small>
                </div>

                <div class="col-12">
                    <label class="form-label">Tag <small class="text-muted">(pisahkan dengan koma)</small></label>
                    <input type="text" name="tag" class="form-control" placeholder="berita, yayasan, pendidikan">
                </div>

                <div class="col-12">
                    <label class="form-label">Isi Artikel <span class="text-danger">*</span></label>
                    <textarea name="isi_artikel" class="form-control" rows="12" placeholder="Tulis isi artikel di sini..." required></textarea>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary px-5">Simpan Artikel</button>
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
