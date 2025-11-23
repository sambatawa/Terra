let isModelRunning = false;
let lastRecommendationTime = 0;
let isAlertShown = false;

const videoElement = document.getElementById('webcam');
const canvas = document.getElementById('aiCanvas');
const ctx = canvas.getContext('2d');
let currentStream = null;
window.ROBOT_API_URL = "http://localhost:8001"; 
window.ROBOT_SAVE_URL = "/detect";      
window.CSRF_TOKEN = "{{ csrf_token() }}";

//CAMERA HANDLING
async function checkCameraPermission() {
    try {
        if (navigator.permissions) {
            const result = await navigator.permissions.query({ name: 'camera' });
            if (result.state === 'denied') {
                log("camera akses tidak ada, silahkan izinkan di settingan browser");
                return false;
            }
        }
        return true;
    } catch (e) {
        return true;
    }
}

async function startCamera() {
    try {
        const hasPermission = await checkCameraPermission();
        if (!hasPermission) return;
        log("Requesting camera access...");
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'environment',
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        });
        currentStream = stream;
        videoElement.srcObject = stream;
        await new Promise(resolve => {
            videoElement.onloadedmetadata = () => {
                canvas.width = videoElement.videoWidth;
                canvas.height = videoElement.videoHeight;
                log(`Video ready: ${videoElement.videoWidth}x${videoElement.videoHeight}`);
                resolve();
            };
        });
        await videoElement.play();

        document.getElementById('camera-loading').style.display = 'none';
        log("Camera started");
        initAI();

    } catch (error) {
        console.error('Camera error:', error);
        log("Camera error: " + error.message);
        document.getElementById('camera-loading').innerHTML =
            `<p class="text-red-400 text-sm font-bold">${error.message}</p>`;
    }
}

function cleanupCamera() {
    if (currentStream) {
        currentStream.getTracks().forEach(t => t.stop());
        currentStream = null;
        log("Camera stopped");
    }
}

window.addEventListener('beforeunload', cleanupCamera);
window.addEventListener('pagehide', cleanupCamera);

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    startCamera();
} else {
    log("Browser tidak mendukung kamera.");
}

//YOLO API INISIALISASI
async function initAI() {
    log("Connecting to YOLO backend...");
    try {
        const healthCheck = await fetch(window.ROBOT_API_URL + "/health");
        if (!healthCheck.ok) throw new Error("Backend offline");

        const health = await healthCheck.json();
        log("Backend Connected - Realtime endpoint ready");

        document.getElementById('ai-status').innerText = "ACTIVE";
        document.getElementById('ai-status').className = "text-xs font-bold text-green-400";
        isModelRunning = true;
        window.requestAnimationFrame(loopAI);
        startAutoSave();
        startSummaryPolling();
    } catch (e) {
        log("Backend Error: " + (e.message || e));
        document.getElementById('ai-status').innerText = "OFFLINE";
        document.getElementById('ai-status').className = "text-xs font-bold text-red-400";

        if ((e.message || "").toLowerCase().includes("backend offline")) {
            log("Pastikan main.py berjalan di http://localhost:8001");
        }
    }
}

//FRAME LOOP + PREDIKSI
let lastFrameTime = 0;
const frameInterval = 1000 / 30;
let detectionResults = [];
let isProcessing = false;
let errorCount = 0;
const MAX_ERROR = 3;
const MAX_IMAGE_SIZE = 416;
const REQUEST_TIMEOUT = 15000;

async function loopAI() {
    if (isModelRunning && document.getElementById('aiToggle').checked) {
        await predict();
    }
    window.requestAnimationFrame(loopAI);
}

async function predict() {
    const now = Date.now();
    if (now - lastFrameTime < frameInterval) return;
    if (isProcessing) return;
    if (errorCount >= MAX_ERROR) return;
    if (!videoElement || !currentStream || videoElement.readyState < 2) return;
    if (!isModelRunning) {
        return;
    }
    lastFrameTime = now;
    isProcessing = true;

    const tempCanvas = document.createElement('canvas');
    const tempCtx = tempCanvas.getContext('2d');
    let w = videoElement.videoWidth;
    let h = videoElement.videoHeight;
    if (w > MAX_IMAGE_SIZE || h > MAX_IMAGE_SIZE) {
        const scale = Math.min(MAX_IMAGE_SIZE / w, MAX_IMAGE_SIZE / h);
        w = Math.floor(w * scale);
        h = Math.floor(h * scale);
    }

    tempCanvas.width = w;
    tempCanvas.height = h;
    tempCtx.drawImage(videoElement, 0, 0, w, h);
    tempCanvas.toBlob(async blob => {
        let timeoutId = null;
        const controller = new AbortController();
        try {
            const formData = new FormData();
            formData.append('file', blob, 'frame.jpg');
            formData.append('frame_w', String(w));
            formData.append('frame_h', String(h));
            timeoutId = setTimeout(() => {
                controller.abort();
            }, REQUEST_TIMEOUT);

            const response = await fetch(window.ROBOT_API_URL + "/detect/realtime", {
                method: "POST",
                body: formData,
                signal: controller.signal
            });
            clearTimeout(timeoutId);
            timeoutId = null;

            if (!response.ok) {
                let text = "";
                try { text = await response.text(); } catch (e) { text = "(no body)"; }
                console.error("detect/realtime error:", response.status, text);
                log(`Backend error ${response.status}: ${text.substring(0,200)}`);
                errorCount++;
                detectionResults = [];
                drawBoundingBoxes(detectionResults);
            } else {
                errorCount = 0;
                const data = await response.json();
                console.log("realtime response:", data);
                detectionResults = data.detections || [];
                drawBoundingBoxes(detectionResults);
                updateUIFromDetections(detectionResults);
            }
        } catch (err) {
            if (err && err.name === 'AbortError') {
                console.warn("Fetch aborted (timeout or manual):", err);
                log("Request timeout: backend took too long or request aborted");
            } else {
                console.error("Fetch error:", err);
                log("Network/timeout error: " + (err.message || err));
            }
            errorCount++;
            detectionResults = [];
            drawBoundingBoxes(detectionResults);
        } finally {
            if (timeoutId) { clearTimeout(timeoutId); }
            isProcessing = false;
        }
    }, "image/jpeg", 0.3);
}

//BOUNDING BOX DRAWING
function getDisplayName(name) {
    const names = {
        sehat: "Sehat",
        bercak_cercospora: "Bercak Cercospora",
        layu_fusarium: "Layu Fusarium",
        mosaic_virus: "Mosaic Virus",
        aphids: "Aphids",
        phytophthora_blight: "Phytophthora",
        powdery_mildew: "Powdery Mildew"
    };
    return names[name] || name.replace(/_/g, " ").replace(/\b\w/g, l => l.toUpperCase());
}

function drawBoundingBoxes(det) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    det.forEach(d => {
        const { x1, y1, width, height } = d.bbox; 
        const name = getDisplayName(d.class);
        const confDecimal = Number(d.confidence).toFixed(2);
        const confPercent = Math.round(d.confidence * 100);

        ctx.strokeStyle = "#8B5CF6";
        ctx.lineWidth = 3;
        ctx.strokeRect(x1, y1, width, height);
        ctx.fillStyle = "#8B5CF6";
        ctx.fillRect(x1, y1 - 22, ctx.measureText(name + " " + confDecimal).width + 20, 20);
        ctx.fillStyle = "#fff";
        ctx.fillText(`${name} ${confDecimal}`, x1 + 5, y1 - 7);
    });
}

//REKOMENDASI POPUP
function updateUIFromDetections(det) {
    if (!det.length) return;
    const top = det.reduce((a, b) => (a.confidence > b.confidence ? a : b));
    const confDecimal = Number(top.confidence).toFixed(2);
    const percent = Math.round(top.confidence * 100);
    document.getElementById("confidence-text").innerText = getDisplayName(top.class);
    document.getElementById("confidence-percent").innerText = confDecimal;
    document.getElementById("confidence-bar").style.width = percent + "%";
    if (!top.class.includes("sehat") && !isAlertShown) {
        const now = Date.now();
        if (now - lastRecommendationTime > 5000) {
            lastRecommendationTime = now;
            fetch(`/api/product-recommendation?label=${top.class}`)
                .then(r => r.json())
                .then(d => { if (d.found) showAlert(d); });
        }
    }
}

function showAlert(data) {
    isAlertShown = true;
    const box = document.getElementById('product-alert');
    document.getElementById('rec-image').src = data.image;
    document.getElementById('rec-name').innerText = data.name;
    document.getElementById('rec-price').innerText = data.price;
    document.getElementById('rec-link').href = data.link;

    box.classList.remove('hidden');
    setTimeout(() => box.classList.remove('translate-y-20', 'opacity-0'), 50);
}

function closeAlert() {
    const box = document.getElementById('product-alert');
    box.classList.add('translate-y-20', 'opacity-0');
    setTimeout(() => { box.classList.add('hidden'); isAlertShown = false; }, 400);
}

//SAVE DETEKSI
function simpanDeteksi() {
    if (!detectionResults.length) return alert("Belum ada deteksi!");
    canvas.toBlob(blob => {
        const formData = new FormData();
        formData.append('file', blob, 'manual_save.jpg'); 
        formData.append('suhu', 26.9);
        formData.append('kelembapan', 83.5);
        formData.append('cahaya', 42);
        formData.append('user_id', 'manual_user');

        //PERBAIKAN 3: Gunakan ROBOT_API_URL + ROBOT_SAVE_URL
        fetch(window.ROBOT_API_URL + window.ROBOT_SAVE_URL, {
            method: "POST",
            headers: {},
            body: formData
        })
            .then(r => r.json())
            .then(d => {
                if (d.dominan_disease) {
                    alert(`Tersimpan! Deteksi: ${getDisplayName(d.dominan_disease)}`);
                    log("Saved manually");
                } else {
                    alert("Gagal menyimpan. Cek console untuk error backend.");
                    log("Manual save failed.");
                }
            })
            .catch(e => {
                log("Error manual save: " + e.message);
                console.error("Error manual save:", e);
            });
    }, "image/jpeg", 0.9);
}

let autoSaveInterval;
const AUTO_SAVE_INTERVAL = 30000;

function startAutoSave() {
    if (autoSaveInterval) clearInterval(autoSaveInterval);
    autoSaveInterval = setInterval(async () => {
        if (!isModelRunning) return;
        log("Auto-save in progress...");
        try {
            const canvas = document.createElement('canvas');
            canvas.width = videoElement.videoWidth;
            canvas.height = videoElement.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(videoElement, 0, 0, canvas.width, canvas.height);
            
            const formData = new FormData();
            canvas.toBlob(async (blob) => {
                formData.append('file', blob, 'snapshot.jpg');
                formData.append('save', 'true');
                
                const response = await fetch(`${window.ROBOT_API_URL}/detect/auto?save=true`, {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    let txt = "";
                    try { txt = await response.text(); } catch (e) { txt = "(no body)"; }
                    log(`Auto-save failed ${response.status}: ${txt.substring(0,200)}`);
                    return;
                }
                const result = await response.json();
                if (result.saved_this_frame) {
                    log("Auto-save berhasil!");
                } else {
                    log(`Auto-save dilewati: ${result.skip_reason || 'tidak diketahui'}`);
                }
            }, 'image/jpeg');
            
        } catch (error) {
            console.error('Auto-save error:', error);
            log(`Auto-save error: ${error.message}`);
        }
    }, AUTO_SAVE_INTERVAL);
}
//SUMMARY POLLING RINGKASAN DETEKSI
function updateSummaryUI(content, type = "info") {
    const summaryElement = document.getElementById('thirtysec-article');
    if (!summaryElement) {
        console.error("Element 'thirtysec-article' not found!");
        return;
    }
    summaryElement.innerHTML = '';
    
    if (typeof content === 'string' && !content.includes('<')) {
        let className = "text-xs ";
        let icon = "";
        switch(type) {
            case "loading":
                className += "text-yellow-400";
                icon = '<div class="summary-loading mx-auto mb-2"></div>';
                break;
            case "error":
                className += "text-red-400";
                icon = '❌ ';
                break;
            case "empty":
                className += "text-white-500";
                icon = 'ℹ️ ';
                break;
            case "success":
                className += "text-gray-300";
                break;
            default:
                className += "text-gray-400";
        }
        
        summaryElement.innerHTML = `
            <div class="text-center py-4">
                ${icon}
                <p class="${className}">${content}</p>
            </div>
        `;
    } else {
        summaryElement.innerHTML = content;
    }
}

function displayFastAPISummaryData(data) {
    const latest = data.detections && data.detections.length > 0 ? data.detections[0] : null;
    if (!latest) {
        updateSummaryUI("Belum ada data deteksi tersimpan.", "empty");
        return;
    }
    const timestamp = new Date(latest.timestamp).toLocaleTimeString('id-ID');
    let summaryHTML = `
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-xs font-semibold text-purple-400">30s...</span>
                <span class="text-xs text-gray-400">${timestamp}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-white font-medium">
                    ${getDisplayName(latest.dominan_disease)}
                </span>
                <span class="text-xs px-2 py-1 rounded-full ${latest.status === 'sehat' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'}">
                    ${latest.status.toUpperCase()}
                </span>
            </div>
            <div class="text-xs text-gray-300">
                Confidence: ${(latest.dominan_confidence_avg * 100).toFixed(1)}%
            </div>
    `;
    
    if (latest.info) {
        summaryHTML += `
            <div class="mt-2 pt-2 border-t border-gray-600">
                <div class="text-xs text-purple-200 font-semibold">Ciri-ciri:</div>
                <div class="text-xs text-gray-300 mt-1">${latest.info.ciri || 'Tidak tersedia'}</div>
                <div class="text-xs text-purple-200 font-semibold mt-2">Rekomendasi:</div>
                <div class="text-xs text-gray-300 mt-1">${latest.info.rekomendasi_penanganan || 'Tidak tersedia'}</div>
            </div>
        `;
    }
    summaryHTML += `</div>`;
    updateSummaryUI(summaryHTML, "success");
}

//RINGKASAN
function startSummaryPolling() {
    console.log("Start Loop hasil deteksi dan rekomendasi...");
    updateSummaryUI("Memuat ringkasan deteksi terbaru...", "loading");

    fetch(window.ROBOT_API_URL + '/detections?limit=1')
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Latest detection data:", data);
            
            if (data.detections && data.detections.length > 0) {
                displayFastAPISummaryData(data);
            } else {
                updateSummaryUI("Belum ada data deteksi tersimpan di database.", "empty");
            }
        })
        .catch(error => {
            console.error("Error fetching detection summary:", error);
            updateSummaryUI("Gagal memuat ringkasan. Cek koneksi backend.", "error");
        });
}
//LOGGING
function log(msg) {
    const div = document.getElementById('console-log');
    const p = document.createElement('p');
    p.innerText = "> " + msg;
    div.appendChild(p);
    div.scrollTop = div.scrollHeight;
}

//KEYBOARD VISUAL FEEDBACK
const keyMap = {
    w: "btn-w",
    a: "btn-a",
    s: "btn-s",
    d: "btn-d",
    arrowup: "btn-up",
    arrowdown: "btn-down",
    arrowleft: "btn-left",
    arrowright: "btn-right",
};

const actionMap = {
    w: "Engine: FWD",
    a: "Engine: LEFT",
    s: "Engine: REV",
    d: "Engine: RIGHT",
    arrowup: "Cam: UP",
    arrowdown: "Cam: DOWN",
    arrowleft: "Cam: LEFT",
    arrowright: "Cam: RIGHT",
};

document.addEventListener("keydown", e => {
    const key = e.key.toLowerCase();
    if (keyMap[key]) {
        const btn = document.getElementById(keyMap[key]);
        btn?.classList.add("translate-y-[4px]", "shadow-none");
        log(actionMap[key]);
    }
});

document.addEventListener("keyup", e => {
    const key = e.key.toLowerCase();
    if (keyMap[key]) {
        const btn = document.getElementById(keyMap[key]);
        btn?.classList.remove("translate-y-[4px]", "shadow-none");
    }
});