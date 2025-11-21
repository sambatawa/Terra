<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - Terra</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #6D28D9 0%, #A855F7 100%);
        }
    </style>
</head>
<body class="antialiased bg-white">

    <div class="min-h-screen flex">
        
        <div class="hidden lg:flex lg:w-1/2 relative bg-gray-900 overflow-hidden">
            <div class="absolute inset-0 bg-cover bg-center opacity-40" style="background-image: url('https://images.unsplash.com/photo-1625246333195-78d9c38ad449?q=80&w=1600&auto=format&fit=crop');"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-purple-900/90 to-purple-800/90"></div>

            <div class="relative z-10 w-full flex flex-col justify-center px-16">
                <div class="mb-8">
                    <img src="{{ asset('img/logo.png') }}" alt="Terra Logo" class="h-12 w-auto mb-6 bg-white rounded-lg p-1">
                    <h2 class="text-4xl font-bold text-white mb-2">Selamat Bergabung, Terra's!</h2>
                    <p class="text-purple-200 text-lg">Mulai langkah pertanian cerdas Anda hari ini.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-8 shadow-2xl">
                    <h3 class="text-white font-bold text-xl mb-6 border-b border-white/20 pb-4">Langkah Pendaftaran</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold text-sm">1</div>
                            <div>
                                <h4 class="text-white font-semibold">Isi Identitas Diri</h4>
                                <p class="text-purple-200 text-sm mt-1">Masukkan Nama lengkap dan Email aktif Anda.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold text-sm">2</div>
                            <div>
                                <h4 class="text-white font-semibold">Pilih Peran (Role)</h4>
                                <p class="text-purple-200 text-sm mt-1">Apakah Anda Petani, Penjual, atau Penyuluh? Pilih yang sesuai.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold text-sm">3</div>
                            <div>
                                <h4 class="text-white font-semibold">Buat Kata Sandi</h4>
                                <p class="text-purple-200 text-sm mt-1">Pastikan kata sandi aman dan mudah diingat.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mt-10 text-purple-300 text-sm">Butuh bantuan teknisi? Hubungi <b>0812-8455-7025</b> (WA)</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white overflow-y-auto">
            <div class="w-full max-w-md py-8">
                
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('img/logo.png') }}" class="h-10 w-auto mx-auto mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
                </div>

                <div class="hidden lg:block mb-8">
                    <h2 class="text-3xl font-bold text-gray-900">Buat Akun Baru 🚀</h2>
                    <p class="text-gray-500 mt-2">Lengkapi formulir di bawah ini.</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            </div>
                            <input type="text" name="name" required autofocus class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition" placeholder="Nama Anda" />
                        </div>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                            </div>
                            <input type="email" name="email" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition" placeholder="email@contoh.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Daftar Sebagai</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            </div>
                            <select name="role" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition appearance-none">
                                <option value="petani">Petani (Pembeli & Pengguna Alat)</option>
                                <option value="penjual">Penjual (Toko Pertanian)</option>
                                <option value="penyuluh">Penyuluh (Ahli Pertanian)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <input type="password" name="password" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ulangi Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <input type="password" name="password_confirmation" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition" placeholder="••••••••" />
                        </div>
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-primary hover:shadow-lg hover:shadow-purple-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all transform hover:-translate-y-0.5 mt-6">
                        {{ __('Daftar Sekarang') }}
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-bold text-purple-600 hover:text-purple-800 transition">
                            Masuk di sini
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</body>
</html>