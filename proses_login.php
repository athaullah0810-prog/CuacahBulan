<?php
session_start();
header('Content-Type: application/json');
require 'koneksi.php';

$input = json_decode(file_get_contents("php://input"), true);

$email    = mysqli_real_escape_string($conn, trim($input['email'] ?? ''));
$password = $input['password'] ?? '';
$roleTab  = $input['role'] ?? 'admin'; // 'admin' atau 'user'

if (empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Email dan password wajib diisi."]);
    exit;
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
$user  = mysqli_fetch_assoc($query);

if ($user && password_verify($password, $user['password'])) {
    // Validasi kecocokan tab login dengan role di database
    if ($user['role'] !== $roleTab) {
        $tabBenar = ($user['role'] === 'admin') ? 'Admin / Owner' : 'User Monitoring';
        echo json_encode([
            "status" => "error",
            "message" => "Akun ini terdaftar sebagai role {$user['role']}. Silakan pilih tab '$tabBenar'."
        ]);
        exit;
    }

    // Simpan sesi login
    $_SESSION['login']   = true;
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nama']    = $user['nama'];
    $_SESSION['role']    = $user['role'];

    // Tentukan halaman tujuan berdasarkan role
    $redirect = ($user['role'] === 'admin') ? 'admin/index.php' : 'user_dashboard.php';

    echo json_encode([
        "status"   => "success",
        "redirect" => $redirect
    ]);
} else {
    echo json_encode([
        "status"  => "error",
        "message" => "Email atau password salah!"
    ]);
}
?>