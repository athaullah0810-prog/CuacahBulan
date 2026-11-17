<?php
session_start();
header('Content-Type: application/json');
require 'koneksi.php';

// Panggil file PHPMailer secara manual (Tanpa Composer)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$input = json_decode(file_get_contents("php://input"), true);
$action = $input['action'] ?? '';

if ($action === 'request_otp') {
    $email = mysqli_real_escape_string($conn, trim($input['email'] ?? ''));
    
    // Cek apakah email sudah terdaftar
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(["status" => "error", "message" => "Email sudah terdaftar di sistem!"]);
        exit;
    }

    // Generate 6 digit kode OTP
    $otp = rand(100000, 999999);
    
    // Simpan ke session (berlaku 5 menit)
    $_SESSION['otp_data'] = [
        'email' => $email,
        'otp' => $otp,
        'expired' => time() + 300 
    ];

    // Mulai Kirim Email via PHPMailer SMTP Gmail
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi Server SMTP Gmail
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'iotcuacah@gmail.com';      // <-- Ganti dengan email Gmail Anda
        $mail->Password   = 'dwef bxed nsnm orpq';       // <-- Ganti dengan 16 digit App Password Gmail Anda
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Pengirim dan Penerima
        $mail->setFrom('iotcuacah@gmail.com', 'AeroSense IoT Security');
        $mail->addAddress($email); // Email user yang mendaftar

        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Kode Verifikasi OTP Anda - AeroSense IoT';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; background: #0f172a; color: #f8fafc; border-radius: 10px;'>
                <h2 style='color: #38bdf8;'>AeroSense IoT Verification</h2>
                <p>Halo,</p>
                <p>Terima kasih telah mendaftar. Gunakan kode OTP di bawah ini untuk memverifikasi akun Anda:</p>
                <div style='background: #1e293b; padding: 15px; text-align: center; font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #34d399; margin: 20px 0; border-radius: 8px;'>
                    {$otp}
                </div>
                <p style='font-size: 12px; color: #94a3b8;'>Kode ini hanya berlaku selama 5 menit. Jangan berikan kode ini kepada siapapun.</p>
            </div>
        ";

        $mail->send();
        
        echo json_encode([
            "status" => "success", 
            "message" => "Kode OTP berhasil dikirim ke email!"
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error", 
            "message" => "Gagal mengirim email OTP. Pastikan koneksi & setting SMTP benar. Error: {$mail->ErrorInfo}"
        ]);
    }
    exit;
}

if ($action === 'verify_otp') {
    $email = mysqli_real_escape_string($conn, trim($input['email'] ?? ''));
    $nama = mysqli_real_escape_string($conn, trim($input['nama'] ?? ''));
    $passwordRaw = $input['password'] ?? '';
    $userOtp = trim($input['otp'] ?? '');

    if (!isset($_SESSION['otp_data']) || $_SESSION['otp_data']['email'] !== $email) {
        echo json_encode(["status" => "error", "message" => "Sesi OTP kedaluwarsa atau tidak valid. Silakan ulangi."]);
        exit;
    }

    if (time() > $_SESSION['otp_data']['expired']) {
        echo json_encode(["status" => "error", "message" => "Kode OTP sudah kedaluwarsa!"]);
        exit;
    }

    if ($_SESSION['otp_data']['otp'] != $userOtp) {
        echo json_encode(["status" => "error", "message" => "Kode OTP salah!"]);
        exit;
    }

    $hashedPassword = password_hash($passwordRaw, PASSWORD_DEFAULT);
    $insert = mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$hashedPassword', 'user')");

    if ($insert) {
        unset($_SESSION['otp_data']);
        echo json_encode(["status" => "success", "message" => "Akun berhasil dibuat."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal menyimpan akun ke database."]);
    }
    exit;
}
?>