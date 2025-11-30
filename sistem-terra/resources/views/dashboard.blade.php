<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Utama') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="relative bg-gradient-to-r from-purple-800 to-purple-600 rounded-3xl p-8 md:p-12 shadow-2xl overflow-hidden mb-10 text-white">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-pink-500 opacity-20 rounded-full blur-2xl"></div>

                <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                    <div>
                        <h3 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                            Halo, {{ Auth::user()->name }}! 👋
                        </h3>
                        <p class="text-purple-100 text-lg font-medium opacity-90">
                            Selamat datang kembali di <span class="font-bold text-white">Terra Ecosystem</span>.
                            <br>Role Anda saat ini: <span class="bg-white/20 px-3 py-1 rounded-full text-sm font-bold uppercase tracking-wider">{{ Auth::user()->role }}</span>
                        </p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-4 rounded-2xl flex items-center gap-4 min-w-[200px]">
                        <div class="bg-yellow-400 rounded-full p-2 shadow-lg shadow-yellow-500/50">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">29°C</p>
                            <p class="text-xs text-purple-100">Cerah Berawan</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 transition hover:shadow-md">
                    <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Aktivitas Terakhir</p>
                        <p class="text-gray-900 font-bold text-lg">Hari ini</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 transition hover:shadow-md">
                    <div class="p-3 bg-green-50 rounded-xl text-green-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Status Sistem</p>
                        <p class="text-green-600 font-bold text-lg">Online • Stabil</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 transition hover:shadow-md">
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Pusat Bantuan</p>
                        <p class="text-gray-900 font-bold text-lg">Siap 24/7</p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Akses Cepat Menu</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <a href="{{ route('marketplace') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-purple-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                        <div class="h-12 w-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg group-hover:text-purple-700">Marketplace</h4>
                        <p class="text-sm text-gray-500 mt-1">Jual beli produk pertanian.</p>
                    </a>

                    <a href="{{ route('forum') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-purple-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                        <div class="h-12 w-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg group-hover:text-blue-700">Forum Diskusi</h4>
                        <p class="text-sm text-gray-500 mt-1">Tanya jawab dengan ahli.</p>
                    </a>

                    <a href="{{ route('history') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-purple-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                        <div class="h-12 w-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600 group-hover:bg-yellow-600 group-hover:text-white transition-colors mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-lg group-hover:text-yellow-700">Riwayat</h4>
                        <p class="text-sm text-gray-500 mt-1">Cek log aktivitas Anda.</p>
                    </a>

                    @if(Auth::user()->role == 'petani')
                        <a href="{{ route('robot') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-green-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                            <div class="h-12 w-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600 group-hover:bg-green-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-lg group-hover:text-green-700">Kontrol Robot</h4>
                            <p class="text-sm text-gray-500 mt-1">Kendalikan robot & AI.</p>
                        </a>

                        <a href="{{ route('sensor') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-teal-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                            <div class="h-12 w-12 bg-teal-100 rounded-xl flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-colors mb-4">
                                <i class="fas fa-chart-bar text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 text-lg group-hover:text-teal-700">Monitoring IoT</h4>
                            <p class="text-sm text-gray-500 mt-1">Pantau suhu & kelembaban.</p>
                        </a>

                        <a href="{{ route('reports.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-red-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                            <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors mb-4">
                                <i class="fas fa-exclamation-triangle text-xl"></i>
                            </div>
                            <h4 class="font-bold text-gray-900 text-lg group-hover:text-red-700">Lapor Masalah</h4>
                            <p class="text-sm text-gray-500 mt-1">Hubungi teknisi jika error.</p>
                        </a>
                    @endif

                    @if(Auth::user()->role == 'teknisi')
                        <a href="{{ route('admin.users') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-gray-800 hover:shadow-lg transition-all duration-300 cursor-pointer">
                            <div class="h-12 w-12 bg-gray-200 rounded-xl flex items-center justify-center text-gray-700 group-hover:bg-gray-800 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-lg group-hover:text-gray-800">Manajemen User</h4>
                            <p class="text-sm text-gray-500 mt-1">Kelola pengguna sistem.</p>
                        </a>

                        <a href="{{ route('reports.index') }}" class="group bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-red-500 hover:shadow-lg transition-all duration-300 cursor-pointer">
                            <div class="h-12 w-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600 group-hover:bg-red-600 group-hover:text-white transition-colors mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <h4 class="font-bold text-gray-900 text-lg group-hover:text-red-700">Laporan Masuk</h4>
                            <p class="text-sm text-gray-500 mt-1">Cek keluhan petani.</p>
                        </a>
                    @endif

                </div>
            </div>

        </div>
    </div>
</x-app-layout>