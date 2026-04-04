<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$pageTitle = "Set Top 5 News";

// ── Ambil semua artikel aktif untuk opsi dropdown ────────────────
$artikelStmt = $conn->prepare(
    "SELECT id_artikel, judul_artikel, kategori
     FROM artikel
     WHERE hapus = 1 AND enable = 1
     ORDER BY tanggal_update DESC"
);
$artikelStmt->execute();
$artikelRows = $artikelStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$artikelStmt->close();

// ── Ambil konfigurasi top_news yang sudah tersimpan ──────────────
$savedPos = []; // $savedPos[posisi] = id_artikel
$savedStmt = $conn->query("SELECT posisi, id_artikel FROM top_news ORDER BY posisi ASC");
if ($savedStmt) {
    while ($r = $savedStmt->fetch_assoc()) {
        $savedPos[(int)$r['posisi']] = (int)$r['id_artikel'];
    }
}

// ── Label tiap posisi (sesuai layout banner halaman depan) ───────
$posisiLabel = [
    1 => 'Posisi 1 — Tengah Besar (Banner Utama)',
    2 => 'Posisi 2 — Kiri Atas',
    3 => 'Posisi 3 — Kiri Bawah',
    4 => 'Posisi 4 — Kanan Atas',
    5 => 'Posisi 5 — Kanan Bawah',
];

// ── Pesan flash dari store.php ───────────────────────────────────
$flashType = $_GET['status'] ?? '';
$flashMsg  = match ($flashType) {
    'ok'    => ['success', 'Top 5 news berhasil disimpan.'],
    'error' => ['danger',  'Gagal menyimpan. Pastikan artikel yang dipilih valid.'],
    default => null,
};
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<!--start content-->
<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Konten</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="<?= ADMIN_URL ?>menu/index.php"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item active">Set Top 5 News</li>
                </ol>
            </nav>
        </div>
    </div>

    <?php if ($flashMsg): ?>
    <div class="alert alert-<?= $flashMsg[0] ?> alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($flashMsg[1]) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Pilih Artikel untuk Banner Utama Halaman Depan</h5>
            <small class="text-muted">
                Banner terdiri dari 5 posisi: 1 banner besar di tengah, 2 di kiri, dan 2 di kanan.
            </small>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= ADMIN_URL ?>top-news/store.php">

                <!-- Ilustrasi tata letak posisi -->
                <div class="row g-2 mb-4 text-center small">
                    <div class="col-3">
                        <div class="border rounded p-2 bg-light">
                            <div class="fw-semibold">Kiri Atas</div>
                            <div class="text-muted">Posisi 2</div>
                        </div>
                        <div class="border rounded p-2 bg-light mt-2">
                            <div class="fw-semibold">Kiri Bawah</div>
                            <div class="text-muted">Posisi 3</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-4 bg-primary bg-opacity-10 h-100 d-flex align-items-center justify-content-center">
                            <div>
                                <div class="fw-bold">Tengah Besar</div>
                                <div class="text-muted">Posisi 1</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="border rounded p-2 bg-light">
                            <div class="fw-semibold">Kanan Atas</div>
                            <div class="text-muted">Posisi 4</div>
                        </div>
                        <div class="border rounded p-2 bg-light mt-2">
                            <div class="fw-semibold">Kanan Bawah</div>
                            <div class="text-muted">Posisi 5</div>
                        </div>
                    </div>
                </div>

                <!-- Form pilih artikel untuk tiap posisi -->
                <div class="row g-3">
                    <?php for ($pos = 1; $pos <= 5; $pos++): ?>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <?= htmlspecialchars($posisiLabel[$pos]) ?>
                        </label>
                        <select name="artikel[<?= $pos ?>]"
                                class="form-select select2-artikel"
                                data-pos="<?= $pos ?>">
                            <option value="">— Pilih Artikel —</option>
                            <?php foreach ($artikelRows as $art): ?>
                            <option value="<?= (int)$art['id_artikel'] ?>"
                                <?= (isset($savedPos[$pos]) && $savedPos[$pos] === (int)$art['id_artikel']) ? 'selected' : '' ?>>
                                [<?= htmlspecialchars($art['kategori'], ENT_QUOTES) ?>]
                                <?= htmlspecialchars($art['judul_artikel'], ENT_QUOTES) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> Simpan Konfigurasi
                    </button>
                    <a href="<?= PUBLIC_URL ?>home.php" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-eye me-1"></i> Lihat Halaman Depan
                    </a>
                </div>

            </form>
        </div>
    </div>

</main>
<!--end content-->

<script>
// Inisialisasi Select2 dengan fitur pencarian judul artikel
$(document).ready(function () {
    $('.select2-artikel').select2({
        theme: 'bootstrap4',
        placeholder: '— Pilih Artikel —',
        allowClear: true,
        width: '100%'
    });
});
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
