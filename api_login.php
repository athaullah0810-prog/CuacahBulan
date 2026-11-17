<?php
session_start();
header('Content-Type: application/json');
require 'koneksi.php';

$data = json_decode(file_get_contents("php://input"), true);
$email = mysqli_real_escape_string($conn, $data['email'] ?? '');
$password = $data['password'] ?? '';
$platform = $data['platform'] ?? 'web'; // 'web' atau 'android'

$query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
$user = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {
    // Jika login lewat Web, pastikan rolenya harus 'admin'
    if ($platform === 'web' && $user['role'] !== 'admin') {
        echo json_encode([
            "status" => "error", 
            "message" => "Akses ditolak! Pengguna umum silakan login via Aplikasi Android."
        ]);
        exit;
    }

    // Jika user Android belum verifikasi OTP
    if ($user['role'] === 'user' && $user['is_verified'] == 0) {
        echo json_encode([
            "status" => "unverified", 
            "message" => "Akun belum diverifikasi OTP."
        ]);
        exit;
    }

    // Simpan sesi
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];

    echo json_encode([
        "status" => "success",
        "role" => $user['role'],
        "redirect" => ($user['role'] === 'admin') ? 'index.html' : 'user_dashboard.html'
    ]);
} else {
    echo json_encode(["status" => "error", "message" => "Email atau password salah!"]);
}
?>