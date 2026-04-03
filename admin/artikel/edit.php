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
$tags_array = array_filter(array_map('trim', explode(',', (string) $tags_string)));

// Ambil kategori
$res_kat = $conn->query("SELECT DISTINCT kategori FROM artikel ORDER BY kategori");

// Ambil daftar tag yang sudah ada
$res_tag = $conn->query("SELECT DISTINCT tag FROM artikel_tag ORDER BY tag");
$pageTitle = "Edit Artikel";
$gambar_awal = $artikel['gambar'] ? mediaUrl($artikel['gambar']) : '';
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
            <form class="row g-3 article-form" action="<?= ADMIN_URL ?>artikel/update.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_artikel" value="<?= $id ?>">

                <div class="col-12">
                    <label class="form-label">Judul Artikel</label>
                    <input type="text" name="judul_artikel" class="form-control" value="<?= e($artikel['judul_artikel']) ?>" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select select2-kategori" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($k = $res_kat->fetch_assoc()): ?>
                            <option value="<?= e($k['kategori']) ?>" <?= $k['kategori'] === $artikel['kategori'] ? 'selected' : '' ?>>
                                <?= e($k['kategori']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label d-block">Sumber Gambar</label>
                    <div class="btn-group w-100" role="group" aria-label="Sumber gambar">
                        <input type="radio" class="btn-check" name="gambar_source" id="gambarSourceFileEdit" value="file" <?= preg_match('#^https?://#i', (string) $artikel['gambar']) ? '' : 'checked' ?>>
                        <label class="btn btn-outline-primary" for="gambarSourceFileEdit">Upload File</label>

                        <input type="radio" class="btn-check" name="gambar_source" id="gambarSourceLinkEdit" value="link" <?= preg_match('#^https?://#i', (string) $artikel['gambar']) ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="gambarSourceLinkEdit">Gunakan Link</label>
                    </div>
                </div>

                <div class="col-12 article-image-file-wrap">
                    <label class="form-label">Ganti Foto <small class="text-muted">(kosongkan jika tidak ingin mengubah)</small></label>
                    <input type="file" name="foto" class="form-control article-image-file-input" accept="image/*">
                    <input type="hidden" name="gambar_cropped_data" class="article-image-cropped-data">
                    <small class="text-muted">Jika memilih file, gambar dapat dicrop agar konsisten dengan tampilan interface.</small>
                </div>

                <div class="col-12 article-image-link-wrap d-none">
                    <label class="form-label">Link Gambar</label>
                    <input type="url" name="gambar_link" class="form-control article-image-link-input" placeholder="https://domain.com/gambar.jpg" value="<?= preg_match('#^https?://#i', (string) $artikel['gambar']) ? e($artikel['gambar']) : '' ?>">
                    <small class="text-muted">Isi link gambar jika ingin menggunakan gambar dari luar website.</small>
                </div>

                <div class="col-12">
                    <div class="article-image-preview-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">Preview Gambar</h6>
                                <small class="text-muted">Preview mengikuti rasio tampilan artikel di interface.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary <?= $gambar_awal ? '' : 'd-none' ?> article-image-recrop-btn">Crop Ulang</button>
                        </div>
                        <div class="article-image-preview-frame <?= $gambar_awal ? 'has-image' : '' ?>">
                            <img class="article-image-preview" src="<?= e($gambar_awal) ?>" alt="Preview gambar artikel">
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
                        <?php
                        $existingTags = [];
                        while ($t = $res_tag->fetch_assoc()) {
                            $existingTags[] = $t['tag'];
                        }
                        $allTags = array_unique(array_merge($existingTags, $tags_array));
                        sort($allTags);
                        foreach ($allTags as $tag):
                        ?>
                            <option value="<?= e($tag) ?>" <?= in_array($tag, $tags_array, true) ? 'selected' : '' ?>>
                                <?= e($tag) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
    var initialPreview = previewImage.getAttribute('src') || '';

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
        linkInput.required = !isFile;

        if (!isFile) {
            fileInput.value = '';
            croppedInput.value = '';
            recropButton.classList.add('d-none');
            setPreview(linkInput.value.trim() || initialPreview);
        } else if (croppedInput.value) {
            setPreview(croppedInput.value);
            recropButton.classList.remove('d-none');
        } else {
            setPreview(initialPreview);
            recropButton.classList.toggle('d-none', !fileInput.files || !fileInput.files[0]);
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
            setPreview(initialPreview);
            recropButton.classList.add('d-none');
        }
    });

    linkInput.addEventListener('input', function () {
        setPreview(linkInput.value.trim() || initialPreview);
    });

    recropButton.addEventListener('click', cropSelectedFile);

    // ── Fallback: klik area file wrap juga buka file picker ─────
    fileWrap.addEventListener('click', function (e) {
        if (e.target === fileInput || e.target.closest('.article-image-recrop-btn')) { return; }
        fileInput.click();
    });

    syncSourceState();
});
</script>

<?php renderSwalFlash(); ?>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
