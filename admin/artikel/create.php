<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
session_start();
requireLogin();

$pageTitle = "Tambah Artikel";

// Ambil daftar kategori yang sudah ada
$sql_kat   = "SELECT DISTINCT kategori FROM artikel ORDER BY kategori";
$res_kat   = $conn->query($sql_kat);

// Ambil daftar tag yang sudah ada
$sql_tag = "SELECT DISTINCT tag FROM artikel_tag ORDER BY tag";
$res_tag = $conn->query($sql_tag);
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
            <form class="row g-3 article-form" action="<?= ADMIN_URL ?>artikel/store.php" method="POST" enctype="multipart/form-data">

                <div class="col-12">
                    <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
                    <input type="text" name="judul_artikel" class="form-control" placeholder="Judul artikel" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select select2-kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($k = $res_kat->fetch_assoc()): ?>
                            <option value="<?= e($k['kategori']) ?>"><?= e($k['kategori']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label d-block">Sumber Gambar <span class="text-danger">*</span></label>
                    <div class="btn-group w-100" role="group" aria-label="Sumber gambar">
                        <input type="radio" class="btn-check" name="gambar_source" id="gambarSourceFileCreate" value="file" checked>
                        <label class="btn btn-outline-primary" for="gambarSourceFileCreate">Upload File</label>

                        <input type="radio" class="btn-check" name="gambar_source" id="gambarSourceLinkCreate" value="link">
                        <label class="btn btn-outline-primary" for="gambarSourceLinkCreate">Gunakan Link</label>
                    </div>
                </div>

                <div class="col-12 article-image-file-wrap">
                    <label class="form-label">Foto Artikel <span class="text-danger">*</span></label>
                    <input type="file" name="foto" class="form-control article-image-file-input" accept="image/*" required>
                    <input type="hidden" name="gambar_cropped_data" class="article-image-cropped-data">
                    <small class="text-muted">Maksimal 5MB. Format: jpg, png, webp, gif. File akan dicrop ke rasio 16:9.</small>
                </div>

                <div class="col-12 article-image-link-wrap d-none">
                    <label class="form-label">Link Gambar</label>
                    <input type="url" name="gambar_link" class="form-control article-image-link-input" placeholder="https://domain.com/gambar.jpg">
                    <small class="text-muted">Gunakan URL gambar langsung. Cocok jika gambar berasal dari website lain/CDN.</small>
                </div>

                <div class="col-12">
                    <div class="article-image-preview-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Preview Gambar</h6>
                                <small class="text-muted">Tampilan disesuaikan dengan kartu artikel pada interface.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary d-none article-image-recrop-btn">Crop Ulang</button>
                        </div>
                        <div class="article-image-preview-frame">
                            <img class="article-image-preview" alt="Preview gambar artikel">
                            <div class="article-image-preview-empty">
                                <i class="bi bi-image fs-1 mb-2"></i>
                                <div>Preview gambar akan muncul di sini.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label">Tag <small class="text-muted">(pisahkan dengan koma)</small></label>
                    <select name="tag[]" class="form-select select2-tags" multiple>
                        <?php while ($t = $res_tag->fetch_assoc()): ?>
                            <option value="<?= e($t['tag']) ?>"><?= e($t['tag']) ?></option>
                        <?php endwhile; ?>
                    </select>
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
window.addEventListener('load', function () {
    if (!window.jQuery || !window.jQuery.fn || !window.jQuery.fn.select2) {
        return;
    }

    window.jQuery('.select2-kategori').select2({
        theme: 'bootstrap4',
        width: '100%',
        tags: true,
        placeholder: '-- Pilih atau ketik kategori --'
    });

    window.jQuery('.select2-tags').select2({
        theme: 'bootstrap4',
        width: '100%',
        tags: true,
        tokenSeparators: [','],
        placeholder: 'Ketik tag lalu tekan Enter atau koma'
    });

    var form = document.querySelector('.article-form');
    if (!form) {
        return;
    }

    var sourceInputs = form.querySelectorAll('input[name="gambar_source"]');
    var fileWrap = form.querySelector('.article-image-file-wrap');
    var linkWrap = form.querySelector('.article-image-link-wrap');
    var fileInput = form.querySelector('.article-image-file-input');
    var linkInput = form.querySelector('.article-image-link-input');
    var croppedInput = form.querySelector('.article-image-cropped-data');
    var previewFrame = form.querySelector('.article-image-preview-frame');
    var previewImage = form.querySelector('.article-image-preview');
    var recropButton = form.querySelector('.article-image-recrop-btn');

    function setPreview(src) {
        if (src) {
            previewImage.src = src;
            previewFrame.classList.add('has-image');
        } else {
            previewImage.removeAttribute('src');
            previewFrame.classList.remove('has-image');
        }
    }

    function syncSourceState() {
        var source = form.querySelector('input[name="gambar_source"]:checked').value;
        var isFile = source === 'file';

        fileWrap.classList.toggle('d-none', !isFile);
        linkWrap.classList.toggle('d-none', isFile);
        fileInput.required = isFile;
        linkInput.required = !isFile;

        if (!isFile) {
            fileInput.value = '';
            croppedInput.value = '';
            recropButton.classList.add('d-none');
            setPreview(linkInput.value.trim());
        } else if (!croppedInput.value) {
            setPreview('');
        }
    }

    function cropSelectedFile() {
        if (!fileInput.files || !fileInput.files[0] || !window.SimpleImageCropper) {
            return;
        }

        window.SimpleImageCropper.open({
            file: fileInput.files[0],
            aspectRatio: 16 / 9,
            outputWidth: 1280,
            onCrop: function (result) {
                croppedInput.value = result.dataUrl;
                setPreview(result.dataUrl);
                recropButton.classList.remove('d-none');
            }
        });
    }

    sourceInputs.forEach(function (input) {
        input.addEventListener('change', syncSourceState);
    });

    fileInput.addEventListener('change', function () {
        if (fileInput.files && fileInput.files[0]) {
            linkInput.value = '';
            cropSelectedFile();
        } else {
            croppedInput.value = '';
            recropButton.classList.add('d-none');
            setPreview('');
        }
    });

    linkInput.addEventListener('input', function () {
        setPreview(linkInput.value.trim());
    });

    recropButton.addEventListener('click', cropSelectedFile);
    syncSourceState();
});
</script>

<?php renderSwalFlash(); ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
