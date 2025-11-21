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
                <span class="text-xs font-bold text-gray-700 tracking-wide">CONNECTED: <span class="text-purple-700">NODE-X1</span></span>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-[#F3F0FF] min-h-screen"> <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <div class="lg:col-span-8 space-y-6">
                    
                    <div class="relative bg-black rounded-3xl overflow-hidden shadow-2xl border-4 border-purple-900 group">
                        
                        <video id="webcam" autoplay playsinline class="w-full h-[500px] object-cover opacity-90"></video>
                        
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
                                FPS: <span id="fps-counter">60</span>
                            </div>
                        </div>

                        <div id="product-alert" class="absolute bottom-6 right-6 z-40 hidden transition-all duration-500 transform translate-y-20 opacity-0">
                            <div class="bg-white border-2 border-purple-500 text-gray-800 p-4 rounded-2xl shadow-2xl max-w-xs flex gap-4 items-center">
                                <img id="rec-image" src="" class="w-14 h-14 object-cover rounded-lg border border-gray-200">
                                <div>
                                    <p class="text-[10px] font-extrabold text-purple-600 uppercase tracking-wider animate-pulse">⚠️ PENYAKIT TERDETEKSI</p>
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
                        <div id="console-log" class="h-20 overflow-y-auto space-y-1 text-xs font-mono text-gray-400 scrollbar-thin scrollbar-thumb-gray-700">
                            <p>> Connecting to Camera Module...</p>
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
                                    <span class="text-sm font-bold">Object Detection</span>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" id="aiToggle" class="sr-only peer" checked>
                                        <div class="w-11 h-6 bg-purple-950 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-purple-500"></div>
                                    </label>
                                </div>
                                
                                <div class="flex justify-between text-[10px] text-purple-300 mb-1">
                                    <span>Confidence</span>
                                    <span id="confidence-text" class="text-white font-bold">0%</span>
                                </div>
                                <div class="w-full bg-purple-950 rounded-full h-2">
                                    <div id="confidence-bar" class="bg-gradient-to-r from-pink-500 to-purple-400 h-2 rounded-full transition-all duration-300 w-0"></div>
                                </div>

                                <button onclick="simpanDeteksi()" class="mt-4 w-full bg-white text-purple-900 text-xs font-bold py-2.5 rounded-lg hover:bg-gray-200 transition shadow-lg flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                                    Simpan Hasil Deteksi
                                </button>
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

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>

    <script>
        // LINK MODEL AI KAMU
        const URL = "https://teachablemachine.withgoogle.com/models/U-JrWFd_Y/"; 
        
        let model, webcam, labelContainer, maxPredictions;
        let isModelRunning = false;
        let lastRecommendationTime = 0;
        let isAlertShown = false;

        const videoElement = document.getElementById('webcam');
        const canvas = document.getElementById('aiCanvas');
        const ctx = canvas.getContext('2d');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function (stream) {
                    videoElement.srcObject = stream;
                    videoElement.onloadedmetadata = () => {
                        canvas.width = videoElement.videoWidth;
                        canvas.height = videoElement.videoHeight;
                        initAI();
                    };
                })
                .catch(function (error) { console.error(error); });
        }

        async function initAI() {
            const modelURL = URL + "model.json";
            const metadataURL = URL + "metadata.json";
            log("Connecting to AI Cloud...");
            try {
                model = await tmImage.load(modelURL, metadataURL);
                maxPredictions = model.getTotalClasses();
                log("AI Loaded ✅");
                document.getElementById('ai-status').innerText = "ACTIVE";
                document.getElementById('ai-status').className = "text-xs font-bold text-green-400";
                isModelRunning = true;
                window.requestAnimationFrame(loopAI);
            } catch (e) { console.error(e); }
        }

        async function loopAI() {
            if(isModelRunning && document.getElementById('aiToggle').checked){ await predict(); }
            window.requestAnimationFrame(loopAI);
        }

        async function predict() {
            const prediction = await model.predict(videoElement);
            let highestProb = 0;
            let bestClass = "Unknown";
            for (let i = 0; i < maxPredictions; i++) {
                if (prediction[i].probability > highestProb) {
                    highestProb = prediction[i].probability;
                    bestClass = prediction[i].className;
                }
            }
            updateUI(bestClass, highestProb);
        }

        function updateUI(className, probability) {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            let color = '#9ca3af'; 
            let text = className;

            if (text.toLowerCase().includes('sehat')) color = '#4ade80'; 
            else if (text.toLowerCase().includes('bercak')) color = '#facc15'; 
            else if (text.toLowerCase().includes('layu')) color = '#f43f5e'; 

            if (probability < 0.75 || text.toLowerCase().includes('background')) {
                document.getElementById('confidence-text').innerText = "Scanning...";
                document.getElementById('confidence-bar').style.width = "0%";
                return;
            }

            // REKOMENDASI
            if (!text.toLowerCase().includes('sehat') && !isAlertShown) {
                const now = Date.now();
                if (now - lastRecommendationTime > 5000) {
                    lastRecommendationTime = now;
                    let keyword = text.split('(')[0].trim(); 
                    fetch(`/api/product-recommendation?label=${keyword}`)
                        .then(res => res.json())
                        .then(data => { if(data.found) showAlert(data); });
                }
            }

            const percent = (probability * 100).toFixed(0);
            
            // UPDATE UI Control Panel
            document.getElementById('confidence-text').innerText = `${text} (${percent}%)`;
            document.getElementById('confidence-bar').style.width = `${percent}%`;
            document.getElementById('confidence-bar').style.backgroundColor = color;

            // DRAW BOX
            const w = 250; const h = 250;
            const x = (canvas.width - w) / 2;
            const y = (canvas.height - h) / 2;
            const len = 40;
            ctx.lineWidth = 4;
            ctx.strokeStyle = color;
            ctx.shadowColor = color;
            ctx.shadowBlur = 15;
            ctx.beginPath();
            ctx.moveTo(x, y + len); ctx.lineTo(x, y); ctx.lineTo(x + len, y);
            ctx.moveTo(x + w - len, y); ctx.lineTo(x + w, y); ctx.lineTo(x + w, y + len);
            ctx.moveTo(x, y + h - len); ctx.lineTo(x, y + h); ctx.lineTo(x + len, y + h);
            ctx.moveTo(x + w - len, y + h); ctx.lineTo(x + w, y + h); ctx.lineTo(x + w, y + h - len);
            ctx.stroke();
            ctx.shadowBlur = 0;
            ctx.fillStyle = color;
            ctx.font = "bold 20px sans-serif";
            ctx.fillText(`${text.toUpperCase()} ${percent}%`, x + 10, y - 15);
        }

        function showAlert(data) {
            isAlertShown = true;
            const alertBox = document.getElementById('product-alert');
            document.getElementById('rec-image').src = data.image;
            document.getElementById('rec-name').innerText = data.name;
            document.getElementById('rec-price').innerText = data.price;
            document.getElementById('rec-link').href = data.link;
            alertBox.classList.remove('hidden');
            setTimeout(() => { alertBox.classList.remove('translate-y-20', 'opacity-0'); }, 100);
        }

        function closeAlert() {
            const alertBox = document.getElementById('product-alert');
            alertBox.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => { alertBox.classList.add('hidden'); isAlertShown = false; }, 500);
        }

        function log(msg) {
            const consoleDiv = document.getElementById('console-log');
            const p = document.createElement('p');
            p.innerText = `> ${msg}`;
            consoleDiv.appendChild(p);
            consoleDiv.scrollTop = consoleDiv.scrollHeight;
        }

        function simpanDeteksi() {
            let labelRaw = document.getElementById('confidence-text').innerText;
            if(labelRaw == 'Waiting...' || labelRaw == 'Scanning...') { alert("Belum ada deteksi valid!"); return; }
            let cleanLabel = labelRaw.split('(')[0].trim();
            let confidence = labelRaw.match(/\d+/)[0];
            fetch("{{ route('history.store_detection') }}", {
                method: "POST",
                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: JSON.stringify({ label: cleanLabel, confidence: confidence })
            }).then(res => { alert("Tersimpan! ✅"); });
        }

        // KEYBOARD VISUAL FEEDBACK (UPDATED FOR DARK THEME)
        const keyMap = { 'w': 'btn-w', 'a': 'btn-a', 's': 'btn-s', 'd': 'btn-d', 'arrowup': 'btn-up', 'arrowdown': 'btn-down', 'arrowleft': 'btn-left', 'arrowright': 'btn-right' };
        const actionMap = { 'w': 'Engine: FWD', 'a': 'Engine: LEFT', 's': 'Engine: REV', 'd': 'Engine: RIGHT', 'arrowup': 'Cam: UP', 'arrowdown': 'Cam: DOWN', 'arrowleft': 'Cam: LEFT', 'arrowright': 'Cam: RIGHT' };

        document.addEventListener('keydown', (e) => {
            const key = e.key.toLowerCase();
            if (keyMap[key]) {
                const btn = document.getElementById(keyMap[key]);
                if(btn) {
                    // Efek Tekan: Hilangkan shadow, turunkan tombol
                    btn.classList.remove('shadow-[0_4px_0_#a855f7]', 'shadow-[0_4px_0_#4c1d95]');
                    btn.classList.add('translate-y-[4px]', 'shadow-none', 'brightness-90'); 
                    log(actionMap[key]);
                }
            }
        });

        document.addEventListener('keyup', (e) => {
            const key = e.key.toLowerCase();
            if (keyMap[key]) {
                const btn = document.getElementById(keyMap[key]);
                if(btn) {
                    // Balikin Efek Timbul
                    btn.classList.remove('translate-y-[4px]', 'shadow-none', 'brightness-90');
                    if(key.includes('arrow')) btn.classList.add('shadow-[0_4px_0_#4c1d95]');
                    else btn.classList.add('shadow-[0_4px_0_#a855f7]');
                }
            }
        });
    </script>
</x-app-layout>