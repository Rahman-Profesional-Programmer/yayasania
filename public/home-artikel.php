<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = "Beranda - Yayasan Ihsanul Amal Alabio";

// Ambil artikel terbaru yang aktif
$sql_artikel = "SELECT * FROM artikel WHERE enable = 1 AND hapus = 1 ORDER BY tanggal_update DESC LIMIT 12";
$res_artikel = $conn->query($sql_artikel);
?>
<?php require_once __DIR__ . '/layout/header.php'; ?>

<!-- News Ticker -->
<div class="news--ticker">
    <div class="container">
        <div class="title"><h2>Berita Terbaru</h2></div>
        <div class="news-updates--list" data-marquee="true">
            <ul class="nav">
                <?php
                    $ticker = $conn->query("SELECT judul_artikel, id_artikel FROM artikel WHERE enable=1 AND hapus=1 ORDER BY tanggal_update DESC LIMIT 5");
                    while ($t = $ticker->fetch_assoc()):
                ?>
                <li><h3 class="h3"><a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $t['id_artikel'] ?>"><?= e($t['judul_artikel']) ?></a></h3></li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>

<!-- Main Content Start -->
<div class="main-content--section pbottom--30">
    <div class="container">
        <div class="row">
            <!-- Post List -->
            <div class="main--content col-md-8 col-sm-7" data-sticky-content="true">
                <div class="sticky-content-inner">
                    <div class="post--items post--items-5 pd--30-0">
                        <ul class="nav">
                        <?php if ($res_artikel && $res_artikel->num_rows > 0): ?>
                            <?php while ($row = $res_artikel->fetch_assoc()): ?>
                            <li>
                                <div class="post--item post--title-larger">
                                    <div class="row">
                                        <div class="col-md-4 col-sm-12 col-xs-4 col-xxs-12">
                                            <div class="post--img">
                                                <a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $row['id_artikel'] ?>" class="thumb">
                                                    <img src="<?= e(mediaUrl($row['gambar'])) ?>" alt="<?= e($row['judul_artikel']) ?>">
                                                </a>
                                                <a href="<?= PUBLIC_URL ?>artikel-search.php?cari_ketegori=<?= urlencode($row['kategori']) ?>" class="cat"><?= e($row['kategori']) ?></a>
                                            </div>
                                        </div>
                                        <div class="col-md-8 col-sm-12 col-xs-8 col-xxs-12">
                                            <div class="post--info">
                                                <ul class="nav meta">
                                                    <li><a href="#"><?= e($row['penulis']) ?></a></li>
                                                    <li><a href="#"><?= $row['tanggal_update'] ?></a></li>
                                                </ul>
                                                <div class="title">
                                                    <h3 class="h4">
                                                        <a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $row['id_artikel'] ?>" class="btn-link">
                                                            <?= e($row['judul_artikel']) ?>
                                                        </a>
                                                    </h3>
                                                </div>
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
                            </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li><p class="text-center">Belum ada artikel yang dipublikasikan.</p></li>
                        <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="main--sidebar col-md-4 col-sm-5 ptop--30 pbottom--30" data-sticky-content="true">
                <div class="sticky-content-inner">
                    <!-- Widget Kategori -->
                    <div class="widget">
                        <div class="widget--title">
                            <h2 class="h4">Kategori</h2>
                        </div>
                        <ul class="widget--cats nav">
                            <?php
                                $res_kat = $conn->query("SELECT DISTINCT kategori, COUNT(*) as jml FROM artikel WHERE enable=1 AND hapus=1 GROUP BY kategori ORDER BY kategori");
                                while ($k = $res_kat->fetch_assoc()):
                            ?>
                            <li>
                                <a href="<?= PUBLIC_URL ?>artikel-search.php?cari_ketegori=<?= urlencode($k['kategori']) ?>">
                                    <?= e($k['kategori']) ?> <span>(<?= $k['jml'] ?>)</span>
                                </a>
                            </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <!-- Widget Tag -->
                    <div class="widget">
                        <div class="widget--title">
                            <h2 class="h4">Tag Populer</h2>
                        </div>
                        <div class="widget--tags">
                            <?php
                                $res_tags = $conn->query("SELECT DISTINCT tag FROM artikel_tag ORDER BY tag LIMIT 20");
                                while ($tg = $res_tags->fetch_assoc()):
                            ?>
                            <a href="<?= PUBLIC_URL ?>artikel-search.php?cari_tag=<?= urlencode($tg['tag']) ?>" class="btn btn-xs btn--color-2 radius-30">
                                <?= e($tg['tag']) ?>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
