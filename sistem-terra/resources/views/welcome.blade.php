<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terra - AI Smart Farming</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .elegant-shadow { box-shadow: 0 10px 30px -10px rgba(109, 40, 217, 0.2); }
        .text-gradient-soft-purple {
            background: linear-gradient(135deg, #7C3AED 0%, #A855F7 100%); /* Ungu ke Ungu lebih terang */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .bg-gradient-button {
            background: linear-gradient(135deg, #8B5CF6 0%, #C084FC 100%); /* Ungu ke Lavender */
        }
    </style>
</head>
<body class="antialiased bg-white text-gray-800">

    <nav x-data="{ open: false }" class="fixed w-full z-50 bg-white/80 backdrop-blur-lg border-b border-gray-100 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex-shrink-0 flex items-center gap-3">
                    <img src="{{ asset('img/logo.png') }}" alt="Terra Logo" class="h-10 w-auto">
                    <span class="font-extrabold text-2xl tracking-tight text-gray-900">Terra.</span>
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#features" class="text-sm font-semibold text-gray-600 hover:text-purple-700 transition">Fitur</a>
                    <a href="#team" class="text-sm font-semibold text-gray-600 hover:text-purple-700 transition">Tim Kami</a>
                    <a href="#faq" class="text-sm font-semibold text-gray-600 hover:text-purple-700 transition">FAQ</a>
                    @if (Route::has('login'))
                        <div class="ml-4 flex items-center gap-4">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="text-sm font-bold bg-purple-100 text-purple-700 px-5 py-2.5 rounded-full hover:bg-purple-200 transition">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="text-sm font-bold text-gray-800 hover:text-purple-700 transition">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="text-sm font-bold bg-gradient-button text-white px-6 py-2.5 rounded-full shadow-lg shadow-purple-200 hover:shadow-xl hover:shadow-purple-300 transition-all transform hover:-translate-y-0.5">Daftar Sekarang</a>
                                @endif
                            @endauth
                        </div>
                    @endif
                </div>

                <div class="-mr-2 flex md:hidden">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /><path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </div>
        </div>
         <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden bg-white border-b shadow-lg">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#features" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-purple-700 hover:bg-purple-50">Fitur</a>
                <a href="#team" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-purple-700 hover:bg-purple-50">Tim Kami</a>
                <a href="#faq" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-purple-700 hover:bg-purple-50">FAQ</a>
            </div>
        </div>
    </nav>

    <section class="relative pt-32 pb-20 md:pt-40 md:pb-28 overflow-hidden bg-[#FBF7FF]">
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[800px] bg-purple-200/30 rounded-full blur-3xl -z-10 pointer-events-none animate-blob animation-delay-4000"></div>
        <div class="absolute bottom-0 right-1/4 w-[600px] h-[600px] bg-pink-200/30 rounded-full blur-3xl -z-10 pointer-events-none animate-blob animation-delay-2000"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-gray-900 mb-6 leading-tight">
                Panen Melimpah, <br class="hidden md:block"> 
                <span class="text-gradient-soft-purple">Tanaman Sehat.</span>
            </h1>
            
            <p class="text-xl md:text-2xl text-gray-700 font-medium mb-4 max-w-3xl mx-auto">
                Deteksi Penyakit Terung Sebelum Terlambat
            </p>
            
            <p class="text-base md:text-lg text-gray-500 max-w-2xl mx-auto leading-relaxed mb-10">
                Monitoring lahan secara otomatis dengan robot berbasis AI. Identifikasi penyakit daun terung secara real-time untuk tindakan yang lebih cepat dan presisi.
            </p>

            <div class="flex justify-center">
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 text-lg font-bold text-white transition-all duration-200 bg-gradient-button border border-transparent rounded-full hover:scale-105 shadow-lg shadow-purple-200 hover:shadow-xl hover:shadow-purple-300">
                    Mulai Sekarang — Gratis
                </a>
            </div>
        </div>

        <div class="mt-16 max-w-5xl mx-auto px-4 relative">
            <div class="bg-white/60 backdrop-blur-xl p-4 rounded-2xl border border-purple-100 elegant-shadow">
                 <img src="{{ asset('img/dashboard.png') }}" alt="App screenshot" class="rounded-xl shadow-sm w-full border border-purple-50">
            </div>
        </div>
    </section>

    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-sm font-bold text-purple-700 uppercase tracking-widest mb-3">Fitur Unggulan</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900">Teknologi Canggih untuk Petani Modern</h3>
            </div>

            <div class="space-y-24">
                <div class="flex flex-col md:flex-row items-center gap-12">
                    <div class="md:w-1/2">
                        <div class="bg-purple-50 rounded-3xl p-8 elegant-shadow border border-purple-100">
                            <img src="{{ asset('img/robot.png') }}" alt="Robot AI" class="rounded-2xl w-full object-cover shadow-sm">
                        </div>
                    </div>
                    <div class="md:w-1/2 md:pl-10">
                        <div class="h-12 w-12 bg-purple-700 rounded-xl flex items-center justify-center mb-6 shadow-lg shadow-purple-200">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-4">Robot Deteksi Berbasis AI</h4>
                        <p class="text-lg text-gray-600 leading-relaxed mb-6">
                            Robot otonom kami menjelajahi lahan Anda, memindai setiap daun terung dengan Computer Vision canggih. Mampu membedakan daun sehat, bercak daun, dan layu bakteri dengan akurasi tinggi.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row-reverse items-center gap-12">
                     <div class="md:w-1/2">
                        <div class="bg-purple-50 rounded-3xl p-8 elegant-shadow border border-purple-100">
                             <img src="{{ asset('img/sensor.png') }}" alt="IoT Dashboard" class="rounded-2xl w-full object-cover shadow-sm">
                        </div>
                    </div>
                    <div class="md:w-1/2 md:pr-10">
                        <div class="h-12 w-12 bg-purple-700 rounded-xl flex items-center justify-center mb-6 shadow-lg shadow-purple-200">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-4">Monitoring Lahan Real-time (IoT)</h4>
                        <p class="text-lg text-gray-600 leading-relaxed mb-6">
                            Dapatkan data kondisi lingkungan lahan Anda (Suhu, Kelembaban) secara langsung melalui dashboard interaktif. Pantau kesehatan tanaman dari mana saja, kapan saja.
                        </p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-12">
                     <div class="md:w-1/2">
                        <div class="bg-purple-50 rounded-3xl p-8 elegant-shadow border border-purple-100">
                             <img src="{{ asset('img/forum.png') }}" alt="Marketplace" class="rounded-2xl w-full object-cover shadow-sm">
                        </div>
                    </div>
                    <div class="md:w-1/2 md:pl-10">
                         <div class="h-12 w-12 bg-purple-700 rounded-xl flex items-center justify-center mb-6 shadow-lg shadow-purple-200">
                            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <h4 class="text-2xl font-bold text-gray-900 mb-4">Ekosistem Marketplace & Komunitas</h4>
                        <p class="text-lg text-gray-600 leading-relaxed mb-6">
                            Terhubung dengan penyuluh ahli di forum diskusi dan temukan solusi obat atau pupuk yang tepat langsung di marketplace kami. Solusi dari hulu ke hilir.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="team" class="py-24 bg-gray-50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-sm font-bold text-purple-700 uppercase tracking-widest mb-2">Tim Ahli</h2>
                <h3 class="text-4xl font-bold text-gray-900">Pikiran Dibalik Inovasi</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                    <div class="h-80 w-full overflow-hidden">
                        <img class="w-full h-full object-cover object-center" src="{{ asset('img/rafi.jpeg') }}">
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-bold text-gray-900">Arya Kusuma Pratama</h4>
                        <p class="text-purple-700 font-semibold text-sm uppercase tracking-wide mb-3">System Analyst</p>
                        <p class="text-gray-500 text-sm leading-relaxed">J0404231015</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                    <div class="h-80 w-full overflow-hidden">
                        <img class="w-full h-full object-cover object-center" src="{{ asset('img/inas.jpg') }}">
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-bold text-gray-900">Inas Samara Taqia</h4>
                        <p class="text-purple-700 font-semibold text-sm uppercase tracking-wide mb-3">Back-End Developer</p>
                        <p class="text-gray-500 text-sm leading-relaxed">J0404231167</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100">
                    <div class="h-80 w-full overflow-hidden">
                        <img class="w-full h-full object-cover object-center" src="{{ asset('img/rafi.jpeg') }}">
                    </div>
                    <div class="p-6">
                        <h4 class="text-xl font-bold text-gray-900">Muhammad Rafi Riza Pratama</h4>
                        <p class="text-purple-700 font-semibold text-sm uppercase tracking-wide mb-3">Front-End Developer</p>
                        <p class="text-gray-500 text-sm leading-relaxed">J0404231125</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
             <div class="text-center mb-16">
                <h2 class="text-sm font-bold text-purple-700 uppercase tracking-widest mb-2">FAQ</h2>
                <h3 class="text-3xl md:text-4xl font-bold text-gray-900">Pertanyaan yang Sering Diajukan</h3>
            </div>

            <div class="space-y-4">
                <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl bg-gray-50 overflow-hidden">
                    <button @click="open = !open" class="flex justify-between items-center w-full px-6 py-4 text-left hover:bg-gray-100 transition">
                        <h4 class="text-lg font-semibold text-gray-800">Bagaimana cara kerja deteksi penyakitnya?</h4>
                        <svg class="h-5 w-5 text-purple-600 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-collapse.duration.400ms class="px-6 pb-6 pt-2 text-gray-600 border-t border-gray-200">
                        Kamera pada robot atau laptop akan menangkap gambar daun. Sistem AI (Machine Learning) kami yang sudah dilatih dengan ribuan data akan menganalisis pola warna dan tekstur untuk menentukan apakah daun Sehat, Bercak, atau Layu dalam hitungan detik.
                    </div>
                </div>

                <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl bg-gray-50 overflow-hidden">
                     <button @click="open = !open" class="flex justify-between items-center w-full px-6 py-4 text-left hover:bg-gray-100 transition">
                        <h4 class="text-lg font-semibold text-gray-800">Apakah data sensor IoT akurat?</h4>
                        <svg class="h-5 w-5 text-purple-600 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-collapse.duration.400ms class="px-6 pb-6 pt-2 text-gray-600 border-t border-gray-200">
                        Sangat akurat. Kami menggunakan sensor industri grade untuk Suhu, Kelembaban, dan Intensitas Cahaya. Data dikirim secara real-time ke server dan ditampilkan di dashboard petani tanpa delay berarti.
                    </div>
                </div>

                <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl bg-gray-50 overflow-hidden">
                     <button @click="open = !open" class="flex justify-between items-center w-full px-6 py-4 text-left hover:bg-gray-100 transition">
                        <h4 class="text-lg font-semibold text-gray-800">Bagaimana jika saya tidak punya laptop?</h4>
                        <svg class="h-5 w-5 text-purple-600 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-collapse.duration.400ms class="px-6 pb-6 pt-2 text-gray-600 border-t border-gray-200">
                        Terra didesain responsif (mobile-friendly). Anda bisa mengakses dashboard, marketplace, dan forum melalui Smartphone (HP) Anda dengan tampilan yang tetap nyaman dan fungsional.
                    </div>
                </div>

                <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl bg-gray-50 overflow-hidden">
                     <button @click="open = !open" class="flex justify-between items-center w-full px-6 py-4 text-left hover:bg-gray-100 transition">
                        <h4 class="text-lg font-semibold text-gray-800">Apakah ada biaya langganan?</h4>
                        <svg class="h-5 w-5 text-purple-600 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-collapse.duration.400ms class="px-6 pb-6 pt-2 text-gray-600 border-t border-gray-200">
                        Untuk fitur dasar seperti Marketplace dan Forum, GRATIS selamanya. Fitur monitoring IoT dan Robot Control memerlukan pembelian perangkat keras (Hardware) kami sekali bayar.
                    </div>
                </div>

                <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl bg-gray-50 overflow-hidden">
                     <button @click="open = !open" class="flex justify-between items-center w-full px-6 py-4 text-left hover:bg-gray-100 transition">
                        <h4 class="text-lg font-semibold text-gray-800">Bagaimana cara menjual hasil panen di Terra?</h4>
                        <svg class="h-5 w-5 text-purple-600 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-collapse.duration.400ms class="px-6 pb-6 pt-2 text-gray-600 border-t border-gray-200">
                        Cukup daftar akun sebagai 'Penjual', lalu masuk ke menu Marketplace. Anda bisa mengunggah foto produk, deskripsi, dan harga. Pembeli akan terhubung langsung ke WhatsApp Anda.
                    </div>
                </div>

                 <div x-data="{ open: false }" class="border border-gray-200 rounded-2xl bg-gray-50 overflow-hidden">
                     <button @click="open = !open" class="flex justify-between items-center w-full px-6 py-4 text-left hover:bg-gray-100 transition">
                        <h4 class="text-lg font-semibold text-gray-800">Apakah data saya aman?</h4>
                        <svg class="h-5 w-5 text-purple-600 transition-transform duration-300" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-collapse.duration.400ms class="px-6 pb-6 pt-2 text-gray-600 border-t border-gray-200">
                        Keamanan adalah prioritas kami. Semua data pengguna dan laporan dilindungi dengan enkripsi standar industri. Hanya Anda dan teknisi berwenang yang bisa mengakses data sensitif.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32 bg-purple-900 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Siap Meningkatkan Hasil Panen Anda?</h2>
            <p class="text-purple-200 text-lg mb-10 max-w-2xl mx-auto">Jangan biarkan penyakit tanaman merugikan Anda. Bergabunglah dengan Terra hari ini dan rasakan masa depan pertanian.</p>
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-bold text-purple-900 transition-all duration-200 bg-white border border-transparent rounded-full hover:bg-gray-100 hover:shadow-lg hover:scale-105">
                Daftar Akun Gratis
            </a>
        </div>
    </section>

    <footer class="bg-gray-900 py-12 border-t border-gray-800 text-gray-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-3">
                <img src="{{ asset('img/logo.png') }}" alt="Terra Logo" class="h-8 w-auto grayscale hover:grayscale-0 transition duration-300">
                <span class="text-xl font-bold text-white tracking-tight">Terra.</span>
            </div>
            
            <div class="text-sm">
                &copy; {{ date('Y') }} Terra Team - Sekolah Vokasi IPB University
            </div>

            <div class="flex space-x-6">
                <a href="#" class="hover:text-purple-400 transition"><span class="sr-only">Facebook</span><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg></a>
                <a href="#" class="hover:text-purple-400 transition"><span class="sr-only">Instagram</span><svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.74.01 3.696.054 1.062.048 1.967.283 2.64.811.673.528 1.176 1.255 1.65 2.236.054.956.106 1.266.106 3.696s-.052 2.74-.106 3.696c-.048 1.062-.283 1.967-.811 2.64-.528.673-1.255 1.176-2.236 1.65-.956.054-1.266.106-3.696.106s-2.74-.052-3.696-.106c-1.062-.048-1.967-.283-2.64-.811-.673-.528-1.176-1.255-1.65-2.236-.054-.956-.106-1.266-.106-3.696s.052-2.74.106-3.696c.048-1.062.283-1.967.811-2.64.528-.673 1.255-1.176 2.236-1.65.956-.054 1.266-.106 3.696-.106zm0-2c-2.723 0-3.06.012-4.13.06C6.092.154 4.725.694 3.51 1.908 2.294 3.124 1.753 4.49 1.705 6.558 1.657 7.628 1.645 7.965 1.645 10.685s.012 3.06.06 4.13c.048 2.068.588 3.434 1.803 4.65 1.215 1.216 2.58 1.756 4.65 1.804 1.07.048 1.407.06 4.13.06 2.723 0 3.06-.012 4.13-.06 2.068-.048 3.434-.588 4.65-1.804 1.216-1.215 1.756-2.58 1.804-4.65.048-1.07.06-1.407.06-4.13s-.012-3.06-.06-4.13c-.048-2.068-.588-3.434-1.803-4.65-1.215-1.216-2.58-1.756-4.65-1.804C15.375 2.012 15.038 2 12.315 2zm0 4.864a5.821 5.821 0 100 11.642 5.821 5.821 0 000-11.642zm0 9.643a3.822 3.822 0 110-7.644 3.822 3.822 0 010 7.644zm7.212-8.175a1.332 1.332 0 11-2.664 0 1.332 1.332 0 012.664 0z" clip-rule="evenodd" /></svg></a>
            </div>
        </div>
    </footer>

</body>
</html>