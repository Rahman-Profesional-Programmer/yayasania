<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireAdmin();

$pageTitle = 'Manajemen Pengguna';

$statsResult = $conn->query("SELECT
    COUNT(*) AS total_users,
    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) AS total_admins,
    SUM(CASE WHEN role = 'user' THEN 1 ELSE 0 END) AS total_regular_users,
    SUM(CASE WHEN enable = 1 THEN 1 ELSE 0 END) AS active_users
FROM users");
$stats = $statsResult ? ($statsResult->fetch_assoc() ?: []) : [];

$users = $conn->query("SELECT * FROM users ORDER BY FIELD(role, 'admin', 'user'), enable DESC, id DESC");
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Sistem</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>menu/index.php"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active">Manajemen Pengguna</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card radius-10 border-0 bg-primary bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0">Total Pengguna</p>
                            <h4 class="my-1"><?= (int) ($stats['total_users'] ?? 0) ?></h4>
                        </div>
                        <div class="text-white ms-auto font-35"><i class="bi bi-people-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 bg-danger bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0">Total Admin</p>
                            <h4 class="my-1"><?= (int) ($stats['total_admins'] ?? 0) ?></h4>
                        </div>
                        <div class="text-white ms-auto font-35"><i class="bi bi-shield-lock-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 bg-info bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0">Total User</p>
                            <h4 class="my-1"><?= (int) ($stats['total_regular_users'] ?? 0) ?></h4>
                        </div>
                        <div class="text-white ms-auto font-35"><i class="bi bi-person-badge-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 bg-success bg-gradient text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <p class="mb-0">Akun Aktif</p>
                            <h4 class="my-1"><?= (int) ($stats['active_users'] ?? 0) ?></h4>
                        </div>
                        <div class="text-white ms-auto font-35"><i class="bi bi-person-check-fill"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert border-0 bg-light-warning alert-dismissible fade show mb-4">
        <div class="text-dark">
            Hanya admin yang dapat mengubah role pengguna menjadi admin. Password baru akan langsung disimpan dalam bentuk terenkripsi.
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="row g-4">
        <div class="col-12 col-xl-4 d-flex">
            <div class="card border shadow-none w-100">
                <div class="card-header py-3">
                    <h6 class="mb-0">Tambah Pengguna</h6>
                </div>
                <div class="card-body">
                    <form class="row g-3" action="<?= ADMIN_URL ?>users/store.php" method="POST">
                        <div class="col-12">
                            <label class="form-label">Nama Tampil</label>
                            <input type="text" name="name_show" class="form-control" placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@domain.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select select2-basic" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="enable" class="form-select select2-basic" required>
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Foto Profil</label>
                            <input type="text" name="foto" class="form-control" placeholder="storage/uploads/foto/nama-file.jpg atau URL">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Facebook</label>
                            <input type="text" name="facebook" class="form-control" placeholder="https://facebook.com/...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Instagram</label>
                            <input type="text" name="instagram" class="form-control" placeholder="https://instagram.com/...">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="diskripsi" class="form-control" rows="4" placeholder="Bio singkat pengguna"></textarea>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus-fill me-1"></i> Simpan Pengguna
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-8 d-flex">
            <div class="card border shadow-none w-100">
                <div class="card-header py-3">
                    <h6 class="mb-0">Daftar Pengguna</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Pengguna</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Sosial</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if ($users && $users->num_rows > 0): ?>
                                <?php while ($row = $users->fetch_assoc()): ?>
                                    <?php
                                    $avatar = ADMIN_ASSETS . 'images/avatars/avatar-1.png';
                                    if (!empty($row['foto'])) {
                                        $avatar = preg_match('#^https?://#', $row['foto'])
                                            ? $row['foto']
                                            : BASE_URL . ltrim($row['foto'], '/');
                                    }
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <img src="<?= e($avatar) ?>" class="rounded-circle" width="46" height="46" alt="avatar">
                                                <div>
                                                    <div class="fw-semibold"><?= e($row['name_show'] ?: '-') ?>
                                                        <?php if ((int) $row['id'] === currentUserId()): ?>
                                                            <span class="badge bg-dark ms-1">Anda</span>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="text-muted small"><?= e($row['email']) ?></div>
                                                    <?php if (!empty($row['diskripsi'])): ?>
                                                        <div class="text-muted small"><?= e(excerpt($row['diskripsi'], 60)) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (($row['role'] ?? 'user') === 'admin'): ?>
                                                <span class="badge bg-danger">Admin</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">User</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ((int) $row['enable'] === 1): ?>
                                                <span class="badge bg-success">Aktif</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 fs-5">
                                                <?php if (!empty($row['facebook'])): ?>
                                                    <a href="<?= e($row['facebook']) ?>" target="_blank" class="text-primary"><i class="bi bi-facebook"></i></a>
                                                <?php endif; ?>
                                                <?php if (!empty($row['instagram'])): ?>
                                                    <a href="<?= e($row['instagram']) ?>" target="_blank" class="text-danger"><i class="bi bi-instagram"></i></a>
                                                <?php endif; ?>
                                                <?php if (empty($row['facebook']) && empty($row['instagram'])): ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex justify-content-end gap-3 fs-6">
                                                <?php if ((int) $row['enable'] === 1): ?>
                                                    <a onclick="toggleUser(<?= (int) $row['id'] ?>, 'disable')" class="text-secondary" style="cursor:pointer" title="Nonaktifkan">
                                                        <i class="bi bi-eye-slash-fill"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a onclick="toggleUser(<?= (int) $row['id'] ?>, 'enable')" class="text-success" style="cursor:pointer" title="Aktifkan">
                                                        <i class="bi bi-eye-fill"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= ADMIN_URL ?>users/edit.php?id=<?= (int) $row['id'] ?>" class="text-warning" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </a>
                                                <a onclick="deleteUser(<?= (int) $row['id'] ?>)" class="text-danger" style="cursor:pointer" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data pengguna.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
(function ($) {
    $('.select2-basic').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
})(jQuery);

function runUserAction(url, id, successText) {
    fetch(url, {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id=' + encodeURIComponent(id)
    })
        .then((response) => response.text())
        .then((text) => {
            const message = text.trim();

            if (message === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: successText,
                    confirmButtonColor: '#0d6efd'
                }).then(function () {
                    location.reload();
                });
                return;
            }

            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: message || 'Aksi tidak dapat diproses.',
                confirmButtonColor: '#0d6efd'
            });
        });
}

function toggleUser(id, aksi) {
    const config = aksi === 'enable'
        ? {
            title: 'Aktifkan akun?',
            text: 'Pengguna akan dapat login kembali ke panel admin.',
            success: 'Pengguna berhasil diaktifkan.',
            url: '<?= ADMIN_URL ?>users/enable.php'
        }
        : {
            title: 'Nonaktifkan akun?',
            text: 'Pengguna tidak akan dapat login selama akun dinonaktifkan.',
            success: 'Pengguna berhasil dinonaktifkan.',
            url: '<?= ADMIN_URL ?>users/disable.php'
        };

    Swal.fire({
        title: config.title,
        text: config.text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (result.isConfirmed) {
            runUserAction(config.url, id, config.success);
        }
    });
}

function deleteUser(id) {
    Swal.fire({
        title: 'Hapus pengguna?',
        text: 'Data pengguna akan dihapus permanen jika tidak terhubung ke artikel.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(function (result) {
        if (result.isConfirmed) {
            runUserAction('<?= ADMIN_URL ?>users/delete.php', id, 'Pengguna berhasil dihapus.');
        }
    });
}
</script>

<?php renderSwalFlash(); ?>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
