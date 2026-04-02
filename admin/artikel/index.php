<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$pageTitle = "Artikel & Berita";

// Ambil semua artikel
$sql    = "SELECT * FROM artikel WHERE hapus = 1 ORDER BY id_artikel DESC";
$result = $conn->query($sql);
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
        <div class="ms-auto">
            <a href="<?= ADMIN_URL ?>artikel/create.php" class="btn btn-primary">+ Tambah Artikel</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h6 class="mb-3 text-uppercase">Daftar Artikel</h6>
            <hr/>
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
                        <tr><td colspan="7" class="text-center">Belum ada artikel</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
</script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
