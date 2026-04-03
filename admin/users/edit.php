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
$defaultAvatar = ADMIN_ASSETS . 'images/avatars/avatar-1.png';
$currentAvatar = mediaUrl($user['foto'] ?? '') ?: $defaultAvatar;
$currentPhotoSource = isExternalUrl($user['foto'] ?? '') ? 'link' : 'file';
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
                    <img src="<?= e($currentAvatar) ?>" class="rounded-circle p-1 border mb-3" width="110" height="110" alt="avatar">
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
                    <form class="row g-3 user-photo-form" action="<?= ADMIN_URL ?>users/update.php" method="POST" enctype="multipart/form-data">
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
                            <label class="form-label d-block mb-2">Sumber Foto Profil</label>
                            <div class="d-flex flex-wrap gap-3">
                                <div class="form-check">
                                    <input class="form-check-input user-photo-source" type="radio" name="foto_source" id="edit-foto-source-link" value="link" <?= $currentPhotoSource === 'link' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="edit-foto-source-link">Link</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input user-photo-source" type="radio" name="foto_source" id="edit-foto-source-file" value="file" <?= $currentPhotoSource === 'file' ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="edit-foto-source-file">File</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 user-photo-link-group <?= $currentPhotoSource === 'file' ? 'd-none' : '' ?>">
                            <label class="form-label">Link Foto Profil</label>
                            <input type="url" name="foto_link" class="form-control user-photo-link-input" value="<?= $currentPhotoSource === 'link' ? e($user['foto']) : '' ?>" placeholder="https://domain.com/avatar.jpg">
                            <div class="form-text">Kosongkan jika ingin menghapus avatar link saat mode link aktif.</div>
                        </div>
                        <div class="col-12 user-photo-file-group <?= $currentPhotoSource === 'link' ? 'd-none' : '' ?>">
                            <label class="form-label">Upload Foto Profil</label>
                            <input type="file" name="foto_file" class="form-control user-photo-file-input" accept="image/*">
                            <input type="hidden" name="foto_cropped_data" class="user-photo-cropped-data">
                            <div class="form-text">Upload file baru bila ingin mengganti foto. Gambar akan di-crop 1:1.</div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded-4 p-3 bg-light user-photo-preview-card">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden border bg-white flex-shrink-0 user-photo-preview-shell" style="width:88px;height:88px;">
                                        <img src="<?= e($currentAvatar) ?>" alt="Preview avatar" class="w-100 h-100 object-fit-cover user-photo-preview-image">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-semibold">Preview Avatar</div>
                                        <div class="text-muted small mb-2">Preview kecil ini mengikuti bentuk tampilan avatar pada header admin.</div>
                                        <button type="button" class="btn btn-sm btn-outline-primary d-none user-photo-recrop-button">Crop Ulang 1:1</button>
                                    </div>
                                </div>
                            </div>
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
window.addEventListener('load', function () {
    if (window.jQuery) {
        jQuery('.select2-basic').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }

    if (typeof SimpleImageCropper === 'undefined') {
        return;
    }

    var defaultAvatar = <?= json_encode($defaultAvatar) ?>;

    document.querySelectorAll('.user-photo-form').forEach(function (form) {
        var sourceInputs = form.querySelectorAll('.user-photo-source');
        var linkGroup = form.querySelector('.user-photo-link-group');
        var fileGroup = form.querySelector('.user-photo-file-group');
        var linkInput = form.querySelector('.user-photo-link-input');
        var fileInput = form.querySelector('.user-photo-file-input');
        var croppedInput = form.querySelector('.user-photo-cropped-data');
        var previewImage = form.querySelector('.user-photo-preview-image');
        var recropButton = form.querySelector('.user-photo-recrop-button');
        var initialPreview = previewImage.getAttribute('src') || defaultAvatar;
        var lastFile = null;

        function currentSource() {
            var checked = form.querySelector('.user-photo-source:checked');
            return checked ? checked.value : 'link';
        }

        function updatePreview(value) {
            previewImage.src = value || defaultAvatar;
        }

        function syncMode() {
            var isFile = currentSource() === 'file';
            linkGroup.classList.toggle('d-none', isFile);
            fileGroup.classList.toggle('d-none', !isFile);

            if (!isFile) {
                if (fileInput) {
                    fileInput.value = '';
                }
                if (croppedInput) {
                    croppedInput.value = '';
                }
                lastFile = null;
                recropButton.classList.add('d-none');
                updatePreview(linkInput.value.trim() || defaultAvatar);
            } else if (!croppedInput.value) {
                updatePreview(initialPreview);
            }
        }

        function openCropper(file) {
            if (!file) {
                return;
            }

            lastFile = file;
            SimpleImageCropper.open({
                file: file,
                aspectRatio: 1,
                aspectRatioLabel: '1:1',
                outputWidth: 600,
                onCrop: function (result) {
                    croppedInput.value = result.dataUrl;
                    updatePreview(result.dataUrl);
                    recropButton.classList.remove('d-none');
                }
            });
        }

        sourceInputs.forEach(function (input) {
            input.addEventListener('change', syncMode);
        });

        if (linkInput) {
            linkInput.addEventListener('input', function () {
                if (currentSource() === 'link') {
                    updatePreview(linkInput.value.trim());
                }
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', function () {
                croppedInput.value = '';
                recropButton.classList.add('d-none');

                if (fileInput.files && fileInput.files[0]) {
                    openCropper(fileInput.files[0]);
                    return;
                }

                updatePreview(initialPreview);
            });
        }

        if (recropButton) {
            recropButton.addEventListener('click', function () {
                if (lastFile) {
                    openCropper(lastFile);
                }
            });
        }

        syncMode();
    });
});
</script>

<?php renderSwalFlash(); ?>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
