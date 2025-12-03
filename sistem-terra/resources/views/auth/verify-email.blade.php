<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verifikasi Email - Terra</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .terra-gradient {
            background: linear-gradient(135deg, #9333ea 0%, #7c3aed 50%, #6d28d9 100%);
        }
        .leaf-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center terra-gradient leaf-pattern">
    <div class="w-full max-w-md mx-4">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full mb-4 shadow-lg">
                <i class="fas fa-seedling text-3xl text-purple-600"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">Terra</h1>
            <p class="text-purple-100 text-sm">Sistem Pertanian Digital</p>
        </div>
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center gap-2">
                    <i class="fas fa-envelope-circle-check"></i>
                    Verifikasi Email
                </h2>
            </div>
            
            <div class="p-6">
                <div class="mb-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-info-circle text-purple-600"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-700 text-sm leading-relaxed">
                                Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengklik tautan yang kami kirim melalui email. Jika Anda tidak menerima email, kami akan dengan senang hati mengirimkan ulang.
                            </p>
                        </div>
                    </div>
                </div>
                @if (session('status') == 'verification-link-sent')
                    <div class="mb-6 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex items-center gap-2 text-purple-700">
                            <i class="fas fa-check-circle"></i>
                            <span class="text-sm font-medium">
                                Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.
                            </span>
                        </div>
                    </div>
                @endif
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                            <i class="fas fa-at text-gray-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Email terdaftar</p>
                            <p class="text-sm font-medium text-gray-800">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-paper-plane"></i>
                            Kirim Ulang Email Verifikasi
                        </button>
                    </form>

                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span>atau</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center gap-2">
                            <i class="fas fa-sign-out-alt"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="text-center mt-6">
            <p class="text-purple-100 text-xs">
                © 2025 Terra - Sistem Pertanian Digital
            </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.bg-white');
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
