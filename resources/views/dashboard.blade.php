<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Koperasi</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            /* Latar belakang gradien mirip dengan gambar */
            background: linear-gradient(135deg, #e4e6f1 0%, #e9e0eb 100%);
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* Animasi muncul untuk ikon */
        @keyframes popIn {
            0% { opacity: 0; transform: scale(0.8) translateY(10px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        .module-card {
            animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
            opacity: 0;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="flex flex-col">

    <!-- Navbar / Header -->
    <header class="w-full px-6 py-4 flex justify-between items-center bg-transparent relative z-10">
        <div class="flex items-center gap-3 cursor-pointer">
            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                <i class="fas fa-handshake text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800 leading-tight">Koperasi Karyawan OPI</h1>
                <p class="text-xs text-gray-500 font-medium">Sistem Informasi Terpadu</p>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <button class="w-10 h-10 rounded-full bg-white/50 hover:bg-white text-gray-600 shadow-sm hover:shadow-md transition-all flex items-center justify-center">
                <i class="fas fa-bell"></i>
            </button>
            <div class="flex items-center gap-2 cursor-pointer bg-white/50 hover:bg-white px-3 py-1.5 rounded-full shadow-sm hover:shadow-md transition-all">
                <img src="https://ui-avatars.com/api/?name=Admin+Koperasi&background=4f46e5&color=fff" alt="User Profile" class="w-8 h-8 rounded-full">
                <span class="text-sm font-semibold text-gray-700 hidden sm:block">Admin</span>
                <i class="fas fa-chevron-down text-xs text-gray-500 ml-1"></i>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow flex flex-col items-center justify-center px-4 py-10 relative z-10 w-full max-w-6xl mx-auto">
        
        <!-- Pesan Notifikasi (Tersembunyi secara default) -->
        <div id="toast" class="fixed top-20 right-5 bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300 flex items-center gap-3 z-50">
            <i class="fas fa-spinner fa-spin"></i>
            <span id="toast-msg">Membuka modul...</span>
        </div>

        <div class="mb-12 text-center">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Selamat Datang!</h2>
            <p class="text-gray-600">Pilih modul koperasi yang ingin Anda akses.</p>
        </div>

        <!-- Grid Container -->
        <div id="module-grid" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-x-6 gap-y-10 justify-items-center w-full">
            <!-- Modul akan di-render di sini oleh JavaScript -->
        </div>

    </main>

    <!-- Dekorasi Background Latar -->
    <div class="fixed top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-200/40 rounded-full blur-3xl z-0 pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-purple-200/40 rounded-full blur-3xl z-0 pointer-events-none"></div>

    <script>
        // Data Modul-modul Koperasi
        const modules = [
            { id: 'dashboard', name: 'Dashboard', icon: 'fas fa-th-large', color: 'text-purple-600', bgColor: 'bg-purple-100' },
            { id: 'keanggotaan', name: 'Keanggotaan', icon: 'fas fa-users', color: 'text-blue-500', bgColor: 'bg-blue-100', url: '/anggota' },
            { id: 'simpanan', name: 'Simpanan', icon: 'fas fa-piggy-bank', color: 'text-pink-500', bgColor: 'bg-pink-100' },
            { id: 'pinjaman', name: 'Pinjaman', icon: 'fas fa-hand-holding-dollar', color: 'text-green-500', bgColor: 'bg-green-100' },
            { id: 'akuntansi', name: 'Akuntansi', icon: 'fas fa-calculator', color: 'text-orange-500', bgColor: 'bg-orange-100' },
            { id: 'kasir', name: 'Kasir / POS', icon: 'fas fa-cash-register', color: 'text-teal-500', bgColor: 'bg-teal-100' },
            { id: 'persetujuan', name: 'Persetujuan', icon: 'fas fa-check-double', color: 'text-indigo-500', bgColor: 'bg-indigo-100' },
            { id: 'dokumen', name: 'Dokumen', icon: 'fas fa-folder-open', color: 'text-yellow-500', bgColor: 'bg-yellow-100' },
            { id: 'laporan', name: 'Laporan', icon: 'fas fa-chart-pie', color: 'text-red-500', bgColor: 'bg-red-100' },
            { id: 'rapat', name: 'Rapat', icon: 'fas fa-calendar-check', color: 'text-blue-600', bgColor: 'bg-blue-100' },
            { id: 'diskusi', name: 'Diskusi', icon: 'fas fa-comments', color: 'text-amber-500', bgColor: 'bg-amber-100' },
            { id: 'pengaturan', name: 'Pengaturan', icon: 'fas fa-cog', color: 'text-gray-600', bgColor: 'bg-gray-100' }
        ];

        const gridContainer = document.getElementById('module-grid');
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-msg');

        // Fungsi untuk merender modul ke dalam HTML
        function renderModules() {
            modules.forEach((mod, index) => {
                // Menghitung delay animasi agar munculnya bergantian
                const animationDelay = index * 0.05;

                const cardHTML = `
                    <div class="module-card flex flex-col items-center justify-start group cursor-pointer w-24" style="animation-delay: ${animationDelay}s" onclick="openModule('${mod.name}', '${mod.url || ''}')">
                        <!-- Kotak Ikon -->
                        <div class="w-[84px] h-[84px] bg-white rounded-[22px] shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition-all duration-300 flex items-center justify-center relative overflow-hidden">
                            <!-- Efek hover background subtle -->
                            <div class="absolute inset-0 opacity-0 group-hover:opacity-20 transition-opacity duration-300 ${mod.bgColor}"></div>
                            
                            <!-- Ikon -->
                            <i class="${mod.icon} text-4xl ${mod.color} relative z-10"></i>
                        </div>
                        <!-- Teks Label -->
                        <span class="mt-3 text-[13px] font-medium text-gray-700 group-hover:text-gray-900 text-center leading-tight whitespace-nowrap">
                            ${mod.name}
                        </span>
                    </div>
                `;
                gridContainer.insertAdjacentHTML('beforeend', cardHTML);
            });
        }

        // Fungsi membuka modul
        function openModule(moduleName, url) {
            // Tampilkan Toast
            toastMsg.innerText = `Memuat modul ${moduleName}...`;
            toast.classList.remove('translate-x-full', 'opacity-0');

            if (url) {
                // Redirect ke URL modul setelah sebentar
                setTimeout(() => {
                    window.location.href = url;
                }, 400);
            } else {
                // Sembunyikan Toast setelah 2 detik jika belum ada URL
                setTimeout(() => {
                    toast.classList.add('translate-x-full', 'opacity-0');
                }, 2000);
            }
        }

        // Jalankan render saat halaman dimuat
        document.addEventListener('DOMContentLoaded', renderModules);
    </script>
</body>
</html>