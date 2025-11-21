<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lupa Sandi - Terra</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">
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
                    <h2 class="text-4xl font-bold text-white mb-2">Lupa Kata Sandi?</h2>
                    <p class="text-purple-200 text-lg">Jangan panik, kami bantu pulihkan akun Anda.</p>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-8 shadow-2xl">
                    <h3 class="text-white font-bold text-xl mb-6 border-b border-white/20 pb-4">Langkah Pemulihan</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold text-sm">1</div>
                            <div>
                                <h4 class="text-white font-semibold">Masukkan Email</h4>
                                <p class="text-purple-200 text-sm mt-1">Ketik alamat email yang terdaftar di akun Terra Anda.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold text-sm">2</div>
                            <div>
                                <h4 class="text-white font-semibold">Cek Kotak Masuk (Inbox)</h4>
                                <p class="text-purple-200 text-sm mt-1">Kami akan mengirimkan tautan (link) khusus ke email Anda.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white text-purple-700 flex items-center justify-center font-bold text-sm">3</div>
                            <div>
                                <h4 class="text-white font-semibold">Buat Sandi Baru</h4>
                                <p class="text-purple-200 text-sm mt-1">Klik link tersebut dan buat kata sandi baru yang aman.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <p class="mt-10 text-purple-300 text-sm">Masih kesulitan? Hubungi <b>0812-8455-7025</b> (Teknisi)</p>
            </div>
        </div>

        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">
                
                <div class="lg:hidden text-center mb-8">
                    <img src="{{ asset('img/logo.png') }}" class="h-10 w-auto mx-auto mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">Reset Password</h2>
                </div>

                <div class="hidden lg:block mb-6">
                    <div class="bg-purple-100 w-16 h-16 rounded-2xl flex items-center justify-center text-purple-600 mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Pulihkan Akun</h2>
                    <p class="text-gray-500 mt-2 text-sm leading-relaxed">
                        Lupa kata sandi? Tidak masalah. Beri tahu kami alamat email Anda dan kami akan mengirimkan tautan pengaturan ulang kata sandi melalui email.
                    </p>
                </div>

                <x-auth-session-status class="mb-4" :status="session('status')" />

                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Alamat Email Terdaftar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                            </div>
                            <input id="email" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 sm:text-sm transition duration-150 ease-in-out" type="email" name="email" :value="old('email')" required autofocus placeholder="contoh@email.com" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-primary hover:shadow-lg hover:shadow-purple-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all transform hover:-translate-y-0.5">
                        {{ __('Kirim Tautan Reset Password') }}
                    </button>
                </form>

                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600">
                        Sudah ingat sandi Anda? 
                        <a href="{{ route('login') }}" class="font-bold text-purple-600 hover:text-purple-800 transition flex items-center justify-center gap-1 mt-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Kembali ke Halaman Login
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</body>
</html>