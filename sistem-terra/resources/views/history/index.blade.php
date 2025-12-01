<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                Riwayat Aktivitas
            </h2>
            <a href="{{ route('history.export') }}" 
               class="bg-gray-800 hover:bg-black text-white text-sm font-bold px-4 py-2 rounded-lg flex items-center gap-2 shadow-lg transition transform hover:scale-105">
                <i class="fas fa-download"></i>
                Download Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($role == 'petani' || $role == 'teknisi')
                <div class="bg-white overflow-hidden rounded-2xl shadow-xl border border-gray-200 mb-8">
                    <div class="bg-purple-900 px-6 py-4 border-b border-purple-800 flex justify-between items-center">
                        <h3 class="font-bold text-white text-lg flex items-center gap-2">
                            <i class="fas fa-seedling mr-2"></i>
                            Log Deteksi Penyakit (AI)
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
                                    <th class="p-4">Data Sensor</th>
                                    <th class="p-4">Info Penyakit</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($data['detections'] as $det)
                                @php
                                    $timestamp = $det->timestamp ?? $det->created_at ?? time();
                                    try {
                                        if (is_numeric($timestamp)) {
                                            if ($timestamp > 9999999999) {
                                                $date = \Carbon\Carbon::createFromTimestampMs($timestamp);
                                            } else {
                                                $date = \Carbon\Carbon::createFromTimestamp($timestamp);
                                            }
                                        } else {
                                            $date = \Carbon\Carbon::parse($timestamp);
                                        }
                                        $dateText = $date->format('d M Y H:i');
                                    } catch (\Exception $e) {
                                        $dateText = \Carbon\Carbon::now()->format('d M Y H:i');
                                    }
                                    
                                    $disease = $det->dominan_disease ?? $det->label ?? 'Tidak Dikenali';
                                    $confidence = $det->confidence ?? $det->dominan_confidence_avg ?? 0;
                                    if ($confidence < 1) $confidence = $confidence * 100;
                                    $isHealthy = Str::contains(strtolower($disease), 'sehat') || 
                                                (isset($det->status) && strtolower($det->status) === 'sehat');
                                    
                                    $sensorData = $det->sensor_data ?? $det->sensor_rata_rata ?? $det->{'sensor_rata-rata'} ?? [];
                                    if (is_string($sensorData)) {
                                        $sensorData = json_decode($sensorData, true) ?? [];
                                    }
                                    $suhu = $det->sensor_rata_rata['suhu'] ?? 'N/A';
                                    $kelembapan = $det->sensor_rata_rata['kelembapan'] ?? 'N/A';
                                    $cahaya = $det->sensor_rata_rata['cahaya'] ?? 'N/A';
                                    $sensorStatus = $sensorData['status'] ?? 'Normal';
                                    
                                    
                                    //INFO DATA
                                    $info = $det->info ?? [];
                                    $ciri = $info['ciri'] ?? 'Tidak ada informasi';
                                    $rekomendasi = $info['rekomendasi_penanganan'] ?? 'Tidak ada rekomendasi';
                                @endphp
                                <tr class="hover:bg-purple-50/50 transition">
                                    <td class="p-4 text-gray-600 font-mono text-sm">{{ $dateText }}</td>
                                    <td class="p-4 font-bold text-gray-800">{{ $disease }}</td>
                                    <td class="p-4">
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-gray-200 rounded-full h-1.5">
                                                <div class="bg-purple-500 h-1.5 rounded-full" style="width: {{ min(100, $confidence) }}%"></div>
                                            </div>
                                            <span class="text-xs font-bold">{{ number_format($confidence, 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        @if($isHealthy)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aman</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Perlu Tindakan</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-xs text-gray-600">
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-1"><span class="text-gray-400">🌡️</span><span>{{ $suhu }}°C</span></div>
                                            <div class="flex items-center gap-1"><span class="text-gray-400">💧</span><span>{{ $kelembapan }}%</span></div>
                                            <div class="flex items-center gap-1"><span class="text-gray-400">☀️</span><span>{{ $cahaya }} Lux</span></div>
                                            @if(isset($sensorData['status']) && str_contains($sensorData['status'], 'Warning'))
                                                <div class="text-xs font-bold text-yellow-600 bg-yellow-50 px-1.5 py-0.5 rounded mt-1">
                                                    ⚠️ {{ $sensorData['status'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs text-gray-700 max-w-xs">
                                        <div class="space-y-1">
                                            <div><span class="font-bold text-gray-500">Ciri:</span> {{ $ciri }}</div>
                                            <div><span class="font-bold text-gray-500">Rekomendasi:</span> {{ $rekomendasi }}</div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-center">
                                        <button onclick="deleteDetection({{ data_get($det, 'id') }}, '{{ $disease }}')" 
                                                class="text-red-600 hover:text-red-900 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                                title="Hapus Data">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="p-8 text-center text-gray-400">Belum ada data deteksi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                        <button id="prevPage" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            ← Sebelumnya
                        </button>
                        <span class="text-xs text-gray-600">
                            Halaman <span id="currentPage" class="font-bold">1</span> dari <span id="totalPages" class="font-bold">1</span>
                        </span>
                        <button id="nextPage" class="px-3 py-1.5 text-xs font-bold rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 disabled:opacity-50 disabled:cursor-not-allowed">
                            Berikutnya →
                        </button>
                    </div>
                </div>

                <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-200">
                    <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                        <h3 class="font-bold text-white text-lg"><i class="fas fa-broadcast-tower mr-2"></i> Log Sensor IoT</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @foreach($data['sensors'] as $sensor)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-2 rounded-full {{ $sensor['status'] == 'Normal' ? 'bg-green-500' : 'bg-yellow-500' }}"></div>
                                <div>
                                    <p class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($sensor['time'])->diffForHumans() }}</p>
                                    <p class="text-xs text-gray-500">Suhu: {{ $sensor['suhu'] }}°C • Lembab: {{ $sensor['kelembapan'] }}%</p>
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
                        <h4 class="font-bold text-gray-700 mb-4"><i class="fas fa-file-alt mr-2"></i> Rincian Klik Terakhir:</h4>
                        <div class="space-y-2">
                            @forelse($data['clicks']->take(10) as $click)
                                <div class="flex justify-between items-center p-3 border rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                            <i class="fas fa-edit text-blue-600"></i>
                                            <i class="fab fa-whatsapp text-green-600"></i>
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
                        <div class="p-3 bg-blue-100 rounded-xl text-blue-600">
                            <i class="fas fa-comments text-2xl"></i>
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
    <script>
    </script>
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js";
        import { getDatabase, ref, onValue } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-database.js";
        import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-analytics.js";

        let allDetections = [];
        let currentPage = 1;
        const itemsPerPage = 4;

        const firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            databaseURL: "{{ config('services.firebase.database_url') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };

        const app = initializeApp(firebaseConfig);
        const database = getDatabase(app);
        const analytics = getAnalytics(app);

        function deleteDetection(id, diseaseName) {
            console.log('deleteDetection called with id:', id, 'diseaseName:', diseaseName);
            if (confirm('Apakah Anda yakin ingin menghapus data deteksi "' + diseaseName + '"?')) {
                fetch('/history/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Data berhasil dihapus', 'success');
                        updateTableFromFirebase();
                    } else {
                        showNotification('Gagal menghapus data', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error deleting detection:', error);
                    showNotification('Terjadi kesalahan saat menghapus data', 'error');
                });
            }
        }

        function showNotification(message, type) {
            let notification = document.getElementById('firebaseNotification');
            if (!notification) {
                notification = document.createElement('div');
                notification.id = 'firebaseNotification';
                notification.className = 'fixed top-4 right-4 z-50 hidden';
                const bgColor = type === 'success' ? 'bg-green-500' : (type === 'error' ? 'bg-red-500' : 'bg-blue-500');
                notification.innerHTML = 
                    '<div class="' + bgColor + ' text-white px-4 py-2 rounded-lg shadow-lg">' +
                        '<span class="text-sm font-medium">' + message + '</span>' +
                    '</div>';
                document.body.appendChild(notification);
            }
            notification.classList.remove('hidden');
            setTimeout(function() {
                notification.classList.add('hidden');
            }, 3000);
        }

        function updateTableFromFirebase() {
            fetch('/history/refresh')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateTable(data.detections);
                        showNotification('Data diperbarui secara real-time', 'success');
                    }
                })
                .catch(error => console.error('Error updating table:', error));
        }

        function updateTable(newDetections) {
            allDetections = newDetections;
            currentPage = 1;
            updatePaginationDisplay();
            renderCurrentPage();
        }

        function updatePaginationDisplay() {
            const totalPages = Math.max(1, Math.ceil(allDetections.length / itemsPerPage));
            document.getElementById('currentPage').textContent = currentPage;
            document.getElementById('totalPages').textContent = totalPages;
            const prevBtn = document.getElementById('prevPage');
            const nextBtn = document.getElementById('nextPage');
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages;
        }

        function renderCurrentPage() {
            const tbody = document.querySelector('tbody');
            if (!tbody) return;
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageData = allDetections.slice(startIndex, endIndex);
            tbody.innerHTML = '';
            if (pageData.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-400">Belum ada data deteksi.</td></tr>';
                return;
            }
            pageData.forEach((det, index) => {
                const row = createDetectionRow(det, startIndex + index);
                tbody.appendChild(row);
            });
        }

        function createDetectionRow(det, index) {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-purple-50/50 transition';
            const timestamp = det.timestamp || det.created_at || Date.now();
            let date;
            try {
                if (typeof timestamp === 'number') {
                    if (timestamp > 9999999999) {
                        date = new Date(timestamp);
                    } else {
                        date = new Date(timestamp * 1000);
                    }
                } else {
                    date = new Date(timestamp);
                }
                if (isNaN(date.getTime())) {
                    throw new Error('Invalid date');
                }
            } catch (e) {
                date = new Date();
            }
            const dateText = date.toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) + ' ' + date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
            const disease = det.dominan_disease || det.label || 'Tidak Dikenali';
            const confidence = det.confidence || det.dominan_confidence_avg || 0;
            const confPercent = confidence < 1 ? confidence * 100 : confidence;
            const status = det.status || 'unknown';
            const isHealthy = disease.toLowerCase().includes('sehat') || status.toLowerCase() === 'sehat';
            const sensorData = det.sensor_data || det.sensor_rata_rata || det['sensor_rata-rata'] || {};
            const suhu = sensorData.suhu || '-';
            const kelembapan = sensorData.kelembapan || '-';
            const cahaya = sensorData.cahaya || '-';
            const sensorStatus = sensorData.status || 'Normal';
            const info = det.info || {};
            const ciri = info.ciri || '-';
            const rekomendasi = info.rekomendasi_penanganan || '-';
            tr.innerHTML = `
                <td class="p-4 text-gray-600 font-mono text-sm">${dateText}</td>
                <td class="p-4 font-bold text-gray-800">${disease.replace(/_/g, ' ')}</td>
                <td class="p-4">
                    <div class="flex items-center gap-2">
                        <div class="w-16 bg-gray-200 rounded-full h-1.5">
                            <div class="bg-purple-500 h-1.5 rounded-full" style="width: ${Math.min(100, confPercent)}%"></div>
                        </div>
                        <span class="text-xs font-bold">${confPercent.toFixed(1)}%</span>
                    </div>
                </td>
                <td class="p-4">
                    ${isHealthy ? 
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aman</span>' :
                        '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Perlu Tindakan</span>'
                    }
                </td>
                <td class="p-4 text-xs text-gray-600">
                    <div class="space-y-1">
                        <div class="flex items-center gap-1"><i class="fas fa-thermometer-half text-gray-400"></i><span>${suhu}${suhu !== '-' ? '°C' : ''}</span></div>
                        <div class="flex items-center gap-1"><i class="fas fa-tint text-gray-400"></i><span>${kelembapan}${kelembapan !== '-' ? '%' : ''}</span></div>
                        <div class="flex items-center gap-1"><i class="fas fa-sun text-gray-400"></i><span>${cahaya}${cahaya !== '-' ? ' Lux' : ''}</span></div>
                        ${sensorStatus.includes('Warning') ? `<div class="text-xs font-bold text-yellow-600 bg-yellow-50 px-1.5 py-0.5 rounded mt-1"><i class="fas fa-exclamation-triangle mr-1"></i>${sensorStatus}</div>` : ''}
                    </div>
                </td>
                <td class="p-4 text-xs text-gray-700 max-w-xs">
                    <div class="space-y-1">
                        <div><span class="font-bold text-gray-500">Ciri:</span> ${ciri !== '-' ? ciri : 'Tidak ada'}</div>
                        <div><span class="font-bold text-gray-500">Rekomendasi:</span> ${rekomendasi !== '-' ? rekomendasi : 'Tidak ada'}</div>
                    </div>
                </td>
                <td class="p-4 text-center">
                    <button onclick="window.deleteDetection(${det.id || det['id']}, '${disease.replace(/'/g, "\\'")}')" 
                            class="text-red-600 hover:text-red-900 hover:bg-red-50 p-2 rounded-lg transition-colors"
                            title="Hapus Data">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </td>
            `;
            return tr;
        }

        function initializePagination() {
            fetch('/history/refresh')
                .then(response => response.json())
                .then(data => {
                    console.log('Initial data received:', data);
                    if (data.success) {
                        allDetections = data.detections;
                        console.log('All detections:', allDetections);
                        updatePaginationDisplay();
                        renderCurrentPage();
                    } else {
                        console.error('Server returned error:', data.message);
                    }
                })
                .catch(error => console.error('Error loading data:', error));
        }
        window.deleteDetection = deleteDetection;

        document.addEventListener('DOMContentLoaded', () => {
            initializePagination();
            document.getElementById('prevPage').addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    updatePaginationDisplay();
                    renderCurrentPage();
                }
            });
            document.getElementById('nextPage').addEventListener('click', () => {
                const totalPages = Math.ceil(allDetections.length / itemsPerPage);
                if (currentPage < totalPages) {
                    currentPage++;
                    updatePaginationDisplay();
                    renderCurrentPage();
                }
            });
            const autoSimpanRef = ref(database, 'detections/autoSimpan');
            onValue(autoSimpanRef, (snapshot) => {
                const data = snapshot.val();
                if (data) {
                    console.log('Real-time update from user', window.authUserId, ':', Object.keys(data).length, 'records');
                    updateTableFromFirebase();
                }
            });
        });
    </script>
</x-app-layout>