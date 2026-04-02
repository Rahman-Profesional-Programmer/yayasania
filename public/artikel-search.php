<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Cari Artikel - Yayasan IA";

// Tentukan query berdasarkan parameter GET
$query_artikel = null;
$judul_halaman = "Semua Artikel";

if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search        = trim($_GET['search']);
    $judul_halaman = 'Hasil pencarian: "' . e($search) . '"';
    $stmt = $conn->prepare("SELECT * FROM artikel WHERE enable=1 AND hapus=1 AND (judul_artikel LIKE ? OR konten_artikel LIKE ?) ORDER BY tanggal_update DESC");
    $like = '%' . $search . '%';
    $stmt->bind_param("ss", $like, $like);

} elseif (isset($_GET['cari_ketegori']) && $_GET['cari_ketegori'] !== '') {
    $search        = trim($_GET['cari_ketegori']);
    $judul_halaman = 'Kategori: "' . e($search) . '"';
    $stmt = $conn->prepare("SELECT * FROM artikel WHERE enable=1 AND hapus=1 AND kategori LIKE ? ORDER BY tanggal_update DESC");
    $like = '%' . $search . '%';
    $stmt->bind_param("s", $like);

} elseif (isset($_GET['cari_tag']) && $_GET['cari_tag'] !== '') {
    $search_tag    = trim($_GET['cari_tag']);
    $judul_halaman = 'Tag: "' . e($search_tag) . '"';
    $stmt = $conn->prepare("SELECT a.* FROM artikel a JOIN artikel_tag t ON a.id_artikel = t.id_artikel WHERE a.enable=1 AND a.hapus=1 AND t.tag LIKE ? ORDER BY a.tanggal_update DESC");
    $like = '%' . $search_tag . '%';
    $stmt->bind_param("s", $like);

} else {
    $judul_halaman = "Semua Artikel";
    $stmt = $conn->prepare("SELECT * FROM artikel WHERE enable=1 AND hapus=1 ORDER BY tanggal_update DESC");
}

$stmt->execute();
$res_artikel = $stmt->get_result();
$stmt->close();
?>
<?php require_once __DIR__ . '/layout/header.php'; ?>

<!-- Breadcrumb -->
<div class="main--breadcrumb">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="<?= PUBLIC_URL ?>home.php" class="btn-link"><i class="fa fm fa-home"></i>Home</a></li>
            <li class="active"><span><?= $judul_halaman ?></span></li>
        </ul>
    </div>
</div>

<!-- Content -->
<div class="main-content--section pbottom--30">
    <div class="container">
        <div class="row">
            <!-- List Artikel -->
            <div class="main--content col-md-8 col-sm-7" data-sticky-content="true">
                <div class="sticky-content-inner">
                    <h5 class="mb--30"><?= $judul_halaman ?></h5>
                    <div class="post--items post--items-5 pd--30-0">
                        <ul class="nav">
                        <?php if ($res_artikel && $res_artikel->num_rows > 0): ?>
                            <?php while ($row = $res_artikel->fetch_assoc()): ?>
                            <li>
                                <div class="post--item post--title-larger">
                                    <div class="row">
                                        <div class="col-md-4 col-xs-4">
                                            <div class="post--img">
                                                <a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $row['id_artikel'] ?>" class="thumb">
                                                    <img src="<?= e(mediaUrl($row['gambar'])) ?>" alt="">
                                                </a>
                                                <a href="<?= PUBLIC_URL ?>artikel-search.php?cari_ketegori=<?= urlencode($row['kategori']) ?>" class="cat"><?= e($row['kategori']) ?></a>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-xs-8">
                                            <div class="post--info">
                                                <ul class="nav meta">
                                                    <li><?= e($row['penulis']) ?></li>
                                                    <li><?= $row['tanggal_update'] ?></li>
                                                </ul>
                                                <div class="title">
                                                    <h3 class="h4"><a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $row['id_artikel'] ?>" class="btn-link"><?= e($row['judul_artikel']) ?></a></h3>
                                                </div>
                                                <div class="post--content">
                                                    <p><?= excerpt($row['konten_artikel'], 180) ?></p>
                                                </div>
                                                <div class="post--action">
                                                    <a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $row['id_artikel'] ?>">Selengkapnya...</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li><p>Tidak ada artikel ditemukan.</p></li>
                        <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="main--sidebar col-md-4 col-sm-5 ptop--30 pbottom--30" data-sticky-content="true">
                <div class="sticky-content-inner">
                    <div class="widget">
                        <div class="widget--title"><h2 class="h4">Kategori</h2></div>
                        <ul class="widget--cats nav">
                            <?php
                                $res_kat = $conn->query("SELECT DISTINCT kategori, COUNT(*) as jml FROM artikel WHERE enable=1 AND hapus=1 GROUP BY kategori ORDER BY kategori");
                                while ($k = $res_kat->fetch_assoc()):
                            ?>
                            <li><a href="<?= PUBLIC_URL ?>artikel-search.php?cari_ketegori=<?= urlencode($k['kategori']) ?>"><?= e($k['kategori']) ?> <span>(<?= $k['jml'] ?>)</span></a></li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                    <div class="widget">
                        <div class="widget--title"><h2 class="h4">Tag</h2></div>
                        <div class="widget--tags">
                            <?php
                                $res_tags = $conn->query("SELECT DISTINCT tag FROM artikel_tag ORDER BY tag LIMIT 30");
                                while ($tg = $res_tags->fetch_assoc()):
                            ?>
                            <a href="<?= PUBLIC_URL ?>artikel-search.php?cari_tag=<?= urlencode($tg['tag']) ?>" class="btn btn-xs btn--color-2 radius-30"><?= e($tg['tag']) ?></a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
