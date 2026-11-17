<?php
header('Content-Type: application/json');
require 'koneksi.php';

// KUNCI RAHASIA API (Samakan dengan yang ada di kode ESP32)
define('API_SECRET_KEY', 'AERO_IOT_2026_RAHASIA');

// 1. Cek metode request (Utamakan POST dari ESP32, GET tetap didukung untuk tes)
$apiKey     = $_POST['api_key']    ?? $_GET['api_key']    ?? '';
$suhu       = $_POST['suhu']       ?? $_GET['suhu']       ?? null;
$kelembaban = $_POST['kelembaban'] ?? $_GET['kelembaban'] ?? null;
$tekanan    = $_POST['tekanan']    ?? $_GET['tekanan']    ?? 0;
$angin      = $_POST['angin']      ?? $_GET['angin']      ?? 0;
$baterai    = $_POST['baterai']    ?? $_GET['baterai']    ?? 0;
$voltase    = $_POST['voltase']    ?? $_GET['voltase']    ?? 0;
$mq         = $_POST['mq']         ?? $_GET['mq']         ?? 0;

// 2. Validasi Keamanan API Key
if ($apiKey !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode([
        "status"  => "unauthorized",
        "message" => "Akses ditolak! API Key tidak valid."
    ]);
    exit;
}

// 3. Validasi Kelengkapan & Tipe Data Numerik
if ($suhu === null || $kelembaban === null || !is_numeric($suhu) || !is_numeric($kelembaban)) {
    http_response_code(400);
    echo json_encode([
        "status"  => "error",
        "message" => "Data suhu dan kelembaban wajib dikirim dalam format angka."
    ]);
    exit;
}

// Konversi tipe data agar aman
$suhuVal       = (float) $suhu;
$kelembabanVal = (float) $kelembaban;
$tekananVal    = (float) $tekanan;
$anginVal      = (float) $angin;
$bateraiVal    = (int)   $baterai;
$voltaseVal    = (float) $voltase;
$mqVal         = (int)   $mq;

// 4. Simpan ke database menggunakan Prepared Statement (Anti SQL Injection)
$stmt = mysqli_prepare($conn, "INSERT INTO sensor_logs (suhu, kelembaban, tekanan, angin, baterai, voltase, mq) VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ddddidi", $suhuVal, $kelembabanVal, $tekananVal, $anginVal, $bateraiVal, $voltaseVal, $mqVal);

if (mysqli_stmt_execute($stmt)) {
    // 5. Auto-Cleanup: Jaga penampungan maksimal 10.000 baris terbaru agar DB tetap ringan
    mysqli_query($conn, "DELETE FROM sensor_logs WHERE id <= (SELECT id FROM (SELECT id FROM sensor_logs ORDER BY id DESC LIMIT 1 OFFSET 10000) AS tmp)");

    echo json_encode([
        "status"  => "success",
        "message" => "Data telemetri aman & tersimpan.",
        "data"    => [
            "suhu"       => $suhuVal,
            "kelembaban" => $kelembabanVal,
            "tekanan"    => $tekananVal,
            "angin"      => $anginVal,
            "baterai"    => $bateraiVal,
            "voltase"    => $voltaseVal,
            "mq"         => $mqVal
        ]
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal menyimpan data ke database."
    ]);
}

mysqli_stmt_close($stmt);
?>