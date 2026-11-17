<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User - AeroSense IoT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Decorative Ambient Glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-cyan-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl relative z-10">
        
        <!-- Brand Header -->
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-2xl shadow-xl shadow-emerald-500/25 mb-4 p-0.5 bg-gradient-to-br from-emerald-400/40 to-cyan-600/40">
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <rect width="40" height="40" rx="10" fill="#059669"/>
                    <circle cx="25" cy="14" r="5" fill="#fde047" fill-opacity="0.9"/>
                    <path d="M13.5 27.5H26.5C28.9853 27.5 31 25.4853 31 23C31 20.6642 29.2202 18.7443 26.9403 18.5218C26.258 15.3679 23.4535 13 20.1 13C16.234 13 13.1 16.134 13.1 20C13.1 20.2215 13.1103 20.4406 13.1304 20.6567C11.3526 21.1823 10 22.8305 10 24.75C10 26.2688 11.567 27.5 13.5 27.5Z" fill="white"/>
                    <circle cx="20.5" cy="23" r="1.8" fill="#047857"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">AeroSense<span class="text-emerald-400">IoT</span></h1>
            <p class="text-xs text-slate-400 mt-1">Pendaftaran Akun Pengguna Monitoring</p>
            <span class="mt-2.5 inline-flex items-center gap-1.5 text-[11px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded-full">
                <i class="fa-solid fa-user-plus"></i> Verifikasi OTP Diperlukan
            </span>
        </div>

        <!-- Alert Notifikasi -->
        <div id="alertBox" class="hidden mb-4 p-3 rounded-xl text-xs font-medium border"></div>

        <!-- STEP 1: FORM PENDAFTARAN -->
        <form id="formRegister" onsubmit="handleRequestOTP(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Nama Lengkap</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-regular fa-user"></i>
                    </span>
                    <input id="regNama" type="text" required placeholder="Nama Lengkap Anda"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Email Aktif</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input id="regEmail" type="email" required placeholder="user@email.com"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white outline-none transition">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input id="regPassword" type="password" required placeholder="••••••••"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white outline-none transition">
                </div>
            </div>

            <button type="submit" id="btnReqOTP" class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-semibold text-sm py-3 rounded-xl shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-2 mt-2">
                <span>Kirim Kode OTP</span>
                <i class="fa-solid fa-paper-plane"></i>
            </button>
        </form>

        <!-- STEP 2: FORM VERIFIKASI OTP (Hidden Default) -->
        <form id="formOTP" onsubmit="handleVerifyOTP(event)" class="space-y-4 hidden">
            <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl text-center mb-3">
                <p class="text-xs text-emerald-300">Kode OTP simulasi telah dikirim ke email:</p>
                <p id="sentEmailTarget" class="text-xs font-bold text-white mt-0.5"></p>
                <!-- Untuk kemudahan testing lokal, OTP ditampilkan langsung di sini -->
                <p id="devOtpInfo" class="text-[11px] text-amber-400 mt-2 font-mono bg-slate-950/60 py-1 rounded"></p>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-300 mb-1.5 text-center">Masukkan 6 Digit Kode OTP</label>
                <input id="otpCode" type="text" maxlength="6" required placeholder="123456"
                    class="w-full bg-slate-950 border border-slate-800 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 rounded-xl px-4 py-3 text-center text-xl tracking-widest font-bold text-white outline-none transition">
            </div>

            <button type="submit" id="btnVerify" class="w-full bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-semibold text-sm py-3 rounded-xl shadow-lg shadow-emerald-500/20 transition flex items-center justify-center gap-2">
                <span>Verifikasi & Buat Akun</span>
                <i class="fa-solid fa-circle-check"></i>
            </button>

            <div class="text-center">
                <button type="button" onclick="resetForm()" class="text-xs text-slate-400 hover:text-emerald-400 transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali / Ubah Data
                </button>
            </div>
        </form>

        <!-- Footer Link ke Login -->
        <div class="mt-6 pt-5 border-t border-slate-800/80 text-center">
            <p class="text-xs text-slate-400">
                Sudah punya akun? <a href="login.php" class="text-emerald-400 font-semibold hover:underline">Login di sini</a>
            </p>
        </div>
    </div>

    <script>
        let tempData = {};

        function showAlert(message, type = 'error') {
            const alertBox = document.getElementById('alertBox');
            alertBox.classList.remove('hidden', 'bg-red-500/10', 'text-red-400', 'border-red-500/30', 'bg-emerald-500/10', 'text-emerald-400', 'border-emerald-500/30');
            
            if (type === 'error') {
                alertBox.classList.add('bg-red-500/10', 'text-red-400', 'border-red-500/30');
                alertBox.innerHTML = `<i class="fa-solid fa-circle-exclamation mr-1.5"></i> ${message}`;
            } else {
                alertBox.classList.add('bg-emerald-500/10', 'text-emerald-400', 'border-emerald-500/30');
                alertBox.innerHTML = `<i class="fa-solid fa-circle-check mr-1.5"></i> ${message}`;
            }
        }

        async function handleRequestOTP(e) {
            e.preventDefault();
            const nama = document.getElementById('regNama').value;
            const email = document.getElementById('regEmail').value;
            const password = document.getElementById('regPassword').value;
            const btn = document.getElementById('btnReqOTP');

            btn.disabled = true;
            btn.classList.add('opacity-70');

            try {
                const res = await fetch('proses_register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'request_otp', nama, email, password })
                });
                const result = await res.json();

                if (result.status === 'success') {
                    tempData = { nama, email, password };
                    document.getElementById('sentEmailTarget').innerText = email;
                    document.getElementById('devOtpInfo').innerHTML = `(Kode Uji Coba Anda: <b>${result.otp_debug}</b>)`;
                    
                    // Transisi ke Form OTP
                    document.getElementById('formRegister').classList.add('hidden');
                    document.getElementById('formOTP').classList.remove('hidden');
                    document.getElementById('alertBox').classList.add('hidden');
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (err) {
                showAlert('Gagal terhubung ke server.', 'error');
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-70');
            }
        }

        async function handleVerifyOTP(e) {
            e.preventDefault();
            const otp = document.getElementById('otpCode').value;
            const btn = document.getElementById('btnVerify');

            btn.disabled = true;
            btn.classList.add('opacity-70');

            try {
                const res = await fetch('proses_register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        action: 'verify_otp', 
                        email: tempData.email, 
                        nama: tempData.nama, 
                        password: tempData.password, 
                        otp: otp 
                    })
                });
                const result = await res.json();

                if (result.status === 'success') {
                    showAlert('Registrasi berhasil! Mengalihkan ke login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (err) {
                showAlert('Gagal memverifikasi OTP.', 'error');
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-70');
            }
        }

        function resetForm() {
            document.getElementById('formOTP').classList.add('hidden');
            document.getElementById('formRegister').classList.remove('hidden');
            document.getElementById('alertBox').classList.add('hidden');
        }
    </script>
</body>
</html>