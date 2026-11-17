<?php
header('Content-Type: application/json');
require 'koneksi.php';

// KUNCI RAHASIA API (Harus sama dengan yang ada di ESP32 dan api_sensor.php)
define('API_SECRET_KEY', 'AERO_IOT_2026_RAHASIA');

// Ambil API Key dari parameter GET atau POST yang dikirim oleh aplikasi Android
$apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';

// 1. Validasi Keamanan API Key
if ($apiKey !== API_SECRET_KEY) {
    http_response_code(401);
    echo json_encode([
        "status"  => "unauthorized",
        "message" => "Akses ditolak! API Key tidak valid."
    ]);
    exit;
}

// 2. Ambil data sensor terbaru dari database
$query = mysqli_query($conn, "SELECT * FROM sensor_logs ORDER BY id DESC LIMIT 1");
$dataSensor = mysqli_fetch_assoc($query);

if ($dataSensor) {
    echo json_encode([
        "status"  => "success",
        "message" => "Berhasil mengambil data sensor terbaru.",
        "data"    => [
            "suhu"       => (float) $dataSensor['suhu'],
            "kelembaban" => (float) $dataSensor['kelembaban'],
            "tekanan"    => (float) $dataSensor['tekanan'],
            "angin"      => (float) $dataSensor['angin'],
            "baterai"    => (int)   $dataSensor['baterai'],
            "voltase"    => (float) $dataSensor['voltase'],
            "mq"         => (int)   $dataSensor['mq'],
            "waktu"      => $dataSensor['created_at']
        ]
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        "status"  => "error",
        "message" => "Belum ada data sensor yang tersimpan di database."
    ]);
}
?>