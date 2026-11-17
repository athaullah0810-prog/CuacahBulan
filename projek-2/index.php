<?php
session_start();
require 'koneksi.php';

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
    <title>AeroSense IoT - Public View</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 text-slate-100 antialiased">
    <div class="min-h-screen flex flex-col p-6 max-w-4xl mx-auto space-y-6">
        <header class="bg-slate-800 p-4 rounded-2xl border border-slate-700 flex justify-between items-center">
            <h1 class="font-bold text-white">AeroSense IoT (Mobile)</h1>
            <a href="admin/index.php" class="bg-cyan-500/20 text-cyan-400 px-3 py-1.5 rounded-xl text-xs font-semibold">Admin</a>
        </header>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 flex justify-between items-center">
                <div>
                    <p class="text-xs text-slate-400">Suhu / Kelembaban</p>
                    <h2 class="text-2xl font-bold text-white mt-1"><?= $suhu; ?>°C <span class="text-cyan-400 text-sm"><?= $kelembaban; ?>%</span></h2>
                </div>
                <i class="fa-solid fa-temperature-half text-amber-400 text-2xl"></i>
            </div>
            <div class="bg-slate-800 p-5 rounded-2xl border border-slate-700 flex justify-between items-center">
                <div>
                    <p class="text-xs text-slate-400">Baterai &amp; Solar</p>
                    <h2 class="text-2xl font-bold text-white mt-1"><?= $baterai; ?>% <span class="text-emerald-400 text-sm"><?= $voltase; ?>V</span></h2>
                </div>
                <i class="fa-solid fa-battery-three-quarters text-emerald-400 text-2xl"></i>
            </div>
        </div>
    </div>
</body>
</html>