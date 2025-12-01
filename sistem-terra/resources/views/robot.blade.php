<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <span class="bg-purple-600 text-white p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </span>
                Robot Control Center
            </h2>
            <div class="flex items-center gap-3 px-4 py-2 bg-white border border-purple-200 rounded-full shadow-sm">
                <span class="relative flex h-3 w-3">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
                <span class="text-xs font-bold text-gray-700 tracking-wide">TERKONEKSI: <span class="text-purple-700">WEBCAM</span></span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 min-h-screen"> <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <div class="lg:col-span-8 space-y-6">
                    <div class="relative bg-black rounded-3xl overflow-hidden shadow-2xl border-4 border-purple-900 group">
                        <div id="camera-loading" class="absolute inset-0 bg-gray-900 flex items-center justify-center z-30">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-500 mx-auto mb-4"></div>
                                <p class="text-white text-sm font-bold">Loading Camera...</p>
                            </div>
                        </div>
                        <video id="webcam" autoplay playsinline muted class="w-full h-[500px] object-cover opacity-90"></video>
                        <canvas id="aiCanvas" class="absolute top-0 left-0 w-full h-full z-10 pointer-events-none"></canvas>
                        <div class="absolute top-6 left-6 z-20">
                            <div id="status-box" class="backdrop-blur-md bg-black/60 border border-white/10 text-white px-4 py-2 rounded-xl shadow-lg flex items-center gap-3">
                                <div class="h-2 w-2 bg-yellow-400 rounded-full animate-pulse"></div>
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-bold">AI Status</p>
                                    <p id="ai-status" class="text-xs font-bold tracking-wide text-white">INITIALIZING...</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-6 right-6 z-20">
                            <div class="bg-purple-900/80 text-white px-3 py-1 rounded-lg text-xs font-mono border border-purple-500/30">
                                FPS: <span id="fps-counter">30</span>
                            </div>
                        </div>
                        <div id="product-alert" class="absolute bottom-6 right-6 z-40 hidden transition-all duration-500 transform translate-y-20 opacity-0">
                            <div class="bg-white border-2 border-purple-500 text-gray-800 p-4 rounded-2xl shadow-2xl max-w-xs flex gap-4 items-center">
                                <img id="rec-image" src="" class="w-14 h-14 object-cover rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-[10px] font-extrabold text-purple-600 uppercase tracking-wider animate-pulse"><i class="fas fa-exclamation-triangle mr-1"></i> PENYAKIT TERDETEKSI</p>
                                    <h4 id="rec-name" class="font-bold text-sm text-gray-900 truncate w-32 mt-0.5">Nama Produk</h4>
                                    <p class="text-xs text-gray-500 font-medium">Rp <span id="rec-price">0</span></p>
                                    <a id="rec-link" href="#" target="_blank" class="mt-2 inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white text-[10px] font-bold px-3 py-1.5 rounded-full transition">
                                        Beli Solusi
                                    </a>
                                </div>
                                <button onclick="closeAlert()" class="absolute top-1 right-2 text-gray-400 hover:text-gray-600">×</button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-900 p-4 rounded-xl border border-gray-800 shadow-inner">
                        <div class="flex justify-between items-center border-b border-gray-700 pb-2 mb-2">
                            <span class="text-xs font-mono text-purple-400">>_ System Log</span>
                            <div class="flex gap-1">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div>
                                <div class="w-2 h-2 rounded-full bg-yellow-500"></div>
                                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                            </div>
                        </div>
                        <div id="console-wrap" class="mt-1">
                            <div id="console-log" class="h-20 overflow-y-auto font-mono text-xs text-gray-400 rounded-lg scrollbar-thin scrollbar-thumb-gray-700"></div>
                            <div id="thirtysec-article-wrap" class="mt-4">
                                <h3 class="text-xs font-semibold text-purple-400 mb-2">
                                    Ringkasan Deteksi Terbaru
                                    <span id="summary-updated-time" class="text-gray-500 text-[10px] ml-2"></span>
                                </h3>
                                <div id="thirtysec-article" class="bg-purple-900 border border-gray-700 rounded-lg p-3 shadow-sm min-h-[120px] flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="summary-loading mx-auto mb-2"></div>
                                        <p class="text-xs text-gray-400">Menunggu hasil deteksi...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-4">
                    <div class="bg-purple-900 rounded-3xl p-6 shadow-2xl border border-purple-700 text-white h-full relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-purple-600 rounded-full blur-3xl opacity-20 -mr-16 -mt-16"></div>
                        <div class="absolute bottom-0 left-0 w-40 h-40 bg-pink-600 rounded-full blur-3xl opacity-20 -ml-10 -mb-10"></div>
                        <div class="relative z-10 mb-8">
                            <h3 class="text-xs font-bold text-purple-300 uppercase tracking-widest mb-3">Artificial Intelligence</h3>
                            <div class="bg-purple-800/50 rounded-xl p-4 border border-purple-700 backdrop-blur-sm">
                                <div class="flex justify-between items-center mb-4">
                                    <span class="text-sm font-bold">Deteksi Objek Penyakit</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="aiToggle" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-purple-950 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                                    </label>
                                </div>
                                <div class="mb-2">
                                    <div class="flex justify-between items-center text-[10px] text-purple-300 mb-1">
                                        <span>Confidence</span>
                                        <span id="confidence-percent" class="text-white font-bold">0%</span>
                                    </div>
                                    <div id="confidence-text" class="text-[11px] text-white font-semibold truncate mb-1" title="">-</div>
                                </div>
                                <div class="w-full bg-purple-950 rounded-full h-2">
                                    <div id="confidence-bar" class="bg-gradient-to-r from-pink-500 to-purple-400 h-2 rounded-full transition-all duration-300 w-0"></div>
                                </div>
                            </div>
                        </div>
                        <div class="relative z-10 space-y-8">
                            <div>
                                <h3 class="text-xs font-bold text-purple-300 uppercase tracking-widest mb-4 text-center">Movement (WASD)</h3>
                                <div class="grid grid-cols-3 gap-3 max-w-[180px] mx-auto">
                                    <div></div>
                                    <button id="btn-w" class="h-14 rounded-xl bg-white text-purple-900 font-black text-xl shadow-[0_4px_0_#a855f7] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center">W</button>
                                    <div></div>
                                    
                                    <button id="btn-a" class="h-14 rounded-xl bg-white text-purple-900 font-black text-xl shadow-[0_4px_0_#a855f7] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center">A</button>
                                    <button id="btn-s" class="h-14 rounded-xl bg-white text-purple-900 font-black text-xl shadow-[0_4px_0_#a855f7] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center">S</button>
                                    <button id="btn-d" class="h-14 rounded-xl bg-white text-purple-900 font-black text-xl shadow-[0_4px_0_#a855f7] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center">D</button>
                                </div>
                            </div>
                            <div class="w-full h-px bg-purple-800"></div>
                            <div>
                                <h3 class="text-xs font-bold text-purple-300 uppercase tracking-widest mb-4 text-center">Camera (Arrows)</h3>
                                <div class="grid grid-cols-3 gap-3 max-w-[180px] mx-auto">
                                    <div></div>
                                    <button id="btn-up" class="h-12 rounded-xl bg-purple-700 text-white font-bold text-lg shadow-[0_4px_0_#4c1d95] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center hover:bg-purple-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path></svg>
                                    </button>
                                    <div></div>
                                    <button id="btn-left" class="h-12 rounded-xl bg-purple-700 text-white font-bold text-lg shadow-[0_4px_0_#4c1d95] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center hover:bg-purple-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                    </button>
                                    <button id="btn-down" class="h-12 rounded-xl bg-purple-700 text-white font-bold text-lg shadow-[0_4px_0_#4c1d95] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center hover:bg-purple-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    <button id="btn-right" class="h-12 rounded-xl bg-purple-700 text-white font-bold text-lg shadow-[0_4px_0_#4c1d95] active:translate-y-[4px] active:shadow-none transition-all flex items-center justify-center hover:bg-purple-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.ROBOT_CONFIG = {
            localModelPath: "{{ asset('models/best.onnx') }}",
            localInputSize: 640,
            confidenceThreshold: 0.25,
            detectionIntervalMs: 500,
            localClassSlugs: [
                "aphids",
                "bercak_cercospora",
                "layu_fusarium",
                "phytophthora_blight",
                "powdery_mildew",
                "sehat",
                "mosaic_virus"
            ]
        };
        function updateLatestSummary() {
            import("https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js").then(({ initializeApp }) => {
                import("https://www.gstatic.com/firebasejs/12.6.0/firebase-database.js").then(({ getDatabase, ref, onValue, get }) => {
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
                    const userId = "{{ Auth::id() }}";
                    const userDetectionsRef = ref(database, `detections/${userId}`);
                    
                    onValue(userDetectionsRef, (snapshot) => {
                        const data = snapshot.val();
                        const updateTimeSpan = document.getElementById('summary-updated-time');
                        if (updateTimeSpan) {
                            updateTimeSpan.innerHTML = '<span class="text-yellow-400 animate-pulse">Updating...</span>';
                        }                        
                        if (data) {
                            displayLatestSummary(data);
                        } else {
                            console.log('No data found di Firebase');
                            const summaryDiv = document.getElementById('thirtysec-article');
                            if (summaryDiv) {
                                summaryDiv.innerHTML = `
                                    <div class="text-center">
                                        <p class="text-xs text-gray-400">Belum ada data deteksi</p>
                                    </div>
                                `;
                            }
                        }
                    });
                });
            });
        }
        
        function displayLatestSummary(detectionData) {
            const summaryDiv = document.getElementById('thirtysec-article');
            const updateTimeSpan = document.getElementById('summary-updated-time');
            
            if (!summaryDiv) return;
            console.log('Processing detection data:', detectionData);
            let latestDetection = null;
            let latestTimestamp = 0;
            Object.keys(detectionData).forEach(key => {
                const detection = detectionData[key];
                const timestamp = parseInt(key) || detection.timestamp || detection.created_at || 0;
                if (timestamp > latestTimestamp) {
                    latestTimestamp = timestamp;
                    latestDetection = detection;
                }
            });
            
            console.log('Latest detection found:', latestDetection);
            if (!latestDetection) {
                summaryDiv.innerHTML = `
                    <div class="text-center">
                        <p class="text-xs text-gray-400">Belum ada data deteksi</p>
                    </div>
                `;
                return;
            }
            
            const disease = latestDetection.dominan_disease || latestDetection.label || 'Tidak Dikenali';
            const confidence = latestDetection.confidence || latestDetection.dominan_confidence_avg || 0;
            const sensorData = latestDetection.sensor_rata_rata || {};
            const suhu = sensorData.suhu || 'N/A';
            const kelembapan = sensorData.kelembapan || 'N/A';
            const cahaya = sensorData.cahaya || 'N/A';
            const info = latestDetection.info || {};
            const ciri = info.ciri || 'Tidak ada informasi';
            const rekomendasi = info.rekomendasi_penanganan || 'Tidak ada rekomendasi';
            console.log('Extracted data:', { disease, confidence, suhu, kelembapan, cahaya, ciri, rekomendasi });
            const now = new Date();
            updateTimeSpan.textContent = `Update: ${now.toLocaleTimeString('id-ID')}`;
            summaryDiv.innerHTML = `
                <div class="space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-purple-300">Hasil Deteksi:</span>
                        <span class="text-xs font-bold text-white">${disease.replace(/_/g, ' ')}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-semibold text-purple-300">Keyakinan:</span>
                        <span class="text-xs font-bold text-green-400">${confidence.toFixed(1)}%</span>
                    </div>
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <div class="text-xs font-semibold text-purple-300 mb-1">Data Sensor:</div>
                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="text-center">
                                <div class="text-gray-400">Suhu</div>
                                <div class="font-bold text-white">${suhu}°C</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-400">Kelembapan</div>
                                <div class="font-bold text-white">${kelembapan}%</div>
                            </div>
                            <div class="text-center">
                                <div class="text-gray-400">Intensitas Cahaya</div>
                                <div class="font-bold text-white">${cahaya}Lux</div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <div class="text-xs font-semibold text-purple-300 mb-1">Ciri-ciri:</div>
                        <div class="text-xs text-gray-300">${ciri}</div>
                    </div>
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <div class="text-xs font-semibold text-purple-300 mb-1">Rekomendasi:</div>
                        <div class="text-xs text-gray-300">${rekomendasi}</div>
                    </div>
                </div>
            `;
        }
        document.addEventListener('DOMContentLoaded', function() {
            updateLatestSummary();
        });
    </script>
<script src="{{ asset('assets/ort/ort.min.js') }}"></script>
<script src="{{ asset('js/robot.js') }}"></script>
</x-app-layout>