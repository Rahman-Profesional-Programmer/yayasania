<?php
// ── Ambil konfigurasi top_news dari DB ───────────────────────────
// Setiap posisi di-join ke tabel artikel agar data langsung tersedia.
// Posisi: 1=Tengah Besar, 2=Kiri Atas, 3=Kiri Bawah, 4=Kanan Atas, 5=Kanan Bawah

// Pastikan koneksi DB dan konstanta URL tersedia
// require_once aman dipanggil banyak kali — hanya dijalankan sekali per path
require_once __DIR__ . '/../../../../config/database.php';

$topStmt = $conn->prepare(
    "SELECT tn.posisi,
            a.id_artikel,
            a.judul_artikel,
            a.kategori,
            a.penulis,
            a.tanggal_update,
            a.gambar
     FROM top_news tn
     JOIN artikel a ON tn.id_artikel = a.id_artikel
     WHERE a.hapus = 1
     ORDER BY tn.posisi ASC"
);
$topStmt->execute();
$topResult = $topStmt->get_result();

// Susun ke array $pos[posisi] = data artikel
$pos = [];
while ($r = $topResult->fetch_assoc()) {
    $pos[(int)$r['posisi']] = $r;
}
$topStmt->close();

// Helper: kembalikan URL gambar artikel, fallback ke banner statis
// Format gambar ada dua kemungkinan:
//   1. URL penuh (https://...) → dipakai langsung
//   2. Path lokal (storage/uploads/foto/xxx) → ditambah BASE_URL di depan
function topNewsImg(array|null $art, int $fallback): string {
    if (!empty($art['gambar'])) {
        $g = $art['gambar'];
        if (str_starts_with($g, 'http://') || str_starts_with($g, 'https://')) {
            return $g; // URL eksternal, pakai langsung
        }
        return BASE_URL . htmlspecialchars(ltrim($g, '/'), ENT_QUOTES, 'UTF-8');
    }
    return 'img/world-news-img/banner-0' . $fallback . '.jpg';
}

// Helper: kembalikan URL artikel, fallback ke '#'
function topNewsUrl(array|null $art): string {
    if (!empty($art['id_artikel'])) {
        return PUBLIC_URL . 'artikel-show.php?id_artikel=' . (int)$art['id_artikel'];
    }
    return '#';
}
?>
<div class="main--content">
                    <!-- Post Items Start -->
                    <div class="post--items post--items-1 pd--30-0">
                        <div class="row gutter--15">
                            <div class="col-md-3">
                                <div class="row gutter--15">
                                    <div class="col-md-12 col-xs-6 col-xxs-12">
                                        <!-- Posisi 2: Kiri Atas -->
                                        <div class="post--item post--layout-1 post--title-large">
                                            <div class="post--img">
                                                <a href="<?= topNewsUrl($pos[2] ?? null) ?>" class="thumb" style="display:block;overflow:hidden;height:195px;">
                                                    <img src="<?= topNewsImg($pos[2] ?? null, 1) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                                </a>
                                                <a href="<?= topNewsUrl($pos[2] ?? null) ?>" class="cat">
                                                    <?= htmlspecialchars($pos[2]['kategori'] ?? 'Kategori', ENT_QUOTES, 'UTF-8') ?>
                                                </a>

                                                <div class="post--info">
                                                    <ul class="nav meta">
                                                        <li><a href="#"><?= htmlspecialchars($pos[2]['penulis'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></li>
                                                        <li><a href="#"><?= !empty($pos[2]['tanggal_update']) ? date('d F Y', strtotime($pos[2]['tanggal_update'])) : '' ?></a></li>
                                                    </ul>

                                                    <div class="title">
                                                        <h2 class="h4">
                                                            <a href="<?= topNewsUrl($pos[2] ?? null) ?>" class="btn-link">
                                                                <?= htmlspecialchars($pos[2]['judul_artikel'] ?? 'Belum diatur', ENT_QUOTES, 'UTF-8') ?>
                                                            </a>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Post Item End -->
                                    </div>

                                    <div class="col-md-12 col-xs-6 hidden-xxs">
                                        <!-- Posisi 3: Kiri Bawah -->
                                        <div class="post--item post--layout-1 post--title-large">
                                            <div class="post--img">
                                                <a href="<?= topNewsUrl($pos[3] ?? null) ?>" class="thumb" style="display:block;overflow:hidden;height:195px;">
                                                    <img src="<?= topNewsImg($pos[3] ?? null, 2) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                                </a>
                                                <a href="<?= topNewsUrl($pos[3] ?? null) ?>" class="cat">
                                                    <?= htmlspecialchars($pos[3]['kategori'] ?? 'Kategori', ENT_QUOTES, 'UTF-8') ?>
                                                </a>

                                                <div class="post--info">
                                                    <ul class="nav meta">
                                                        <li><a href="#"><?= htmlspecialchars($pos[3]['penulis'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></li>
                                                        <li><a href="#"><?= !empty($pos[3]['tanggal_update']) ? date('d F Y', strtotime($pos[3]['tanggal_update'])) : '' ?></a></li>
                                                    </ul>

                                                    <div class="title">
                                                        <h2 class="h4">
                                                            <a href="<?= topNewsUrl($pos[3] ?? null) ?>" class="btn-link">
                                                                <?= htmlspecialchars($pos[3]['judul_artikel'] ?? 'Belum diatur', ENT_QUOTES, 'UTF-8') ?>
                                                            </a>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Post Item End -->
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Posisi 1: Tengah Besar -->
                                <div class="post--item post--layout-1 post--title-larger">
                                    <div class="post--img">
                                        <a href="<?= topNewsUrl($pos[1] ?? null) ?>" class="thumb" style="display:block;overflow:hidden;height:405px;">
                                            <img src="<?= topNewsImg($pos[1] ?? null, 3) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                        </a>
                                        <a href="<?= topNewsUrl($pos[1] ?? null) ?>" class="cat">
                                            <?= htmlspecialchars($pos[1]['kategori'] ?? 'Kategori', ENT_QUOTES, 'UTF-8') ?>
                                        </a>

                                        <div class="post--info">
                                            <ul class="nav meta">
                                                <li><a href="#"><?= htmlspecialchars($pos[1]['penulis'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></li>
                                                <li><a href="#"><?= !empty($pos[1]['tanggal_update']) ? date('d F Y', strtotime($pos[1]['tanggal_update'])) : '' ?></a></li>
                                            </ul>

                                            <div class="title">
                                                <h2 class="h4">
                                                    <a href="<?= topNewsUrl($pos[1] ?? null) ?>" class="btn-link">
                                                        <?= htmlspecialchars($pos[1]['judul_artikel'] ?? 'Belum diatur', ENT_QUOTES, 'UTF-8') ?>
                                                    </a>
                                                </h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Post Item End -->
                            </div>

                            <div class="col-md-3">
                                <div class="row gutter--15">
                                    <div class="col-md-12 col-xs-6 col-xxs-12">
                                        <!-- Posisi 4: Kanan Atas -->
                                        <div class="post--item post--layout-1 post--title-large">
                                            <div class="post--img">
                                                <a href="<?= topNewsUrl($pos[4] ?? null) ?>" class="thumb" style="display:block;overflow:hidden;height:195px;">
                                                    <img src="<?= topNewsImg($pos[4] ?? null, 4) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                                </a>
                                                <a href="<?= topNewsUrl($pos[4] ?? null) ?>" class="cat">
                                                    <?= htmlspecialchars($pos[4]['kategori'] ?? 'Kategori', ENT_QUOTES, 'UTF-8') ?>
                                                </a>

                                                <div class="post--info">
                                                    <ul class="nav meta">
                                                        <li><a href="#"><?= htmlspecialchars($pos[4]['penulis'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></li>
                                                        <li><a href="#"><?= !empty($pos[4]['tanggal_update']) ? date('d F Y', strtotime($pos[4]['tanggal_update'])) : '' ?></a></li>
                                                    </ul>

                                                    <div class="title">
                                                        <h2 class="h4">
                                                            <a href="<?= topNewsUrl($pos[4] ?? null) ?>" class="btn-link">
                                                                <?= htmlspecialchars($pos[4]['judul_artikel'] ?? 'Belum diatur', ENT_QUOTES, 'UTF-8') ?>
                                                            </a>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Post Item End -->
                                    </div>

                                    <div class="col-md-12 col-xs-6 hidden-xxs">
                                        <!-- Posisi 5: Kanan Bawah -->
                                        <div class="post--item post--layout-1 post--title-large">
                                            <div class="post--img">
                                                <a href="<?= topNewsUrl($pos[5] ?? null) ?>" class="thumb" style="display:block;overflow:hidden;height:195px;">
                                                    <img src="<?= topNewsImg($pos[5] ?? null, 5) ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
                                                </a>
                                                <a href="<?= topNewsUrl($pos[5] ?? null) ?>" class="cat">
                                                    <?= htmlspecialchars($pos[5]['kategori'] ?? 'Kategori', ENT_QUOTES, 'UTF-8') ?>
                                                </a>

                                                <div class="post--info">
                                                    <ul class="nav meta">
                                                        <li><a href="#"><?= htmlspecialchars($pos[5]['penulis'] ?? '', ENT_QUOTES, 'UTF-8') ?></a></li>
                                                        <li><a href="#"><?= !empty($pos[5]['tanggal_update']) ? date('d F Y', strtotime($pos[5]['tanggal_update'])) : '' ?></a></li>
                                                    </ul>

                                                    <div class="title">
                                                        <h2 class="h4">
                                                            <a href="<?= topNewsUrl($pos[5] ?? null) ?>" class="btn-link">
                                                                <?= htmlspecialchars($pos[5]['judul_artikel'] ?? 'Belum diatur', ENT_QUOTES, 'UTF-8') ?>
                                                            </a>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Post Item End -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Post Items End -->
                </div>
