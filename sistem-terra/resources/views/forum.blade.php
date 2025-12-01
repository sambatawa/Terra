<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Forum Diskusi Komunitas') }}
        </h2>
    </x-slot>
    
    <style>
        .ring-offset-2 {
            --tw-ring-offset-width: 2px;
        }
        .ring-2 {
            --tw-ring-width: 2px;
        }
        .ring-purple-400 {
            --tw-ring-color: rgb(192 132 252);
        }
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 300ms;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form action="{{ route('forum.post') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="flex gap-4">
                        <div class="h-12 w-12 rounded-full bg-green-600 flex items-center justify-center text-white font-bold text-xl shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="w-full">
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Judul Diskusi</label>
                                <input type="text" name="title" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" placeholder="Contoh: Cara mengatasi hama ulat pada tanaman cabai">
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm text-gray-600 mb-1">Kategori Diskusi</label>
                                <select name="category" required class="w-full border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach(App\Models\Post::$categories as $key => $category)
                                        <option value="{{ $key }}">
                                            <i class="{{ $category['icon'] }}"></i> {{ $category['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <textarea name="content" rows="3" class="w-full border-gray-200 rounded-lg bg-gray-50 focus:bg-white focus:ring-green-500 focus:border-green-500" placeholder="Apa yang ingin Anda diskusikan hari ini, Pak/Bu?"></textarea>
                            
                            <div class="flex justify-between items-center mt-3">
                                <input type="file" name="image" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700 font-bold">Posting</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="flex flex-wrap gap-2 mb-6">
                <a href="{{ route('forum') }}" 
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border transition-colors {{ !request('category') || request('category') == 'all' ? 'bg-green-100 text-green-700 border-green-300' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                    Semua
                </a>
                @foreach(App\Models\Post::$categories as $key => $category)
                <a href="{{ route('forum', ['category' => $key]) }}" 
                   class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium border transition-colors {{ request('category') == $key ? 'text-white border-transparent' : 'border-transparent hover:opacity-80' }}"
                   style="background-color: {{ $category['color'] }}20; color: {{ $category['color'] }}; {{ request('category') == $key ? 'background-color: ' . $category['color'] . '; color: white;' : '' }}">
                    {{ $category['name'] }}
                </a>
                @endforeach
            </div>

            @foreach($posts as $post)
            @php
                $isPenyuluh = $post->user->role == 'penyuluh';
                $cardClass = $isPenyuluh ? 'bg-purple-50 border-2 border-purple-300' : 'bg-white border border-gray-200';
                $badge = $isPenyuluh ? '<span class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full ml-2">🎓 Penyuluh Ahli</span>' : '<span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-full ml-2">🌾 Petani</span>';
            @endphp
            <div class="{{ $cardClass }} overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center font-bold text-gray-700">
                            {{ substr($post->user->name, 0, 1) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800 flex items-center">
                                {{ $post->user->name }} {!! $badge !!}
                            </h4>
                            <p class="text-xs text-gray-500">{{ $post->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if(Auth::id() == $post->user_id || Auth::user()->role == 'penyuluh')
                        <form action="{{ route('forum.delete', $post->id) }}" method="POST" onsubmit="return confirm('Hapus postingan ini?')">
                            @csrf @method('DELETE')
                            <button class="text-gray-400 hover:text-red-500 text-xl">×</button>
                        </form>
                    @endif
                </div>
                <h3 class="mt-4 font-bold text-lg text-gray-800">{{ $post->title }}</h3>
                <p class="mt-2 text-gray-800 whitespace-pre-line leading-relaxed">{{ $post->content }}</p>
                @if($post->image)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $post->image) }}" class="rounded-lg w-full object-cover max-h-96 border">
                    </div>
                @endif
                <hr class="my-4 border-gray-200">
               <div class="flex items-center gap-6 mt-4 pt-4 border-t border-gray-100">
                    <form action="{{ route('forum.like', $post->id) }}" method="POST">
                        @csrf
                        <button class="flex items-center gap-2 text-sm font-semibold transition duration-200 {{ $post->isLikedByAuthUser() ? 'text-pink-600' : 'text-gray-500 hover:text-pink-500' }}">
                            @if($post->isLikedByAuthUser())
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                    <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-.383-.218 25.18 25.18 0 01-4.244-3.17C4.688 15.36 2.25 12.174 2.25 8.25 2.25 5.322 4.714 3 7.688 3A5.5 5.5 0 0112 5.052 5.5 5.5 0 0116.313 3c2.973 0 5.437 2.322 5.437 5.25 0 3.925-2.438 7.111-4.739 9.256a25.175 25.175 0 01-4.244 3.17 15.247 15.247 0 01-.383.219l-.022.012-.007.004-.003.001a.752.752 0 01-.704 0l-.003-.001z" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                </svg>
                            @endif
                            <span>{{ $post->likes->count() }} Suka</span>
                        </button>
                    </form>
                    <button class="flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-blue-600 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                        </svg>
                        <span>{{ $post->comments->count() }} Komentar</span>
                    </button>
                    <div class="relative inline-block text-left">
                        <button onclick="toggleDropdown('share-menu-{{ $post->id }}')" class="flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-green-600 transition duration-200 group">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                            </svg>
                            <span>Bagikan</span>
                        </button>
                        <div id="share-menu-{{ $post->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-100">
                            <div class="py-1">
                                @php
                                    $waText = "Lihat diskusi menarik ini di Terra: " . $post->content;
                                    $waLink = "https://wa.me/?text=" . urlencode($waText);
                                @endphp
                                <a href="{{ $waLink }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700">
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    WhatsApp
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700">
                                    <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    Facebook
                                </a>
                                <button onclick="copyLink(this)" class="w-full flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    <span class="share-text">Salin Link</span>
                                </button>
                            </div>
                        </div>
                    </div>
            </div>

                <div class="mt-4 bg-gray-50/50 p-4 rounded-lg">
                    <div class="space-y-3 mb-4">
                        @foreach($post->comments as $comment)
                            <div class="flex gap-3">
                                <div class="h-8 w-8 rounded-full {{ $comment->user->role == 'penyuluh' ? 'bg-purple-600 text-white' : 'bg-gray-300' }} flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                                <div class="bg-gray-100 p-2 rounded-lg w-full {{ $comment->user->role == 'penyuluh' ? 'border border-purple-300 bg-purple-50' : '' }}">
                                    <div class="flex justify-between">
                                        <p class="font-bold text-xs {{ $comment->user->role == 'penyuluh' ? 'text-purple-700' : 'text-gray-700' }}">
                                            {{ $comment->user->name }} 
                                            @if($comment->user->role == 'penyuluh') (Ahli) @endif
                                        </p>
                                        <span class="text-[10px] text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 mt-1">{{ $comment->content }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('forum.comment', $post->id) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="text" name="content" placeholder="Tulis komentar..." class="w-full text-sm border-gray-300 rounded-full px-4 focus:ring-green-500 focus:border-green-500">
                        <button type="submit" class="text-green-600 hover:bg-green-50 px-3 py-1 rounded-full text-sm font-bold">Kirim</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</x-app-layout>

<script>
    function copyLink(btn) {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(() => {
            const textSpan = btn.querySelector('.share-text');
            const originalText = textSpan.innerText;
            textSpan.innerText = "Tersalin!";
            textSpan.classList.add('text-green-600', 'font-bold');
            setTimeout(() => {
                textSpan.innerText = originalText;
                textSpan.classList.remove('text-green-600', 'font-bold');
            }, 2000);
        });
    }
    //DROPDOWN
    function toggleDropdown(id) {
        document.querySelectorAll('[id^="share-menu-"]').forEach(el => {
            if(el.id !== id) el.classList.add('hidden');
        });
        const menu = document.getElementById(id);
        menu.classList.toggle('hidden');
    }

    //TUTUP DROPDOWN
    window.addEventListener('click', function(e) {
        if (!e.target.closest('.relative')) {
            document.querySelectorAll('[id^="share-menu-"]').forEach(el => {
                el.classList.add('hidden');
            });
        }
    });

</script>