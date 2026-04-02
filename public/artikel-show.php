<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$id = (int) ($_GET['id_artikel'] ?? 0);
if (!$id) {
    redirect(PUBLIC_URL . 'home.php');
}

// Ambil artikel
$stmt = $conn->prepare("SELECT * FROM artikel WHERE id_artikel = ? AND hapus = 1 LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$artikel = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$artikel) {
    redirect(PUBLIC_URL . 'home.php');
}

// Update viewer
$conn->query("UPDATE artikel SET viewer = viewer + 1 WHERE id_artikel = $id");

// Ambil data penulis
$stmt_user = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt_user->bind_param("s", $artikel['penulis']);
$stmt_user->execute();
$penulis_data = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

// Ambil tags artikel
$stmt_tag = $conn->prepare("SELECT tag FROM artikel_tag WHERE id_artikel = ?");
$stmt_tag->bind_param("i", $id);
$stmt_tag->execute();
$res_tags = $stmt_tag->get_result();
$stmt_tag->close();

$pageTitle = e($artikel['judul_artikel']) . " - Yayasan IA";
?>
<?php require_once __DIR__ . '/layout/header.php'; ?>

<!-- Breadcrumb -->
<div class="main--breadcrumb">
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="<?= PUBLIC_URL ?>home.php" class="btn-link"><i class="fa fm fa-home"></i>Home</a></li>
            <li><a href="<?= PUBLIC_URL ?>artikel-search.php?cari_ketegori=<?= urlencode($artikel['kategori']) ?>"><?= e($artikel['kategori']) ?></a></li>
            <li class="active"><span><?= e(mb_substr($artikel['judul_artikel'], 0, 50)) ?>...</span></li>
        </ul>
    </div>
</div>

<!-- Main Content -->
<div class="main-content--section pbottom--30">
    <div class="container">
        <div class="row">
            <!-- Artikel -->
            <div class="main--content col-md-8 col-sm-7">
                <div class="post--single">

                    <div class="post--img">
                        <?php if ($artikel['gambar']): ?>
                        <img src="<?= e(mediaUrl($artikel['gambar'])) ?>" class="img-responsive" alt="<?= e($artikel['judul_artikel']) ?>">
                        <?php endif; ?>
                        <a href="<?= PUBLIC_URL ?>artikel-search.php?cari_ketegori=<?= urlencode($artikel['kategori']) ?>" class="cat"><?= e($artikel['kategori']) ?></a>
                    </div>

                    <div class="post--info">
                        <ul class="nav meta">
                            <li><a href="#"><?= e($penulis_data['name_show'] ?? $artikel['penulis']) ?></a></li>
                            <li><a href="#"><?= $artikel['tanggal_update'] ?></a></li>
                            <li><i class="fa fa-eye"></i> <?= $artikel['viewer'] ?> views</li>
                        </ul>
                        <div class="title">
                            <h1 class="h2"><?= e($artikel['judul_artikel']) ?></h1>
                        </div>
                    </div>

                    <div class="post--content">
                        <?= nl2br(e($artikel['konten_artikel'])) ?>
                    </div>

                    <!-- Tags -->
                    <?php if ($res_tags->num_rows > 0): ?>
                    <div class="post--tags">
                        <strong>Tags: </strong>
                        <?php while ($tg = $res_tags->fetch_assoc()): ?>
                            <a href="<?= PUBLIC_URL ?>artikel-search.php?cari_tag=<?= urlencode($tg['tag']) ?>" class="btn btn-xs btn--color-2 radius-30">
                                <?= e($tg['tag']) ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Author Box -->
                    <?php if ($penulis_data): ?>
                    <div class="widget--author" style="border:1px solid #eee; padding:20px; margin-top:30px;">
                        <div class="row">
                            <div class="col-sm-2">
                                <?php if (!empty($penulis_data['foto'])): ?>
                                    <img src="<?= e(mediaUrl($penulis_data['foto'])) ?>" class="img-circle" alt="" width="70">
                                <?php else: ?>
                                    <div style="width:70px;height:70px;background:#ddd;border-radius:50%;display:flex;align-items:center;justify-content:center"><i class="fa fa-user fa-2x"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-10">
                                <h4><?= e($penulis_data['name_show'] ?? $penulis_data['email']) ?></h4>
                                <p><?= e($penulis_data['diskripsi'] ?? '') ?></p>
                                <?php if (!empty($penulis_data['facebook'])): ?>
                                    <a href="<?= e($penulis_data['facebook']) ?>" target="_blank"><i class="fa fa-facebook"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($penulis_data['instagram'])): ?>
                                    <a href="<?= e($penulis_data['instagram']) ?>" target="_blank"><i class="fa fa-instagram"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- Sidebar -->
            <div class="main--sidebar col-md-4 col-sm-5 ptop--30 pbottom--30" data-sticky-content="true">
                <div class="sticky-content-inner">
                    <!-- Kategori -->
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

                    <!-- Artikel Terkait -->
                    <div class="widget">
                        <div class="widget--title"><h2 class="h4">Artikel Terkait</h2></div>
                        <?php
                            $kat = $artikel['kategori'];
                            $stmt_rel = $conn->prepare("SELECT id_artikel, judul_artikel, gambar, tanggal_update FROM artikel WHERE enable=1 AND hapus=1 AND kategori=? AND id_artikel != ? ORDER BY tanggal_update DESC LIMIT 5");
                            $stmt_rel->bind_param("si", $kat, $id);
                            $stmt_rel->execute();
                            $res_rel = $stmt_rel->get_result();
                            $stmt_rel->close();
                        ?>
                        <?php while ($rel = $res_rel->fetch_assoc()): ?>
                        <div class="post--item post--layout-2 pd--10-0">
                            <div class="post--img">
                                <a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $rel['id_artikel'] ?>">
                                    <img src="<?= e(mediaUrl($rel['gambar'])) ?>" alt="">
                                </a>
                            </div>
                            <div class="post--info">
                                <div class="title"><h3 class="h6"><a href="<?= PUBLIC_URL ?>artikel-show.php?id_artikel=<?= $rel['id_artikel'] ?>"><?= e($rel['judul_artikel']) ?></a></h3></div>
                                <small><?= $rel['tanggal_update'] ?></small>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
