<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                📜 Riwayat Aktivitas
            </h2>
            <a href="{{ route('history.export') }}" class="bg-gray-800 hover:bg-black text-white text-sm font-bold px-4 py-2 rounded-lg flex items-center gap-2 shadow-lg transition transform hover:scale-105">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($role == 'petani' || $role == 'teknisi')
                <div class="bg-white overflow-hidden rounded-2xl shadow-xl border border-gray-200 mb-8">
                    <div class="bg-purple-900 px-6 py-4 border-b border-purple-800 flex justify-between items-center">
                        <h3 class="font-bold text-white text-lg flex items-center gap-2">
                            🌱 Log Deteksi Penyakit (AI)
                        </h3>
                        <span class="text-purple-200 text-xs bg-purple-800 px-2 py-1 rounded">{{ $data['detections']->count() }} Data</span>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-purple-50 text-purple-900 uppercase text-xs font-bold tracking-wider">
                                <tr>
                                    <th class="p-4">Waktu</th>
                                    <th class="p-4">Hasil Deteksi</th>
                                    <th class="p-4">Keyakinan</th>
                                    <th class="p-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($data['detections'] as $det)
                                <tr class="hover:bg-purple-50/50 transition">
                                    <td class="p-4 text-gray-600 font-mono text-sm">{{ $det->created_at->format('d M Y, H:i') }}</td>
                                    <td class="p-4 font-bold text-gray-800">{{ $det->label }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ $det->confidence }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold">{{ $det->confidence }}%</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if(Str::contains(strtolower($det->label), 'sehat'))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aman</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Perlu Tindakan</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="p-8 text-center text-gray-400">Belum ada data deteksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-200">
                    <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                        <h3 class="font-bold text-white text-lg">📡 Log Sensor IoT</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($data['sensors'] as $sensor)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full {{ $sensor['status'] == 'Normal' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($sensor['time'])->diffForHumans() }}</p>
                                    <p class="text-xs text-gray-500">Suhu: {{ $sensor['suhu'] }}°C • Lembab: {{ $sensor['kelembaban'] }}%</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 rounded {{ $sensor['status'] == 'Normal' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">{{ $sensor['status'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

            @elseif($role == 'penjual')
                <div class="bg-white p-8 rounded-3xl shadow-xl text-center">
                    <h3 class="font-bold text-2xl text-gray-800 mb-6">Statistik Peminat Produk</h3>
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-purple-50 p-6 rounded-2xl border border-purple-100">
                            <p class="text-3xl font-black text-purple-700">{{ $data['total_clicks'] }}</p>
                            <p class="text-sm text-purple-500 font-bold">Total Klik WA</p>
                        </div>
                        <div class="bg-green-50 p-6 rounded-2xl border border-green-100">
                            <p class="text-3xl font-black text-green-700">{{ $data['clicks']->groupBy('product_name')->count() }}</p>
                            <p class="text-sm text-green-500 font-bold">Produk Dilirik</p>
                        </div>
                    </div>
                    <div class="text-left">
                        <h4 class="font-bold text-gray-700 mb-4">Rincian Klik Terakhir:</h4>
                        <div class="space-y-2">
                            @forelse($data['clicks']->take(10) as $click)
                                <div class="flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                        </div>
                                        <span class="font-medium text-gray-800">{{ $click->product_name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ $click->created_at->diffForHumans() }}</span>
                                </div>
                            @empty
                                <p class="text-center text-gray-400 py-4">Belum ada data klik.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            @elseif($role == 'penyuluh')
                
                <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center gap-4">
                        <div class="p-3 bg-yellow-100 rounded-xl text-yellow-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase">Kontribusi</p>
                            <p class="text-2xl font-black text-gray-800">{{ $data['total_posts'] }} <span class="text-sm font-normal text-gray-400">Post</span></p>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center gap-4">
                        <div class="p-3 bg-pink-100 rounded-xl text-pink-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase">Dampak (Interaksi)</p>
                            <p class="text-2xl font-black text-gray-800">{{ $data['total_interactions'] }} <span class="text-sm font-normal text-gray-400">Reacts</span></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-xl border border-purple-100 overflow-hidden">
                    <div class="bg-purple-900 px-6 py-5 border-b border-purple-800">
                        <h3 class="font-bold text-white text-lg flex items-center gap-2">
                            🎓 Jejak Edukasi Anda
                        </h3>
                    </div>
                    
                    <div class="p-0">
                        @forelse($data['posts'] as $post)
                        <div class="p-6 border-b border-gray-100 hover:bg-purple-50/30 transition flex gap-4 items-start">
                            <div class="flex-shrink-0 w-14 text-center">
                                <span class="block text-xs font-bold text-gray-400 uppercase">{{ $post->created_at->format('M') }}</span>
                                <span class="block text-2xl font-black text-purple-700 leading-none">{{ $post->created_at->format('d') }}</span>
                            </div>
                            
                            <div class="flex-1">
                                <p class="text-gray-800 text-base leading-relaxed mb-2">{{ Str::limit($post->content, 150) }}</p>
                                <div class="flex items-center gap-4 text-xs font-bold text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                        {{ $post->likes_count }} Likes
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        {{ $post->comments_count }} Komentar
                                    </span>
                                    <a href="{{ route('forum') }}" class="text-purple-600 hover:underline ml-auto">Lihat di Forum →</a>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="p-10 text-center">
                            <div class="inline-block p-4 bg-gray-100 rounded-full mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </div>
                            <p class="text-gray-500">Belum ada aktivitas edukasi. <a href="{{ route('forum') }}" class="text-purple-600 font-bold hover:underline">Mulai Posting!</a></p>
                        </div>
                        @endforelse
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>