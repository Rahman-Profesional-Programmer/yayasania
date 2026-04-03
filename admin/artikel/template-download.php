<?php
/**
 * admin/artikel/template-download.php
 *
 * Membuat dan mengunduh file Excel (.xlsx) sebagai template bulk-import artikel.
 * Kolom yang tersedia:
 *   A – judul_artikel  (wajib, string)
 *   B – kategori       (opsional, string)
 *   C – gambar         (opsional, URL/link saja – max 500 karakter)
 *   D – konten_artikel (wajib, string)
 *   E – tags           (opsional, pisahkan dengan koma)
 *
 * Diakses melalui GET dari halaman index artikel (login wajib).
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../vendor/autoload.php';

session_start();
requireLogin();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

// ─────────────────────────────────────────────────────────────
// 1. Buat spreadsheet
// ─────────────────────────────────────────────────────────────
$spreadsheet = new Spreadsheet();
$sheet       = $spreadsheet->getActiveSheet();
$sheet->setTitle('Import Artikel');

// ─────────────────────────────────────────────────────────────
// 2. Definisi kolom: [huruf => [header, deskripsi, lebar]]
// ─────────────────────────────────────────────────────────────
$columns = [
    'A' => ['judul_artikel',  'Judul artikel — WAJIB diisi, teks bebas',                          42],
    'B' => ['kategori',       'Kategori artikel (teks bebas)',                                     24],
    'C' => ['gambar',         'Link/URL foto (https://...) — hanya URL, bukan upload file',       58],
    'D' => ['konten_artikel', 'Isi artikel — WAJIB diisi, boleh panjang',                         58],
    'E' => ['tags',           'Tag artikel, pisahkan dengan koma. Contoh: berita, pendidikan',     36],
];

// ─────────────────────────────────────────────────────────────
// 3. Baris 1 – Judul/nama kolom (header teknis)
// ─────────────────────────────────────────────────────────────
foreach ($columns as $col => [$header]) {
    $sheet->setCellValue($col . '1', $header);
}

// Style baris header: biru bootstrap, teks putih, bold, center
$sheet->getStyle('A1:E1')->applyFromArray([
    'font' => [
        'bold'  => true,
        'size'  => 11,
        'color' => ['rgb' => 'FFFFFF'],
    ],
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '0D6EFD'],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical'   => Alignment::VERTICAL_CENTER,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => '6EA0FF'],
        ],
    ],
]);
$sheet->getRowDimension(1)->setRowHeight(24);

// ─────────────────────────────────────────────────────────────
// 4. Baris 2 – Penjelasan setiap kolom (baris keterangan)
// ─────────────────────────────────────────────────────────────
foreach ($columns as $col => [, $desc]) {
    $sheet->setCellValue($col . '2', $desc);
}

// Style baris keterangan: abu-abu muda, italic
$sheet->getStyle('A2:E2')->applyFromArray([
    'font' => [
        'italic' => true,
        'size'   => 9,
        'color'  => ['rgb' => '6C757D'],
    ],
    'fill' => [
        'fillType'   => Fill::FILL_SOLID,
        'startColor' => ['rgb' => 'F8F9FA'],
    ],
    'alignment' => [
        'wrapText'   => true,
        'vertical'   => Alignment::VERTICAL_TOP,
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color'       => ['rgb' => 'DEE2E6'],
        ],
    ],
]);
$sheet->getRowDimension(2)->setRowHeight(32);

// ─────────────────────────────────────────────────────────────
// 5. Baris 3 & 4 – Data contoh agar user langsung paham format
// ─────────────────────────────────────────────────────────────
$samples = [
    3 => [
        'A' => 'Kegiatan Bakti Sosial Ramadhan 1446 H',
        'B' => 'Kegiatan',
        'C' => 'https://example.com/foto-bakti-sosial.jpg',
        'D' => 'Yayasan Ihsanul Amal Alabio menggelar kegiatan bakti sosial dalam rangka menyambut Ramadhan 1446 H. Kegiatan ini diikuti oleh ratusan warga sekitar.',
        'E' => 'bakti sosial, ramadhan, yayasan',
    ],
    4 => [
        'A' => 'Pengumuman Penerimaan Siswa Baru Tahun 2026',
        'B' => 'Pengumuman',
        'C' => '',
        'D' => 'Kami membuka pendaftaran siswa baru untuk tahun pelajaran 2026/2027. Informasi lengkap bisa menghubungi panitia penerimaan.',
        'E' => 'ppdb, siswa baru, 2026',
    ],
];

foreach ($samples as $row => $data) {
    foreach ($data as $col => $val) {
        $sheet->setCellValue($col . $row, $val);
    }
    // Beri warna berganti untuk keterbacaan
    $fillColor = ($row % 2 === 1) ? 'EFF6FF' : 'FFFFFF';
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DEE2E6']]],
    ]);
}

// ─────────────────────────────────────────────────────────────
// 6. Lebar kolom
// ─────────────────────────────────────────────────────────────
foreach ($columns as $col => [,, $width]) {
    $sheet->getColumnDimension($col)->setWidth($width);
}

// Wrap text & tinggi baris untuk kolom isi artikel (D)
$sheet->getStyle('D3:D4')->getAlignment()->setWrapText(true);
$sheet->getRowDimension(3)->setRowHeight(52);
$sheet->getRowDimension(4)->setRowHeight(52);

// ─────────────────────────────────────────────────────────────
// 7. Freeze row header + keterangan
// ─────────────────────────────────────────────────────────────
$sheet->freezePane('A3');

// ─────────────────────────────────────────────────────────────
// 8. Sheet kedua – Petunjuk penggunaan
// ─────────────────────────────────────────────────────────────
$guideSheet = $spreadsheet->createSheet();
$guideSheet->setTitle('Petunjuk');
$guideSheet->getColumnDimension('A')->setWidth(80);

$guides = [
    ['PETUNJUK IMPORT ARTIKEL', true, 14, 'FFFFFF', '0D6EFD'],
    ['', false, 11, '000000', 'FFFFFF'],
    ['Cara menggunakan template ini:', true, 11, '000000', 'FFFFFF'],
    ['1. Isi data mulai dari baris ke-3 pada sheet "Import Artikel".', false, 11, '333333', 'FFFFFF'],
    ['2. Jangan mengubah atau menghapus baris 1 (nama kolom) dan baris 2 (keterangan).', false, 11, '333333', 'FFFFFF'],
    ['3. Kolom wajib: judul_artikel dan konten_artikel harus diisi.', false, 11, '333333', 'FFFFFF'],
    ['4. Kolom gambar: hanya URL/link, bukan path file lokal. Kosongkan jika tidak ada.', false, 11, '333333', 'FFFFFF'],
    ['5. Kolom tags: pisahkan dengan koma. Contoh: berita, pendidikan, ramadhan', false, 11, '333333', 'FFFFFF'],
    ['6. Baris yang kolom judul DAN isi-nya kosong akan otomatis dilewati.', false, 11, '333333', 'FFFFFF'],
    ['', false, 11, '000000', 'FFFFFF'],
    ['Format yang didukung saat upload:', true, 11, '000000', 'FFFFFF'],
    ['• .xlsx  (direkomendasikan)', false, 11, '333333', 'FFFFFF'],
    ['• .xls   (Excel lama)', false, 11, '333333', 'FFFFFF'],
    ['• .csv   (pastikan encoding UTF-8)', false, 11, '333333', 'FFFFFF'],
    ['', false, 11, '000000', 'FFFFFF'],
    ['Batas upload: maksimal 10 MB per file.', false, 11, 'D1001E', 'FFFFFF'],
];

foreach ($guides as $i => [$text, $bold, $size, $fontColor, $bg]) {
    $cell = 'A' . ($i + 1);
    $guideSheet->setCellValue($cell, $text);
    $style = ['font' => ['bold' => $bold, 'size' => $size, 'color' => ['rgb' => $fontColor]]];
    if ($bg !== 'FFFFFF') {
        $style['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]];
    }
    $guideSheet->getStyle($cell)->applyFromArray($style);
}

// Set sheet aktif kembali ke sheet pertama
$spreadsheet->setActiveSheetIndex(0);

// ─────────────────────────────────────────────────────────────
// 9. Kirim response download
// ─────────────────────────────────────────────────────────────
$filename = 'template-import-artikel-' . date('Ymd') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
