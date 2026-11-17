<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Portal - AeroSense IoT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Decorative Ambient Glow -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-blue-600/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-xl border border-slate-800 rounded-3xl p-8 shadow-2xl relative z-10">
        <!-- Brand Logo Header -->
        <div class="flex flex-col items-center text-center mb-6">
            <div class="w-16 h-16 rounded-2xl shadow-xl shadow-cyan-500/25 mb-4 p-0.5 bg-gradient-to-br from-cyan-400/40 to-blue-600/40">
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                    <defs>
                        <linearGradient id="logoGrad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                            <stop offset="0%" stop-color="#0ea5e9"/>
                            <stop offset="100%" stop-color="#06b6d4"/>
                        </linearGradient>
                    </defs>
                    <rect width="40" height="40" rx="10" fill="url(#logoGrad)"/>
                    <circle cx="25" cy="14" r="5" fill="#fde047" fill-opacity="0.9"/>
                    <path d="M13.5 27.5H26.5C28.9853 27.5 31 25.4853 31 23C31 20.6642 29.2202 18.7443 26.9403 18.5218C26.258 15.3679 23.4535 13 20.1 13C16.234 13 13.1 16.134 13.1 20C13.1 20.2215 13.1103 20.4406 13.1304 20.6567C11.3526 21.1823 10 22.8305 10 24.75C10 26.2688 11.567 27.5 13.5 27.5Z" fill="white"/>
                    <circle cx="20.5" cy="23" r="1.8" fill="#0284c7"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">AeroSense<span class="text-cyan-400">IoT</span></h1>
            <p id="subtitleText" class="text-xs text-slate-400 mt-1">Web Console Khusus Owner &amp; Admin</p>
            <span id="roleBadge" class="mt-2.5 inline-flex items-center gap-1.5 text-[11px] font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1 rounded-full transition-all">
                <i class="fa-solid fa-shield-halved"></i> Full Access • 2 Projek Terhubung
            </span>
        </div>

        <!-- Role Switcher Tab (Admin vs User) -->
        <div class="grid grid-cols-2 gap-1.5 bg-slate-950 p-1 rounded-xl border border-slate-800 mb-5">
            <button type="button" id="tabAdmin" onclick="switchRole('admin')"
                class="py-2 text-xs font-semibold rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 text-slate-950 shadow transition">
                <i class="fa-solid fa-user-shield mr-1"></i> Admin / Owner
            </button>
            <button type="button" id="tabUser" onclick="switchRole('user')"
                class="py-2 text-xs font-medium rounded-lg text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-user mr-1"></i> User Monitoring
            </button>
        </div>

        <!-- Alert Notifikasi Error / Sukses -->
        <div id="alertBox" class="hidden mb-4 p-3 rounded-xl text-xs font-medium border"></div>

        <!-- Login Form -->
        <form onsubmit="handleLogin(event)" class="space-y-4">
            <input type="hidden" id="selectedRole" value="admin">

            <div>
                <label id="emailLabel" class="block text-xs font-medium text-slate-300 mb-1.5">Email Admin</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-regular fa-envelope"></i>
                    </span>
                    <input id="emailInput" type="email" required value="admin@aerosense.id" placeholder="admin@aerosense.id"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl pl-10 pr-4 py-2.5 text-sm text-white outline-none transition">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-medium text-slate-300">Password</label>
                    <a href="#" class="text-[11px] text-cyan-400 hover:underline">Lupa sandi?</a>
                </div>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-500">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input id="passwordInput" type="password" required value="admin12345" placeholder="••••••••"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 rounded-xl pl-10 pr-10 py-2.5 text-sm text-white outline-none transition">
                    <button type="button" onclick="togglePass()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300">
                        <i class="fa-regular fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 text-xs text-slate-400 cursor-pointer">
                    <input type="checkbox" id="rememberMe" checked class="rounded bg-slate-900 border-slate-700 accent-cyan-500">
                    <span>Ingat sesi login web ini</span>
                </label>
            </div>

            <button id="submitBtn" type="submit" class="w-full bg-gradient-to-r from-sky-500 to-cyan-500 hover:from-sky-400 hover:to-cyan-400 text-slate-950 font-semibold text-sm py-3 rounded-xl shadow-lg shadow-cyan-500/20 transition flex items-center justify-center gap-2 mt-2">
                <span id="btnText">Masuk ke Dashboard Admin</span>
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </form>

        <!-- Footer Info -->
        <div class="mt-6 pt-5 border-t border-slate-800/80 text-center space-y-2">
            <p class="text-[11px] text-slate-400">
                <i class="fa-brands fa-android text-emerald-400 mr-1"></i>
                Pengguna umum juga dapat memantau cuaca melalui <b>Aplikasi Android</b>.
            </p>
        </div>
    </div>

    <script>
        function switchRole(role) {
            const tabAdmin = document.getElementById('tabAdmin');
            const tabUser = document.getElementById('tabUser');
            const selectedRole = document.getElementById('selectedRole');
            const subtitleText = document.getElementById('subtitleText');
            const roleBadge = document.getElementById('roleBadge');
            const emailLabel = document.getElementById('emailLabel');
            const emailInput = document.getElementById('emailInput');
            const passwordInput = document.getElementById('passwordInput');
            const btnText = document.getElementById('btnText');
            const alertBox = document.getElementById('alertBox');

            alertBox.classList.add('hidden');
            selectedRole.value = role;

            if (role === 'admin') {
                tabAdmin.className = "py-2 text-xs font-semibold rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 text-slate-950 shadow transition";
                tabUser.className = "py-2 text-xs font-medium rounded-lg text-slate-400 hover:text-white transition";
                subtitleText.innerText = "Web Console Khusus Owner & Admin";
                roleBadge.className = "mt-2.5 inline-flex items-center gap-1.5 text-[11px] font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20 px-3 py-1 rounded-full transition-all";
                roleBadge.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Full Access • 2 Projek Terhubung';
                emailLabel.innerText = "Email Admin";
                emailInput.value = "admin@aerosense.id";
                emailInput.placeholder = "admin@aerosense.id";
                passwordInput.value = "admin12345";
                btnText.innerText = "Masuk ke Dashboard Admin";
            } else {
                tabUser.className = "py-2 text-xs font-semibold rounded-lg bg-gradient-to-r from-sky-500 to-cyan-500 text-slate-950 shadow transition";
                tabAdmin.className = "py-2 text-xs font-medium rounded-lg text-slate-400 hover:text-white transition";
                subtitleText.innerText = "Portal Pemantauan Cuaca Pengguna";
                roleBadge.className = "mt-2.5 inline-flex items-center gap-1.5 text-[11px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded-full transition-all";
                roleBadge.innerHTML = '<i class="fa-solid fa-cloud-sun"></i> Live Weather Monitoring';
                emailLabel.innerText = "Email Pengguna";
                emailInput.value = "";
                emailInput.placeholder = "user@email.com";
                passwordInput.value = "";
                btnText.innerText = "Masuk ke Portal User";
            }
        }

        function togglePass() {
            const inp = document.getElementById('passwordInput');
            const icon = document.getElementById('eyeIcon');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                inp.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

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

        async function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('emailInput').value;
            const password = document.getElementById('passwordInput').value;
            const role = document.getElementById('selectedRole').value;
            const submitBtn = document.getElementById('submitBtn');

            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70');

            try {
                const response = await fetch('proses_login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password, role })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    showAlert('Login berhasil! Mengalihkan...', 'success');
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 800);
                } else {
                    showAlert(result.message, 'error');
                }
            } catch (error) {
                showAlert('Gagal terhubung ke server PHP. Pastikan XAMPP/Laragon aktif.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-70');
            }
        }
    </script>
</body>
</html>