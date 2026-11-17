<?php
session_start();
require 'koneksi.php';

// Ambil data sensor terbaru dari database
$querySensor = mysqli_query($conn, "SELECT * FROM sensor_logs ORDER BY id DESC LIMIT 1");
$dataSensor = mysqli_fetch_assoc($querySensor);

$suhu       = $dataSensor['suhu'] ?? '26.8';
$kelembaban = $dataSensor['kelembaban'] ?? '68';
$tekanan    = $dataSensor['tekanan'] ?? '1012';
$angin      = $dataSensor['angin'] ?? '14.2';
$baterai    = $dataSensor['baterai'] ?? '84';
$voltase    = $dataSensor['voltase'] ?? '4.08';
$mq         = $dataSensor['mq'] ?? '42';

$isLoggedIn = isset($_SESSION['login']);
$namaUser   = $_SESSION['nama'] ?? 'Tamu / User';
$roleUser   = $_SESSION['role'] ?? 'guest';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AeroSense IoT - Public Weather & Telemetry</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 antialiased selection:bg-cyan-500 selection:text-white">
    <div class="min-h-screen flex flex-col">
        <!-- HEADER / NAVBAR -->
        <header class="h-16 bg-slate-800/80 backdrop-blur-md border-b border-slate-700/60 flex items-center justify-between px-6 sticky top-0 z-20">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-sky-500 to-cyan-500 flex items-center justify-center text-white font-bold shadow-lg shadow-cyan-500/20">
                    <i class="fa-solid fa-cloud-bolt"></i>
                </div>
                <div>
                    <span class="font-bold text-base tracking-tight text-white block leading-none">AeroSense<span class="text-cyan-400">IoT</span></span>
                    <span class="text-[10px] text-slate-400 font-medium">Public Weather Telemetry (Android View)</span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <?php if ($isLoggedIn): ?>
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-semibold text-white"><?= htmlspecialchars($namaUser); ?></p>
                        <p class="text-[10px] text-cyan-400 uppercase"><?= $roleUser; ?></p>
                    </div>
                    <?php if ($roleUser === 'admin'): ?>
                        <a href="admin/index.php" class="bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-400 border border-cyan-500/30 text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                            Panel Admin
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-xs font-semibold px-3 py-1.5 rounded-xl transition">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="login.html" class="bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-semibold px-4 py-2 rounded-xl transition shadow-lg shadow-cyan-500/20">
                        Masuk / Login
                    </a>
                <?php endif; ?>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 p-6 max-w-6xl mx-auto w-full space-y-6">
            <!-- BANNER INFO -->
            <div class="bg-gradient-to-r from-slate-800 via-slate-800 to-slate-900 border border-slate-700/80 rounded-2xl p-6 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <span class="inline-block text-[10px] bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded-full font-semibold uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-circle text-[8px] mr-1 animate-pulse"></i> Live ESP32 Sensor Feed
                    </span>
                    <h2 class="text-xl font-bold text-white">Monitoring Stasiun Cuaca & Lingkungan</h2>
                    <p class="text-xs text-slate-400 mt-1">Data telemetri ini diperbarui secara otomatis dari mikrokontroler ESP32 dan dapat diakses publik via Web & Aplikasi Android.</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="text-xs text-slate-400">Waktu Server:</p>
                    <p class="text-sm font-mono font-bold text-cyan-400" id="liveClock">--:--:--</p>
                </div>
            </div>

            <!-- KARTU STATISTIK SENSOR -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-slate-800/70 border border-slate-700/60 rounded-2xl p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-400">Suhu / Kelembaban</p>
                        <div class="flex items-baseline gap-2 mt-1">
                            <h2 class="text-2xl font-bold text-white"><?= $suhu; ?>°C</h2>
                            <span class="text-xs font-medium text-cyan-400"><?= $kelembaban; ?>% RH</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Sensor DHT22 • Live DB</p>
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
                        <p class="text-[11px] text-emerald-400 mt-1">Sensor BMP280 • Stabil</p>
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
                        <p class="text-[11px] text-slate-400 mt-1">Anemometer</p>
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
                        <p class="text-[11px] text-slate-400 mt-1"><?= $voltase; ?>V</p>
                    </div>
                    <div class="w-11 h-11 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-battery-three-quarters"></i>
                    </div>
                </div>
            </div>
        </main>

        <!-- FOOTER -->
        <footer class="py-4 text-center text-xs text-slate-500 border-t border-slate-800">
            AeroSense IoT &copy; 2026 • Stasiun Cuaca & Telemetri Berbasis ESP32
        </footer>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            const timeString = now.toTimeString().split(' ')[0];
            const clockEl = document.getElementById('liveClock');
            if (clockEl) clockEl.innerText = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>