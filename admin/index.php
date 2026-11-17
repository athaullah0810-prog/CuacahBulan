<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

$adminId = $_SESSION['user_id'] ?? 0;
$queryAdmin = mysqli_query($conn, "SELECT * FROM users WHERE id = '$adminId' LIMIT 1");
$adminData = mysqli_fetch_assoc($queryAdmin);

$namaAdmin  = $adminData['nama'] ?? $_SESSION['nama'] ?? 'Owner Admin';
$emailAdmin = $adminData['email'] ?? 'admin@aerosense.id';
$namaFileFoto = $adminData['foto'] ?? '';
$pathFisik    = "../uploads/" . $namaFileFoto;
$adaFoto      = !empty($namaFileFoto) && file_exists($pathFisik);
$inisial      = strtoupper(substr($namaAdmin, 0, 1));

$querySensor = mysqli_query($conn, "SELECT * FROM sensor_logs ORDER BY id DESC LIMIT 1");
$dataSensor = mysqli_fetch_assoc($querySensor);

$suhu       = $dataSensor['suhu'] ?? '26.8';
$kelembaban = $dataSensor['kelembaban'] ?? '68';
$tekanan    = $dataSensor['tekanan'] ?? '1012';
$angin      = $dataSensor['angin'] ?? '14.2';
$baterai    = $dataSensor['baterai'] ?? '84';
$voltase    = $dataSensor['voltase'] ?? '4.08';
$mq         = $dataSensor['mq'] ?? '42';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistik - AeroSense IoT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 antialiased selection:bg-blue-500 selection:text-white">
    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="w-64 bg-slate-800 border-r border-slate-700/60 flex flex-col h-screen shrink-0">
            <div class="p-4 border-b border-slate-700/60 space-y-3 shrink-0">
                <a href="index.php" class="flex items-center gap-3 px-1.5 group">
                    <div class="w-9 h-9 rounded-xl shadow-lg shadow-cyan-500/20 bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center text-white font-bold">
                        <i class="fa-solid fa-cloud-bolt"></i>
                    </div>
                    <div>
                        <span class="font-bold text-base tracking-tight text-white block leading-none">AeroSense<span class="text-cyan-400">IoT</span></span>
                        <span class="text-[10px] text-slate-400 font-medium">Weather &amp; AI Telemetry</span>
                    </div>
                </a>
            </div>
            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto">
                <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Monitoring</p>
                <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-600/15 text-blue-400 font-medium border border-blue-500/25">
                    <i class="fa-solid fa-cloud-sun w-5 text-center"></i>
                    <span class="text-sm">Dashboard Statistik</span>
                </a>
                <a href="profil.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-slate-100 hover:bg-slate-700/50 transition">
                    <i class="fa-regular fa-id-badge w-5 text-center"></i>
                    <span class="text-sm">Profil &amp; Hak Akses</span>
                </a>
            </nav>
            <div class="p-4 border-t border-slate-700/60 shrink-0 bg-slate-800">
                <div class="bg-slate-900/70 rounded-xl p-3 border border-slate-700/50 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium text-slate-200">ESP32-WROOM v1</span>
                        <span class="text-[10px] text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded">Online</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 border-t border-slate-800">
                        <span><i class="fa-regular fa-clock mr-1 text-cyan-400"></i><span id="rtcClock">--:--:--</span></span>
                        <span><i class="fa-solid fa-display mr-1 text-blue-400"></i>LCD OK</span>
                    </div>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-hidden">
            <header class="h-16 bg-slate-800/80 backdrop-blur-md border-b border-slate-700/60 flex items-center justify-between px-6 shrink-0 z-20">
                <h1 class="text-base font-semibold text-white">Dashboard Statistik Cuaca</h1>
                <div class="relative" id="profileDropdownContainer">
                    <button id="profileDropdownBtn" class="flex items-center gap-2.5 pl-2 pr-3 py-1.5 rounded-full bg-slate-700/50 hover:bg-slate-700 border border-slate-600/60 transition focus:outline-none">
                        <?php if ($adaFoto): ?>
                            <img src="<?= $pathFisik; ?>" alt="Admin" class="w-7 h-7 rounded-full object-cover">
                        <?php else: ?>
                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold shadow">
                                <?= $inisial; ?>
                            </div>
                        <?php endif; ?>
                        <span class="text-xs font-semibold text-white"><?= htmlspecialchars($namaAdmin); ?></span>
                        <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200" id="dropdownArrow"></i>
                    </button>
                    <div id="profileDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-slate-800 rounded-xl shadow-2xl border border-slate-700 py-1.5 z-50">
                        <div class="px-4 py-2.5 border-b border-slate-700/80">
                            <p class="text-sm font-semibold text-white"><?= htmlspecialchars($namaAdmin); ?></p>
                            <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars($emailAdmin); ?></p>
                        </div>
                        <a href="profil.php" class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-300 hover:bg-slate-700/60">Profil</a>
                        <a href="../logout.php" class="flex items-center gap-2.5 px-4 py-2 text-sm text-rose-400 hover:bg-rose-500/10">Keluar</a>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-6 bg-slate-900">
                <div class="max-w-6xl mx-auto space-y-6">
                    <div class="bg-slate-800 border border-slate-700 rounded-2xl p-5 flex items-center justify-between shadow-xl">
                        <div class="flex items-center gap-4">
                            <?php if ($adaFoto): ?>
                                <img src="<?= $pathFisik; ?>" alt="Foto" class="w-14 h-14 rounded-2xl object-cover border-2 border-cyan-500">
                            <?php else: ?>
                                <div class="w-14 h-14 rounded-2xl bg-cyan-500 flex items-center justify-center text-white text-xl font-bold">
                                    <?= $inisial; ?>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h2 class="text-base font-bold text-white">Selamat Datang, <?= htmlspecialchars($namaAdmin); ?>! 👋</h2>
                                <p class="text-xs text-slate-400"><?= htmlspecialchars($emailAdmin); ?> • Sistem telemetri aktif.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl p-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-slate-400">Suhu / Kelembaban</p>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <h2 class="text-2xl font-bold text-white"><?= $suhu; ?>°C</h2>
                                    <span class="text-xs font-medium text-cyan-400"><?= $kelembaban; ?>% RH</span>
                                </div>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-temperature-half"></i>
                            </div>
                        </div>

                        <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl p-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-slate-400">Tekanan Udara</p>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <h2 class="text-2xl font-bold text-white"><?= $tekanan; ?> <span class="text-sm font-normal text-slate-400">hPa</span></h2>
                                </div>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-gauge-high"></i>
                            </div>
                        </div>

                        <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl p-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-slate-400">Kecepatan Angin</p>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <h2 class="text-2xl font-bold text-white"><?= $angin; ?> <span class="text-sm font-normal text-slate-400">km/j</span></h2>
                                </div>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-wind"></i>
                            </div>
                        </div>

                        <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl p-5 flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-slate-400">Kapasitas Baterai</p>
                                <div class="flex items-baseline gap-2 mt-1">
                                    <h2 class="text-2xl font-bold text-white"><?= $baterai; ?>%</h2>
                                    <span class="text-[11px] text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded">+Solar</span>
                                </div>
                            </div>
                            <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-battery-three-quarters"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const profileBtn = document.getElementById('profileDropdownBtn');
        const profileMenu = document.getElementById('profileDropdownMenu');
        const dropdownArrow = document.getElementById('dropdownArrow');

        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
            dropdownArrow.classList.toggle('rotate-180');
        });

        window.addEventListener('click', () => {
            if (!profileMenu.classList.contains('hidden')) {
                profileMenu.classList.add('hidden');
                dropdownArrow.classList.remove('rotate-180');
            }
        });

        function updateClock() {
            const now = new Date();
            const timeString = now.toTimeString().split(' ')[0];
            const clockEl = document.getElementById('rtcClock');
            if (clockEl) clockEl.innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>