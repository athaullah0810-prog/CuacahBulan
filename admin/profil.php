<?php
session_start();
require '../koneksi.php';

// Proteksi: Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

$userId = $_SESSION['user_id'];
$pesanSukses = "";
$pesanError = "";

// Proses Update Profil jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaBaru  = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $emailBaru = mysqli_real_escape_string($conn, trim($_POST['email']));
    $passBaru  = $_POST['password_baru'] ?? '';

    // Cek apakah email baru sudah dipakai akun lain
    $cekEmail = mysqli_query($conn, "SELECT id FROM users WHERE email = '$emailBaru' AND id != '$userId'");
    if (mysqli_num_rows($cekEmail) > 0) {
        $pesanError = "Email tersebut sudah digunakan oleh akun lain!";
    } else {
        // Handle Upload Foto Profil jika ada file yang dipilih
        $updateFotoSql = "";
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ekstensiValid = ['jpg', 'jpeg', 'png', 'webp'];
            $namaFile = $_FILES['foto']['name'];
            $tmpName  = $_FILES['foto']['tmp_name'];
            $ekstensi = strtolower(pathinfo($namaFile, PATHINFO_EXTENSION));

            if (in_array($ekstensi, $ekstensiValid)) {
                $namaFileBaru = "admin_" . $userId . "_" . time() . "." . $ekstensi;
                $tujuanUpload = "../uploads/" . $namaFileBaru;

                if (!is_dir("../uploads")) {
                    mkdir("../uploads", 0777, true);
                }

                if (move_uploaded_file($tmpName, $tujuanUpload)) {
                    $updateFotoSql = ", foto = '$namaFileBaru'";
                } else {
                    $pesanError = "Gagal mengunggah foto profil ke server.";
                }
            } else {
                $pesanError = "Format foto harus JPG, JPEG, PNG, atau WEBP!";
            }
        }

        // Update Password jika diisi
        $updatePassSql = "";
        if (!empty($passBaru)) {
            if (strlen($passBaru) < 6) {
                $pesanError = "Password baru minimal 6 karakter!";
            } else {
                $hashedPass = password_hash($passBaru, PASSWORD_DEFAULT);
                $updatePassSql = ", password = '$hashedPass'";
            }
        }

        // Eksekusi Update ke Database jika tidak ada error
        if (empty($pesanError)) {
            $sqlUpdate = "UPDATE users SET nama = '$namaBaru', email = '$emailBaru' $updateFotoSql $updatePassSql WHERE id = '$userId'";
            if (mysqli_query($conn, $sqlUpdate)) {
                $_SESSION['nama'] = $namaBaru; // Perbarui nama di sesi aktif
                $pesanSukses = "Profil berhasil diperbarui!";
            } else {
                $pesanError = "Terjadi kesalahan saat menyimpan ke database.";
            }
        }
    }
}

// Ambil data terbaru user dari database
$queryUser = mysqli_query($conn, "SELECT * FROM users WHERE id = '$userId' LIMIT 1");
$user = mysqli_fetch_assoc($queryUser);

// Tentukan URL Foto (jika belum upload, pakai UI Avatars)
$fotoUrl = !empty($user['foto']) && file_exists("../uploads/" . $user['foto'])
    ? "../uploads/" . $user['foto']
    : "https://ui-avatars.com/api/?name=" . urlencode($user['nama']) . "&background=0284c7&color=fff&size=200";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Hak Akses - AeroSense IoT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        aside ::-webkit-scrollbar { width: 5px; }
        aside ::-webkit-scrollbar-track { background: transparent; }
        aside ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.3); border-radius: 10px; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 antialiased selection:bg-blue-500 selection:text-white">
    <div class="flex h-screen overflow-hidden">
        
        <!-- SIDEBAR -->
        <aside class="w-64 bg-slate-800 border-r border-slate-700/60 flex flex-col h-screen shrink-0 z-30">
            <div class="p-4 border-b border-slate-700/60 shrink-0">
                <a href="index.php" class="flex items-center gap-3 px-1.5 group">
                    <div class="w-9 h-9 rounded-xl shadow-lg shadow-cyan-500/20 shrink-0 bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center text-white font-bold">
                        <i class="fa-solid fa-cloud-bolt"></i>
                    </div>
                    <div>
                        <span class="font-bold text-base tracking-tight text-white block leading-none">AeroSense<span class="text-cyan-400">IoT</span></span>
                        <span class="text-[10px] text-slate-400 font-medium">Weather &amp; AI Telemetry</span>
                    </div>
                </a>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Monitoring &amp; Fitur</p>
                <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-700/50 transition">
                    <i class="fa-solid fa-cloud-sun w-5 text-center"></i>
                    <span class="text-sm">Dashboard Cuaca</span>
                </a>
                <a href="sensor.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-700/50 transition">
                    <i class="fa-solid fa-microchip w-5 text-center"></i>
                    <span class="text-sm">Sensor &amp; Hardware</span>
                </a>
                <a href="users.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-700/50 transition">
                    <i class="fa-solid fa-mobile-screen-button w-5 text-center"></i>
                    <span class="text-sm">User Aplikasi &amp; OTP</span>
                </a>
                <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mt-6 mb-2">Akun &amp; Sistem</p>
                <a href="profil.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-600/15 text-blue-400 font-medium border border-blue-500/25">
                    <i class="fa-regular fa-id-badge w-5 text-center"></i>
                    <span class="text-sm">Profil &amp; Hak Akses</span>
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700/60 shrink-0">
                <a href="../logout.php" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-xs font-medium text-rose-400 transition">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar Sesi Web</span>
                </a>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <!-- NAVBAR -->
            <header class="h-16 bg-slate-800/80 backdrop-blur-md border-b border-slate-700/60 flex items-center justify-between px-6 shrink-0">
                <div>
                    <h1 class="text-base font-semibold text-white">Profil &amp; Informasi Akun</h1>
                    <p class="text-xs text-slate-400">Kelola identitas akun dan kredensial keamanan Anda</p>
                </div>
                <a href="index.php" class="text-xs text-cyan-400 hover:text-cyan-300 flex items-center gap-1.5">
                    <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </header>

            <!-- BODY -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto space-y-6">

                    <!-- Alert Notifikasi -->
                    <?php if (!empty($pesanSukses)): ?>
                        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-medium flex items-center gap-2">
                            <i class="fa-solid fa-circle-check text-base"></i>
                            <span><?= $pesanSukses; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pesanError)): ?>
                        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-medium flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation text-base"></i>
                            <span><?= $pesanError; ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        
                        <!-- KARTU KIRI: INFO IDENTITAS & ID -->
                        <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl p-6 flex flex-col items-center text-center">
                            <div class="relative group mb-4">
                                <img id="previewAvatar" src="<?= $fotoUrl; ?>" alt="Foto Profil" class="w-28 h-28 rounded-2xl object-cover border-2 border-cyan-500/50 shadow-lg shadow-cyan-500/10">
                            </div>

                            <h2 class="text-lg font-bold text-white"><?= htmlspecialchars($user['nama']); ?></h2>
                            <p class="text-xs text-slate-400 mt-0.5"><?= htmlspecialchars($user['email']); ?></p>

                            <span class="mt-3 inline-flex items-center gap-1.5 text-[11px] font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1 rounded-full uppercase">
                                <i class="fa-solid fa-shield-halved"></i> <?= htmlspecialchars($user['role']); ?>
                            </span>

                            <!-- Detail Metadata ID -->
                            <div class="w-full mt-6 pt-5 border-t border-slate-700/60 space-y-3 text-left text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">ID Pengguna</span>
                                    <span class="font-mono font-bold text-cyan-400 bg-slate-900 px-2.5 py-1 rounded-lg border border-slate-700">
                                        #USR-<?= str_pad($user['id'], 4, '0', STR_PAD_LEFT); ?>
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Status Akun</span>
                                    <span class="text-emerald-400 font-medium flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Aktif
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-400">Terdaftar Sejak</span>
                                    <span class="text-slate-200 font-medium">
                                        <?= date('d M Y', strtotime($user['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- KARTU KANAN: FORM EDIT PROFIL -->
                        <div class="md:col-span-2 bg-slate-800/70 border border-slate-700/60 rounded-2xl p-6">
                            <h3 class="text-base font-semibold text-white mb-1">Edit Informasi Profil</h3>
                            <p class="text-xs text-slate-400 mb-5">Perbarui nama tampilan, foto profil, atau ganti kata sandi login Anda.</p>

                            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Ganti Foto Profil (Opsional)</label>
                                    <input type="file" name="foto" accept="image/*" onchange="previewImage(event)"
                                        class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-cyan-500/15 file:text-cyan-400 hover:file:bg-cyan-500/25 bg-slate-900/80 border border-slate-700/80 rounded-xl p-1.5 cursor-pointer">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-300 mb-1.5">Nama Lengkap</label>
                                        <input type="text" name="nama" required value="<?= htmlspecialchars($user['nama']); ?>"
                                            class="w-full bg-slate-900/90 border border-slate-700 focus:border-cyan-500 rounded-xl px-3.5 py-2.5 text-sm text-white outline-none transition">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-slate-300 mb-1.5">Alamat Email</label>
                                        <input type="email" name="email" required value="<?= htmlspecialchars($user['email']); ?>"
                                            class="w-full bg-slate-900/90 border border-slate-700 focus:border-cyan-500 rounded-xl px-3.5 py-2.5 text-sm text-white outline-none transition">
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-slate-700/60">
                                    <label class="block text-xs font-medium text-slate-300 mb-1.5">Password Baru <span class="text-slate-500 font-normal">(Kosongkan jika tidak ingin mengganti password)</span></label>
                                    <div class="relative">
                                        <input id="newPass" type="password" name="password_baru" placeholder="Masukkan password baru minimal 6 karakter..."
                                            class="w-full bg-slate-900/90 border border-slate-700 focus:border-cyan-500 rounded-xl px-3.5 py-2.5 pr-10 text-sm text-white outline-none transition">
                                        <button type="button" onclick="togglePass()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300">
                                            <i class="fa-regular fa-eye" id="eyeIcon"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="pt-3 flex justify-end">
                                    <button type="submit" class="bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-slate-950 font-semibold text-xs px-6 py-3 rounded-xl shadow-lg shadow-cyan-500/20 transition flex items-center gap-2">
                                        <i class="fa-solid fa-floppy-disk"></i>
                                        <span>Simpan Perubahan Profil</span>
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function(){
                document.getElementById('previewAvatar').src = reader.result;
            };
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function togglePass() {
            const inp = document.getElementById('newPass');
            const icon = document.getElementById('eyeIcon');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                inp.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>