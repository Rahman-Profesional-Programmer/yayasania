<?php
/**
 * admin/artikel/import-proses.php
 *
 * Handler AJAX untuk bulk-import artikel dari file Excel.
 *
 * Alur:
 *  1. Terima POST multipart/form-data dengan field "import_file".
 *  2. Validasi tipe/ukuran file.
 *  3. Baca spreadsheet dengan PhpSpreadsheet.
 *  4. Proses setiap baris: validasi → insert artikel → insert tags.
 *  5. Logging setiap langkah melalui DebugBar MessagesCollector.
 *  6. Kembalikan JSON:
 *       { success, total, imported, failed, skipped, elapsed, results[], logs[] }
 *
 * Kolom yang diharapkan di file Excel (urutan wajib sesuai template):
 *   A – judul_artikel  (wajib)
 *   B – kategori
 *   C – gambar         (URL/link saja)
 *   D – konten_artikel (wajib)
 *   E – tags           (pisah koma)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();
requireLogin();

// Semua output harus JSON murni
header('Content-Type: application/json; charset=utf-8');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;

// ─────────────────────────────────────────────────────────────
// DebugBar MessagesCollector — logger per-baris
// Collect() menghasilkan array messages yang dikembalikan di JSON
// sehingga frontend bisa merender log tanpa render HTML toolbar.
// ─────────────────────────────────────────────────────────────
use DebugBar\DataCollector\MessagesCollector;

$importLogger = new MessagesCollector('import_log');

/**
 * Tambahkan pesan log ke collector dan ke array ringkas.
 * Array $plainLog digunakan sebagai fallback di JSON response.
 *
 * @param MessagesCollector $logger
 * @param array             &$plainLog
 * @param string            $level   info | notice | warning | error
 * @param string            $message
 * @param int|null          $row     Nomor baris spreadsheet (null = global)
 */
function importLog(
    MessagesCollector $logger,
    array &$plainLog,
    string $level,
    string $message,
    ?int $row = null
): void {
    $prefix = $row !== null ? "[Baris $row] " : '';
    $full   = $prefix . $message;

    // Kirim ke DebugBar collector
    $logger->$level($full);

    // Simpan juga ke array untuk respons JSON
    $plainLog[] = [
        'type'    => $level,
        'row'     => $row,
        'message' => $full,
        'time'    => date('H:i:s'),
    ];
}

// ─────────────────────────────────────────────────────────────
// 1. Validasi metode & keberadaan file
// ─────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan.']);
    exit;
}

if (empty($_FILES['import_file']) || ($_FILES['import_file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
    echo json_encode(['success' => false, 'message' => 'Pilih file Excel terlebih dahulu.']);
    exit;
}

$file = $_FILES['import_file'];
$plainLog = [];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload gagal (kode error PHP: ' . $file['error'] . ').']);
    exit;
}

// Batasi ukuran 10 MB
if ($file['size'] > 10_000_000) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 10 MB.']);
    exit;
}

// Validasi ekstensi
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['xlsx', 'xls', 'csv'], true)) {
    echo json_encode(['success' => false, 'message' => 'Format tidak didukung. Gunakan .xlsx, .xls, atau .csv.']);
    exit;
}

// ─────────────────────────────────────────────────────────────
// 2. Baca spreadsheet
// ─────────────────────────────────────────────────────────────
importLog($importLogger, $plainLog, 'info', 'Memulai baca file: ' . htmlspecialchars($file['name']));

$startTime = microtime(true);

try {
    // IOFactory::load mendeteksi format otomatis berdasarkan tmp file
    $spreadsheet = IOFactory::load($file['tmp_name']);
    $sheet       = $spreadsheet->getActiveSheet();

    /**
     * toArray(null, true, true, true):
     *   param1 null       = nilai default untuk sel kosong
     *   param2 true       = hitung formula
     *   param3 true       = format nilai sesuai format sel
     *   param4 true       = index berdasarkan huruf kolom (A, B, C, ...)
     */
    $rows = $sheet->toArray(null, true, true, true);

} catch (SpreadsheetException $e) {
    echo json_encode(['success' => false, 'message' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
    exit;
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => 'Error tak terduga: ' . $e->getMessage()]);
    exit;
}

// Hitung baris data (kurangi baris header + keterangan)
// Baris 1 = nama kolom, Baris 2 = keterangan (keduanya dilewati)
$totalRows = max(0, count($rows) - 2);

importLog($importLogger, $plainLog, 'info', "File berhasil dibaca. Ditemukan {$totalRows} baris data.");

if ($totalRows <= 0) {
    echo json_encode(['success' => false, 'message' => 'File kosong atau tidak ada data di luar baris header.']);
    exit;
}

// ─────────────────────────────────────────────────────────────
// 3. Proses setiap baris
// ─────────────────────────────────────────────────────────────
$penulis      = $_SESSION['email'] ?? DEFAULT_USER_EMAIL;
$successCount = 0;
$failCount    = 0;
$skipCount    = 0;
$results      = [];

// Lewati dua baris pertama (header + keterangan)
$skipHeaders = 2;

foreach ($rows as $rowIndex => $row) {
    // Lewati baris header (baris 1 dan 2 dalam spreadsheet)
    if ($skipHeaders > 0) {
        $skipHeaders--;
        continue;
    }

    // ──────────────────────────────────────────────────────
    // Petakan kolom ke variabel
    // A=judul, B=kategori, C=gambar, D=konten, E=tags
    // ──────────────────────────────────────────────────────
    $judul    = trim((string) ($row['A'] ?? ''));
    $kategori = trim((string) ($row['B'] ?? ''));
    $gambar   = trim((string) ($row['C'] ?? ''));
    $konten   = trim((string) ($row['D'] ?? ''));
    $tagsRaw  = trim((string) ($row['E'] ?? ''));

    // Lewati baris yang benar-benar kosong (semua kolom utama kosong)
    if ($judul === '' && $konten === '' && $kategori === '') {
        $skipCount++;
        importLog($importLogger, $plainLog, 'notice', 'Baris dilewati: semua kolom utama kosong.', $rowIndex);
        continue;
    }

    // ──────────────────────────────────────────────────────
    // Validasi kolom wajib
    // ──────────────────────────────────────────────────────
    if ($judul === '') {
        $failCount++;
        importLog($importLogger, $plainLog, 'error', 'Ditolak: kolom judul_artikel kosong.', $rowIndex);
        $results[] = ['row' => $rowIndex, 'status' => 'error', 'judul' => '-', 'reason' => 'judul_artikel kosong'];
        continue;
    }

    if ($konten === '') {
        $failCount++;
        importLog($importLogger, $plainLog, 'error', 'Ditolak: kolom konten_artikel kosong.', $rowIndex);
        $results[] = ['row' => $rowIndex, 'status' => 'error', 'judul' => $judul, 'reason' => 'konten_artikel kosong'];
        continue;
    }

    // ──────────────────────────────────────────────────────
    // Validasi foto — hanya URL eksternal yang diterima
    // ──────────────────────────────────────────────────────
    if ($gambar !== '') {
        if (!filter_var($gambar, FILTER_VALIDATE_URL)) {
            // URL tidak valid → abaikan foto, lanjutkan proses artikel
            importLog($importLogger, $plainLog, 'warning', "URL foto tidak valid, diabaikan: {$gambar}", $rowIndex);
            $gambar = '';
        } elseif (mb_strlen($gambar) > 500) {
            // URL terlalu panjang → abaikan untuk menghindari error DB
            importLog($importLogger, $plainLog, 'warning', 'URL foto terlalu panjang (>500 karakter), diabaikan.', $rowIndex);
            $gambar = '';
        }
    }

    // ──────────────────────────────────────────────────────
    // Insert artikel
    // ──────────────────────────────────────────────────────
    $stmt = $conn->prepare(
        "INSERT INTO artikel
            (judul_artikel, konten_artikel, gambar, penulis, kategori, tanggal_update, viewer, enable, hapus)
         VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP(), 0, 1, 1)"
    );

    if (!$stmt) {
        $failCount++;
        importLog($importLogger, $plainLog, 'error', 'Prepare statement gagal: ' . $conn->error, $rowIndex);
        $results[] = ['row' => $rowIndex, 'status' => 'error', 'judul' => $judul, 'reason' => 'Database prepare error'];
        continue;
    }

    $stmt->bind_param('sssss', $judul, $konten, $gambar, $penulis, $kategori);

    if (!$stmt->execute()) {
        $failCount++;
        $reason = $stmt->error;
        importLog($importLogger, $plainLog, 'error', "Insert gagal: {$reason}", $rowIndex);
        $results[] = ['row' => $rowIndex, 'status' => 'error', 'judul' => $judul, 'reason' => $reason];
        $stmt->close();
        continue;
    }

    $artikelId = (int) $conn->insert_id;
    $stmt->close();

    // ──────────────────────────────────────────────────────
    // Insert tags (boleh kosong)
    // Pisahkan dengan koma, bersihkan, hapus duplikat
    // ──────────────────────────────────────────────────────
    $tagCount = 0;

    if ($tagsRaw !== '') {
        $tags = array_values(
            array_unique(
                array_filter(
                    array_map('trim', explode(',', $tagsRaw))
                )
            )
        );

        if (!empty($tags)) {
            $stmtTag = $conn->prepare(
                "INSERT INTO artikel_tag (id_artikel, tag) VALUES (?, ?)"
            );

            if ($stmtTag) {
                foreach ($tags as $tag) {
                    $stmtTag->bind_param('is', $artikelId, $tag);
                    $stmtTag->execute();
                }
                $stmtTag->close();
                $tagCount = count($tags);
            }
        }
    }

    // ──────────────────────────────────────────────────────
    // Catat sukses
    // ──────────────────────────────────────────────────────
    $successCount++;

    $shortJudul = mb_strlen($judul) > 55 ? mb_substr($judul, 0, 55) . '…' : $judul;
    $tagInfo    = $tagCount > 0 ? " + {$tagCount} tag" : '';
    importLog($importLogger, $plainLog, 'info', "Berhasil: \"{$shortJudul}\"{$tagInfo}", $rowIndex);

    $results[] = [
        'row'    => $rowIndex,
        'status' => 'success',
        'judul'  => $shortJudul,
        'tags'   => $tagCount,
    ];
}

// ─────────────────────────────────────────────────────────────
// 4. Susun ringkasan & kumpulkan log dari DebugBar collector
// ─────────────────────────────────────────────────────────────
$elapsed = round(microtime(true) - $startTime, 3);

importLog(
    $importLogger,
    $plainLog,
    'info',
    "Selesai dalam {$elapsed} detik. " .
    "Berhasil: {$successCount} | Gagal: {$failCount} | Dilewati: {$skipCount}."
);

/**
 * Ambil data log dari DebugBar collector.
 * Format: array dari ['message' => ..., 'label' => 'INFO'|'WARNING'|..., 'time' => ...]
 * Ini berguna sebagai alternatif structured log yang dibawa dalam respons.
 */
$debugbarMessages = $importLogger->collect()['messages'] ?? [];

// ─────────────────────────────────────────────────────────────
// 5. Kembalikan JSON hasil import
// ─────────────────────────────────────────────────────────────
echo json_encode([
    'success'     => true,
    'total'       => $successCount + $failCount + $skipCount,
    'imported'    => $successCount,
    'failed'      => $failCount,
    'skipped'     => $skipCount,
    'elapsed'     => $elapsed,

    // Hasil per-baris: digunakan frontend untuk tabel ringkasan
    'results'     => $results,

    // Log ringkas per-aksi: digunakan frontend untuk panel debug
    'logs'        => $plainLog,

    // Log terstruktur dari DebugBar collector (dikirim bersama untuk keperluan debugging lanjutan)
    'debugbar_messages' => $debugbarMessages,
]);
