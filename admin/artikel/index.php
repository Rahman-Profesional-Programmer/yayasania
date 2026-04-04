<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$pageTitle = "Artikel & Berita";

// ── Filter & pagination params ───────────────────────────────────
$search    = trim($_GET['search']   ?? '');
$fKategori = trim($_GET['kategori'] ?? '');
$fPenulis  = trim($_GET['penulis']  ?? '');
$fStatus   = $_GET['status']    ?? '';        // '' | '1' | '0'
$fDateFrom = trim($_GET['date_from'] ?? '');
$fDateTo   = trim($_GET['date_to']   ?? '');
$_pp     = (int)($_GET['per_page'] ?? 0);
$perPage = in_array($_pp, [10, 50, 100]) ? $_pp : 10;
$curPage   = max(1, (int)($_GET['page'] ?? 1));

$filterActive = ($search !== '' || $fKategori !== '' || $fPenulis !== ''
    || $fStatus !== '' || $fDateFrom !== '' || $fDateTo !== '');

// ── Build WHERE clause (prepared statement) ──────────────────────
$conditions = ['a.hapus = 1'];
$params     = [];
$types      = '';

if ($search !== '') {
    $like         = '%' . $search . '%';
    $conditions[] = '(a.judul_artikel LIKE ? OR a.kategori LIKE ?
        OR EXISTS (SELECT 1 FROM artikel_tag _t WHERE _t.id_artikel = a.id_artikel AND _t.tag LIKE ?))';
    $params[]     = $like;
    $params[]     = $like;
    $params[]     = $like;
    $types       .= 'sss';
}
if ($fKategori !== '') {
    $conditions[] = 'a.kategori = ?';
    $params[]     = $fKategori;
    $types       .= 's';
}
if ($fPenulis !== '') {
    $conditions[] = 'a.penulis LIKE ?';
    $params[]     = '%' . $fPenulis . '%';
    $types       .= 's';
}
if ($fStatus !== '') {
    $conditions[] = 'a.enable = ?';
    $params[]     = (int)$fStatus;
    $types       .= 'i';
}
if ($fDateFrom !== '') {
    $conditions[] = 'DATE(a.tanggal_update) >= ?';
    $params[]     = $fDateFrom;
    $types       .= 's';
}
if ($fDateTo !== '') {
    $conditions[] = 'DATE(a.tanggal_update) <= ?';
    $params[]     = $fDateTo;
    $types       .= 's';
}

$where = implode(' AND ', $conditions);

// ── Count total rows ─────────────────────────────────────────────
$cntStmt = $conn->prepare("SELECT COUNT(*) AS n FROM artikel a WHERE {$where}");
if (!empty($params)) {
    $cntStmt->bind_param($types, ...$params);
}
$cntStmt->execute();
$totalRows  = (int)$cntStmt->get_result()->fetch_assoc()['n'];
$cntStmt->close();

$totalPages = max(1, (int)ceil($totalRows / $perPage));
$curPage    = min($curPage, $totalPages);
$offset     = ($curPage - 1) * $perPage;

// ── Fetch paged data ─────────────────────────────────────────────
$dataStmt  = $conn->prepare(
    "SELECT a.* FROM artikel a WHERE {$where} ORDER BY a.id_artikel DESC LIMIT ? OFFSET ?"
);
$allParams = array_merge($params, [$perPage, $offset]);
$allTypes  = $types . 'ii';
$dataStmt->bind_param($allTypes, ...$allParams);
$dataStmt->execute();
$result = $dataStmt->get_result();

// ── Dropdown options ─────────────────────────────────────────────
$katRows = $conn->query(
    "SELECT DISTINCT kategori FROM artikel WHERE hapus = 1 AND kategori <> '' ORDER BY kategori ASC"
);

// ── URL helper: preserve all current params, override page ───────
function pageUrl(int $p): string {
    $q = array_merge($_GET, ['page' => $p]);
    return '?' . http_build_query($q);
}
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
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>menu/index.php"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active">Artikel</li>
                </ol>
            </nav>
        </div>
        <div class="ms-auto d-flex gap-2">
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importArtikelModal">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
            </button>
            <a href="<?= ADMIN_URL ?>artikel/create.php" class="btn btn-primary">+ Tambah Artikel</a>
        </div>
    </div>

    <!-- ── Filter Card ──────────────────────────────────────────── -->
    <div class="card mb-3">
        <div class="card-body py-2 px-3">
            <form method="get" action="" id="filterForm">
                <div class="row g-2 align-items-end">
                    <div class="col-sm-6 col-md-3">
                        <label class="form-label small mb-1 fw-semibold">Cari Artikel</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                               placeholder="Judul, kategori, atau tag&hellip;"
                               value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small mb-1 fw-semibold">Kategori</label>
                        <select name="kategori" class="form-select form-select-sm">
                            <option value="">Semua Kategori</option>
                            <?php if ($katRows): while ($k = $katRows->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($k['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                <?= $fKategori === $k['kategori'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['kategori'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                            <?php endwhile; endif; ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small mb-1 fw-semibold">Penulis</label>
                        <input type="text" name="penulis" class="form-control form-control-sm"
                               placeholder="Email penulis&hellip;"
                               value="<?= htmlspecialchars($fPenulis, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-sm-6 col-md-1">
                        <label class="form-label small mb-1 fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Semua</option>
                            <option value="1" <?= $fStatus === '1' ? 'selected' : '' ?>>Aktif</option>
                            <option value="0" <?= $fStatus === '0' ? 'selected' : '' ?>>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small mb-1 fw-semibold">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($fDateFrom, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label class="form-label small mb-1 fw-semibold">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control form-control-sm"
                               value="<?= htmlspecialchars($fDateTo, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-sm-12 col-md-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="?" class="btn btn-outline-secondary btn-sm px-3">
                            <i class="bi bi-x-lg me-1"></i>Reset
                        </a>
                    </div>
                </div>
                <!-- Preserve per_page when re-submitting filters -->
                <input type="hidden" name="per_page" id="formPerPage" value="<?= $perPage ?>">
            </form>
        </div>
    </div>

    <!-- ── Data Card ────────────────────────────────────────────── -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="mb-0 text-uppercase">
                    Daftar Artikel
                    <?php if ($filterActive): ?>
                    <span class="badge bg-warning text-dark ms-1" style="font-size:.65rem;">Filter Aktif</span>
                    <?php endif; ?>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small">
                        <?= $totalRows > 0 ? ($offset + 1) . '&ndash;' . min($offset + $perPage, $totalRows) : '0' ?>
                        dari <strong><?= $totalRows ?></strong>
                    </span>
                    <select class="form-select form-select-sm w-auto" onchange="changePerPage(this.value)">
                        <?php foreach ([10, 50, 100] as $n): ?>
                        <option value="<?= $n ?>" <?= $perPage === $n ? 'selected' : '' ?>><?= $n ?>&nbsp;/ hal</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr class="my-2"/>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id_artikel'] ?></td>
                            <td><?= e(mb_substr($row['judul_artikel'], 0, 60)) ?><?= mb_strlen($row['judul_artikel']) > 60 ? '...' : '' ?></td>
                            <td><?= e($row['kategori']) ?></td>
                            <td><?= e($row['penulis']) ?></td>
                            <td><?= $row['tanggal_update'] ?></td>
                            <td>
                                <?php if ($row['enable'] == 1): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3 fs-6">
                                    <?php if ($row['enable'] == 1): ?>
                                        <a onclick="toggleArtikel(<?= $row['id_artikel'] ?>, 'disable')" class="text-primary" title="Nonaktifkan" style="cursor:pointer">
                                            <i class="bi bi-eye-fill"></i></a>
                                    <?php else: ?>
                                        <a onclick="toggleArtikel(<?= $row['id_artikel'] ?>, 'enable')" class="text-secondary" title="Aktifkan" style="cursor:pointer">
                                            <i class="bi bi-eye-slash-fill"></i></a>
                                    <?php endif; ?>
                                    <a href="<?= ADMIN_URL ?>artikel/edit.php?id=<?= $row['id_artikel'] ?>" class="text-warning" title="Edit">
                                        <i class="bi bi-pencil-fill"></i></a>
                                    <a onclick="hapusArtikel(<?= $row['id_artikel'] ?>)" class="text-danger" title="Hapus" style="cursor:pointer">
                                        <i class="bi bi-trash-fill"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <?php if ($filterActive): ?>
                                <i class="bi bi-search me-1"></i>Tidak ada artikel yang sesuai dengan filter.
                                <?php else: ?>
                                Belum ada artikel.
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPages > 1): ?>
            <!-- ── Pagination ──────────────────────────────────── -->
            <nav class="mt-3" aria-label="Navigasi halaman artikel">
                <ul class="pagination pagination-sm justify-content-center mb-0">
                    <li class="page-item <?= $curPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars(pageUrl($curPage - 1)) ?>">&lsaquo;</a>
                    </li>
                    <?php
                    $pRange = 2;
                    $pStart = max(1, $curPage - $pRange);
                    $pEnd   = min($totalPages, $curPage + $pRange);
                    if ($pStart > 1): ?>
                        <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(pageUrl(1)) ?>">1</a></li>
                        <?php if ($pStart > 2): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; ?>
                    <?php endif;
                    for ($p = $pStart; $p <= $pEnd; $p++): ?>
                    <li class="page-item <?= $p === $curPage ? 'active' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars(pageUrl($p)) ?>"><?= $p ?></a>
                    </li>
                    <?php endfor;
                    if ($pEnd < $totalPages):
                        if ($pEnd < $totalPages - 1): ?><li class="page-item disabled"><span class="page-link">&hellip;</span></li><?php endif; ?>
                        <li class="page-item"><a class="page-link" href="<?= htmlspecialchars(pageUrl($totalPages)) ?>"><?= $totalPages ?></a></li>
                    <?php endif; ?>
                    <li class="page-item <?= $curPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars(pageUrl($curPage + 1)) ?>">&rsaquo;</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </div>
</main>

<script>
function toggleArtikel(id, aksi) {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin ingin mengubah status artikel ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            let url = aksi === 'enable' ? '<?= ADMIN_URL ?>artikel/enable.php' : '<?= ADMIN_URL ?>artikel/disable.php';
            fetch(url, { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'id=' + id })
                .then(() => location.reload());
        }
    });
}
function hapusArtikel(id) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Yakin ingin menghapus artikel ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(function(result) {
        if (result.isConfirmed) {
            fetch('<?= ADMIN_URL ?>artikel/delete.php', { method: 'POST', headers: {'Content-Type': 'application/x-www-form-urlencoded'}, body: 'id=' + id })
                .then(() => location.reload());
        }
    });
}

function changePerPage(n) {
    var hiddenField = document.getElementById('formPerPage');
    if (hiddenField) { hiddenField.value = n; }
    var url = new URL(window.location.href);
    url.searchParams.set('per_page', n);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

// ── Import modal JS ────────────────────────────────────────────
window.addEventListener('load', function () {
    'use strict';

    var fileInput   = document.getElementById('importFileInput');
    var dropZone    = document.getElementById('importDropZone');
    var fileInfo    = document.getElementById('importFileInfo');
    var fileName    = document.getElementById('importFileName');
    var fileSize    = document.getElementById('importFileSize');
    var fileClear   = document.getElementById('importFileClear');
    var startBtn    = document.getElementById('importStartBtn');
    var againBtn    = document.getElementById('importAgainBtn');
    var step1       = document.getElementById('importStep1');
    var step2       = document.getElementById('importStep2');
    var step3       = document.getElementById('importStep3');
    var progressTxt = document.getElementById('importProgressText');
    var importModal = document.getElementById('importArtikelModal');

    if (!fileInput || !dropZone || !fileInfo || !fileName || !fileSize || !fileClear || !startBtn || !againBtn || !step1 || !step2 || !step3 || !progressTxt || !importModal) {
        return;
    }

    // ── Reset modal kembali ke Step 1 ──────────────────────────
    function resetModal() {
        fileInput.value  = '';
        fileInfo.classList.add('d-none');
        dropZone.classList.remove('d-none');
        step1.classList.remove('d-none');
        step2.classList.add('d-none');
        step3.classList.add('d-none');
        startBtn.classList.remove('d-none');
        againBtn.classList.add('d-none');
    }

    // Reset ketika modal ditutup
    importModal.addEventListener('hidden.bs.modal', resetModal);

    // ── Tampilkan info file yang dipilih ───────────────────────
    function showFile(file) {
        if (!file) { return; }
        fileName.textContent = file.name;
        fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        fileInfo.classList.remove('d-none');
        dropZone.classList.add('d-none');
    }

    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) {
            showFile(fileInput.files[0]);
        }
    });

    // ── Tombol hapus pilihan file ──────────────────────────────
    fileClear.addEventListener('click', function () {
        fileInput.value = '';
        fileInfo.classList.add('d-none');
        dropZone.classList.remove('d-none');
    });

    // ── Drag & Drop ────────────────────────────────────────────
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });
    dropZone.addEventListener('dragleave', function () {
        dropZone.classList.remove('drag-over');
    });
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        var files = e.dataTransfer.files;
        if (files && files[0]) {
            // Inject ke input
            var dt = new DataTransfer();
            dt.items.add(files[0]);
            fileInput.files = dt.files;
            showFile(files[0]);
        }
    });

    // ── Klik area drop zone membuka file picker ────────────────
    dropZone.addEventListener('click', function (e) {
        // Jika klik langsung ke input file, biarkan browser handle secara native
        if (e.target === fileInput) { return; }
        // Untuk elemen lain (label, icon, teks), trigger programmatically
        e.preventDefault();
        fileInput.click();
    });

    // ── Mulai Import ───────────────────────────────────────────
    startBtn.addEventListener('click', function () {
        var file = fileInput.files && fileInput.files[0];
        if (!file) {
            Swal.fire({
                icon: 'warning',
                title: 'Pilih File',
                text: 'Silakan pilih atau drag & drop file Excel terlebih dahulu.',
                confirmButtonColor: '#0d6efd'
            });
            return;
        }

        // Tampilkan step loading
        step1.classList.add('d-none');
        step2.classList.remove('d-none');
        startBtn.classList.add('d-none');
        progressTxt.textContent = 'Membaca file dan mengimpor artikel ke database...';

        var formData = new FormData();
        formData.append('import_file', file);

        fetch('<?= ADMIN_URL ?>artikel/import-proses.php', {
            method: 'POST',
            body: formData
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('Respons server: HTTP ' + response.status);
            }

            return response.text().then(function (text) {
                try {
                    return JSON.parse(text);
                } catch (parseError) {
                    var normalizedText = String(text || '').trim();
                    var shortText = normalizedText.replace(/\s+/g, ' ').slice(0, 250);

                    if (response.redirected || /<\/?html/i.test(normalizedText)) {
                        throw new Error('Server mengembalikan halaman HTML. Kemungkinan sesi login berakhir atau terjadi error PHP.');
                    }

                    throw new Error(shortText || 'Respons server bukan JSON yang valid.');
                }
            });
        })
        .then(function (data) {
            step2.classList.add('d-none');
            step3.classList.remove('d-none');

            if (!data.success) {
                // Error global (mis. file tidak bisa dibaca)
                step3.innerHTML =
                    '<div class="alert alert-danger">'
                    + '<i class="bi bi-x-circle-fill me-2"></i>'
                    + (data.message || 'Import gagal.')
                    + '</div>';
                startBtn.classList.remove('d-none');
                return;
            }

            // ── Isi stat cards ──────────────────────────────────
            document.getElementById('resTotal').textContent    = data.total    || 0;
            document.getElementById('resImported').textContent = data.imported || 0;
            document.getElementById('resFailed').textContent   = data.failed   || 0;
            document.getElementById('resSkipped').textContent  = data.skipped  || 0;

            // ── Tabel hasil per-baris ───────────────────────────
            var tbody = document.getElementById('resTableBody');
            tbody.innerHTML = '';

            (data.results || []).forEach(function (item) {
                var isOk    = item.status === 'success';
                var badge   = isOk
                    ? '<span class="badge bg-success">Berhasil</span>'
                    : '<span class="badge bg-danger">Gagal</span>';
                var tagCell = isOk
                    ? '<span class="badge bg-primary">' + (item.tags || 0) + '</span>'
                    : '-';
                var reason  = isOk ? '-' : (item.reason || '');

                tbody.insertAdjacentHTML('beforeend',
                    '<tr>'
                    + '<td class="text-center text-muted small">' + (item.row || '-') + '</td>'
                    + '<td class="text-truncate" style="max-width:220px;" title="' + escHtml(item.judul || '') + '">' + escHtml(item.judul || '-') + '</td>'
                    + '<td class="text-center">' + tagCell + '</td>'
                    + '<td class="text-center">' + badge + '</td>'
                    + '<td class="text-muted small">' + escHtml(reason) + '</td>'
                    + '</tr>'
                );
            });

            // ── Debug log ───────────────────────────────────────
            var logLines = (data.logs || []).map(function (entry) {
                var icon = { info: 'ℹ️', notice: '📋', warning: '⚠️', error: '❌' }[entry.type] || '·';
                return '[' + (entry.time || '') + '] ' + icon + ' ' + entry.message;
            });

            document.getElementById('debugLogContent').textContent = logLines.join('\n');
            document.getElementById('debugLogCount').textContent   = logLines.length;

            // ── Tombol lanjutan ─────────────────────────────────
            if ((data.imported || 0) > 0) {
                againBtn.classList.remove('d-none');
                // Reload tabel artikel di background setelah tutup modal
                importModal
                    .addEventListener('hidden.bs.modal', function () {
                        location.reload();
                    }, { once: true });
            } else {
                startBtn.classList.remove('d-none');
            }
        })
        .catch(function (err) {
            step2.classList.add('d-none');
            step3.classList.remove('d-none');
            step3.innerHTML =
                '<div class="alert alert-danger">'
                + '<i class="bi bi-x-circle-fill me-2"></i>'
                + 'Terjadi kesalahan koneksi: ' + err.message
                + '</div>';
            startBtn.classList.remove('d-none');
        });
    });

    againBtn.addEventListener('click', resetModal);

    /** Encode HTML entities untuk output aman di innerHTML */
    function escHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

});
</script>

<?php renderSwalFlash(); ?>

<!-- ═══════════════════════════════════════════════════════════════
     MODAL: Import Artikel via Excel
     Berisi: panduan kolom, download template, upload file,
             panel loading, dan panel hasil / debug log.
════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="importArtikelModal" tabindex="-1" aria-labelledby="importArtikelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">

            <!-- ─── Header ───────────────────────────────────────── -->
            <div class="modal-header bg-light border-bottom">
                <h5 class="modal-title" id="importArtikelModalLabel">
                    <i class="bi bi-file-earmark-spreadsheet me-2 text-success"></i>Import Artikel via Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <!-- ─── Body ─────────────────────────────────────────── -->
            <div class="modal-body px-4 py-3">

                <!-- ── STEP 1 : Form upload ────────────────────── -->
                <div id="importStep1">

                    <!-- Petunjuk kolom Excel -->
                    <div class="alert border-0 bg-light-info mb-4">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-info fs-5 mt-1 flex-shrink-0"></i>
                            <div>
                                <div class="fw-semibold mb-1">Struktur kolom template Excel</div>
                                <table class="table table-sm table-bordered mb-0" style="font-size:13px;">
                                    <thead class="table-primary">
                                        <tr>
                                            <th style="width:28px">Kol</th>
                                            <th>Nama Kolom</th>
                                            <th>Keterangan</th>
                                            <th style="width:70px">Wajib?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr><td class="text-center fw-bold">A</td><td>judul_artikel</td><td>Judul artikel (teks)</td><td class="text-center"><span class="badge bg-danger">Wajib</span></td></tr>
                                        <tr><td class="text-center fw-bold">B</td><td>kategori</td><td>Kategori artikel (teks)</td><td class="text-center"><span class="badge bg-secondary">Opsional</span></td></tr>
                                        <tr><td class="text-center fw-bold">C</td><td>gambar</td><td>URL/link foto (https://...) — bukan file upload</td><td class="text-center"><span class="badge bg-secondary">Opsional</span></td></tr>
                                        <tr><td class="text-center fw-bold">D</td><td>konten_artikel</td><td>Isi artikel (teks)</td><td class="text-center"><span class="badge bg-danger">Wajib</span></td></tr>
                                        <tr><td class="text-center fw-bold">E</td><td>tags</td><td>Tag dipisah koma. Contoh: <code>berita, pendidikan</code></td><td class="text-center"><span class="badge bg-secondary">Opsional</span></td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Download template -->
                    <div class="d-flex align-items-center justify-content-between bg-light rounded-3 px-4 py-3 mb-4 border">
                        <div>
                            <div class="fw-semibold"><i class="bi bi-file-earmark-excel text-success me-1"></i>Download Template Excel</div>
                            <div class="text-muted small">Template berisi petunjuk lengkap dan 2 baris contoh data.</div>
                        </div>
                        <a href="<?= ADMIN_URL ?>artikel/template-download.php"
                           class="btn btn-success btn-sm flex-shrink-0" target="_blank">
                            <i class="bi bi-download me-1"></i> Download
                        </a>
                    </div>

                    <!-- Area upload file -->
                    <div class="mb-2">
                        <label class="form-label fw-semibold" for="importFileInput">
                            Upload File Excel <span class="text-danger">*</span>
                        </label>
                        <div class="import-drop-zone border rounded-3 p-4 text-center position-relative" id="importDropZone">
                            <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
                            <div class="mt-2 text-muted">Drag &amp; drop file di sini, atau</div>
                            <label class="btn btn-outline-primary btn-sm mt-2" for="importFileInput">Pilih File</label>
                            <input type="file" id="importFileInput" class="d-none" accept=".xlsx,.xls,.csv">
                            <div class="mt-2 text-muted small">Mendukung: .xlsx, .xls, .csv &mdash; Maks 10 MB</div>
                        </div>
                        <!-- Nama file yang dipilih -->
                        <div id="importFileInfo" class="d-none mt-2">
                            <div class="d-flex align-items-center gap-2 bg-light-success rounded-3 px-3 py-2 border border-success-subtle">
                                <i class="bi bi-file-earmark-check-fill text-success fs-5"></i>
                                <div class="min-w-0">
                                    <div class="fw-semibold text-truncate" id="importFileName"></div>
                                    <div class="text-muted small" id="importFileSize"></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-auto flex-shrink-0" id="importFileClear">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ── STEP 2 : Loading / progress ─────────────── -->
                <div id="importStep2" class="d-none text-center py-4">
                    <div class="spinner-border text-primary mb-3" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="fw-semibold fs-5 mb-1">Sedang memproses...</div>
                    <div class="text-muted small" id="importProgressText">Membaca file dan mengimpor artikel ke database.</div>
                    <div class="progress mt-3" style="height:8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                             id="importProgressBar" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>

                <!-- ── STEP 3 : Hasil import ───────────────────── -->
                <div id="importStep3" class="d-none">

                    <!-- Stat cards ringkasan -->
                    <div class="row g-2 mb-3">
                        <div class="col-6 col-md-3">
                            <div class="card border-0 bg-light text-center py-3">
                                <div class="fs-3 fw-bold" id="resTotal">0</div>
                                <div class="text-muted small">Total Baris</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 bg-success bg-opacity-10 text-center py-3">
                                <div class="fs-3 fw-bold text-success" id="resImported">0</div>
                                <div class="text-muted small">Berhasil</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 bg-danger bg-opacity-10 text-center py-3">
                                <div class="fs-3 fw-bold text-danger" id="resFailed">0</div>
                                <div class="text-muted small">Gagal</div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card border-0 bg-warning bg-opacity-10 text-center py-3">
                                <div class="fs-3 fw-bold text-warning" id="resSkipped">0</div>
                                <div class="text-muted small">Dilewati</div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel hasil per-baris -->
                    <div class="table-responsive mb-3" id="resTableWrap" style="max-height:200px;overflow-y:auto;">
                        <table class="table table-sm table-bordered align-middle">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width:60px">Baris</th>
                                    <th>Judul Artikel</th>
                                    <th style="width:80px">Tag</th>
                                    <th style="width:100px">Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody id="resTableBody"></tbody>
                        </table>
                    </div>

                    <!-- Panel debug log (bisa dilipat) -->
                    <div class="accordion" id="debugAccordion">
                        <div class="accordion-item border">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-semibold py-2" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#debugLogPanel"
                                        aria-expanded="false">
                                    <i class="bi bi-terminal me-2"></i>Import Log (Debug)
                                    <span class="badge bg-secondary ms-2" id="debugLogCount">0</span>
                                </button>
                            </h2>
                            <div id="debugLogPanel" class="accordion-collapse collapse">
                                <div class="accordion-body p-0">
                                    <div class="bg-dark text-light font-monospace small p-3"
                                         id="debugLogContent"
                                         style="max-height:280px;overflow-y:auto;white-space:pre-wrap;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div><!-- /modal-body -->

            <!-- ─── Footer ───────────────────────────────────────── -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>

                <!-- Tombol mulai import (tampil di Step 1) -->
                <button type="button" class="btn btn-success" id="importStartBtn">
                    <i class="bi bi-cloud-upload me-1"></i> Mulai Import
                </button>

                <!-- Tombol import lagi (tampil di Step 3 jika ada yg berhasil) -->
                <button type="button" class="btn btn-outline-primary d-none" id="importAgainBtn">
                    <i class="bi bi-arrow-repeat me-1"></i> Import Lagi
                </button>
            </div>

        </div><!-- /modal-content -->
    </div><!-- /modal-dialog -->
</div><!-- /#importArtikelModal -->

<style>
/* ── Drop-zone styling ── */
.import-drop-zone {
    border: 2px dashed #adb5bd !important;
    background: #f8f9fa;
    transition: border-color .2s, background .2s;
    cursor: pointer;
}
.import-drop-zone.drag-over {
    border-color: #0d6efd !important;
    background: #e7effe;
}
</style>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
