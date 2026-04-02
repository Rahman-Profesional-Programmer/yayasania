<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    redirect(ADMIN_URL . 'users/index.php');
}

$stmt = $conn->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    setSwalFlash('error', 'Data tidak ditemukan', 'Pengguna yang dipilih tidak tersedia.');
    redirect(ADMIN_URL . 'users/index.php');
}

$pageTitle = 'Edit Pengguna';
$isCurrentUser = currentUserId() === (int) $user['id'];
?>
<?php require_once __DIR__ . '/../layout/header.php'; ?>
<?php require_once __DIR__ . '/../layout/sidebar.php'; ?>

<main class="page-content">
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Sistem</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="<?= ADMIN_URL ?>users/index.php">Manajemen Pengguna</a></li>
                    <li class="breadcrumb-item active">Edit Pengguna</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-4">
            <div class="card border shadow-none h-100">
                <div class="card-body text-center p-4">
                    <?php
                    $avatar = ADMIN_ASSETS . 'images/avatars/avatar-1.png';
                    if (!empty($user['foto'])) {
                        $avatar = preg_match('#^https?://#', $user['foto'])
                            ? $user['foto']
                            : BASE_URL . ltrim($user['foto'], '/');
                    }
                    ?>
                    <img src="<?= e($avatar) ?>" class="rounded-circle p-1 border mb-3" width="110" height="110" alt="avatar">
                    <h5 class="mb-1"><?= e($user['name_show'] ?: '-') ?></h5>
                    <p class="text-muted mb-2"><?= e($user['email']) ?></p>
                    <?php if (($user['role'] ?? 'user') === 'admin'): ?>
                        <span class="badge bg-danger">Admin</span>
                    <?php else: ?>
                        <span class="badge bg-info text-dark">User</span>
                    <?php endif; ?>
                    <?php if ((int) $user['enable'] === 1): ?>
                        <span class="badge bg-success ms-1">Aktif</span>
                    <?php else: ?>
                        <span class="badge bg-secondary ms-1">Nonaktif</span>
                    <?php endif; ?>

                    <?php if ($isCurrentUser): ?>
                        <div class="alert border-0 bg-light-info mt-4 mb-0 text-start">
                            Anda sedang mengedit akun sendiri. Demi keamanan, role dan status akun sendiri tidak dapat diubah dari halaman ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card border shadow-none">
                <div class="card-header py-3">
                    <h6 class="mb-0">Form Edit Pengguna</h6>
                </div>
                <div class="card-body">
                    <form class="row g-3" action="<?= ADMIN_URL ?>users/update.php" method="POST">
                        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">

                        <div class="col-md-6">
                            <label class="form-label">Nama Tampil</label>
                            <input type="text" name="name_show" class="form-control" value="<?= e($user['name_show']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= e($user['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select select2-basic" <?= $isCurrentUser ? 'disabled' : '' ?>>
                                <option value="user" <?= ($user['role'] ?? 'user') === 'user' ? 'selected' : '' ?>>User</option>
                                <option value="admin" <?= ($user['role'] ?? 'user') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                            <?php if ($isCurrentUser): ?>
                                <input type="hidden" name="role" value="<?= e($user['role'] ?? 'user') ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select name="enable" class="form-select select2-basic" <?= $isCurrentUser ? 'disabled' : '' ?>>
                                <option value="1" <?= (int) $user['enable'] === 1 ? 'selected' : '' ?>>Aktif</option>
                                <option value="0" <?= (int) $user['enable'] === 0 ? 'selected' : '' ?>>Nonaktif</option>
                            </select>
                            <?php if ($isCurrentUser): ?>
                                <input type="hidden" name="enable" value="<?= (int) $user['enable'] ?>">
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Foto Profil</label>
                            <input type="text" name="foto" class="form-control" value="<?= e($user['foto']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Facebook</label>
                            <input type="text" name="facebook" class="form-control" value="<?= e($user['facebook']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Instagram</label>
                            <input type="text" name="instagram" class="form-control" value="<?= e($user['instagram']) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="diskripsi" class="form-control" rows="5"><?= e($user['diskripsi']) ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="<?= ADMIN_URL ?>users/index.php" class="btn btn-secondary ms-2">Kembali</a>
                        </div>
                    </form>
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
</script>

<?php renderSwalFlash(); ?>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
