ort.env.wasm.wasmPath = '/assets/ort/';
ort.env.wasm.numThreads = 4;
const ROBOT_CONFIG = window.ROBOT_CONFIG || {};
const LOCAL_MODEL_PATH = ROBOT_CONFIG.localModelPath || "";
const LOCAL_INPUT_SIZE = ROBOT_CONFIG.localInputSize || 640;
const LOCAL_CONFIDENCE_THRESHOLD = ROBOT_CONFIG.confidenceThreshold ?? 0.3;
const LOCAL_DETECTION_INTERVAL = ROBOT_CONFIG.detectionIntervalMs || 500;

let isModelRunning = false;
let detectionLoopStarted = false;
let lastRecommendationTime = 0;
let isAlertShown = false;
let currentStream = null;
let detectionResults = [];
let localSession = null;
let localModelReady = false;
let localProcessing = false;
let lastLocalRun = 0;
let lastAutoSaveTime = 0;
const AUTO_SAVE_INTERVAL = 30000;

let currentDisease = null;
let diseaseCounts = {
    'aphids': 0,
    'bercak_cercospora': 0,
    'layu_fusarium': 0,
    'mosaic_virus': 0,
    'phytophthora_blight': 0,
    'powdery_mildew': 0,
    'sehat': 0
};
let detectionHistory = [];
const HISTORY_DURATION = 30000;

const CLASS_NAME_DISPLAY = {
    aphids: "Aphids",
    bercak_cercospora: "Bercak Cercospora",
    layu_fusarium: "Layu Fusarium",
    phytophthora_blight: "Phytophthora",
    powdery_mildew: "Powdery Mildew",
    sehat: "Sehat",
    mosaic_virus: "Mosaic Virus"
};

const LOCAL_CLASS_SLUGS = ROBOT_CONFIG.localClassSlugs || Object.keys(CLASS_NAME_DISPLAY);

const videoElement = document.getElementById("webcam");
const canvas = document.getElementById("aiCanvas");
const ctx = canvas.getContext("2d");

const tempCanvasLocal = document.createElement("canvas");
const tempCtxLocal = tempCanvasLocal.getContext("2d");
tempCanvasLocal.width = LOCAL_INPUT_SIZE;
tempCanvasLocal.height = LOCAL_INPUT_SIZE;

document.addEventListener("DOMContentLoaded", setupCanvasOverlay);
window.addEventListener("beforeunload", cleanupCamera);
window.addEventListener("pagehide", cleanupCamera);

if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
    startCamera();
} else {
    log("Browser tidak mendukung kamera.");
}

function setupCanvasOverlay() {
    videoElement.addEventListener("loadedmetadata", () => {
        canvas.width = videoElement.videoWidth;
        canvas.height = videoElement.videoHeight;
    });
    canvas.style.position = "absolute";
    canvas.style.top = "0";
    canvas.style.left = "0";
    canvas.style.zIndex = "10";
    canvas.style.pointerEvents = "none";
}

async function checkCameraPermission() {
    try {
        if (navigator.permissions) {
            const result = await navigator.permissions.query({ name: "camera" });
            if (result.state === "denied") {
                log("Akses kamera ditolak. Mohon izinkan kamera di pengaturan browser.");
                return false;
            }
        }
        return true;
    } catch {
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
                facingMode: "environment",
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
                log(`Resolusi: ${videoElement.videoWidth}x${videoElement.videoHeight}`);
                resolve();
            };
        });
        await videoElement.play();

        document.getElementById("camera-loading").style.display = "none";
        log("Kamera dimulai");
        await initLocalModel();
        ensureDetectionLoop();
    } catch (error) {
        console.error("Kamera error:", error);
        log("Kamera error: " + error.message);
        document.getElementById("camera-loading").innerHTML =
            `<p class="text-red-400 text-sm font-bold">${error.message}</p>`;
    }
}

function cleanupCamera() {
    if (currentStream) {
        currentStream.getTracks().forEach(track => track.stop());
        currentStream = null;
        log("Kamera berhenti");
    }
}

async function initLocalModel() {
    if (localModelReady) return true;
    if (!LOCAL_MODEL_PATH) {
        log("localModelPath belum diset di window.ROBOT_CONFIG.");
        return false;
    }
    if (typeof ort === "undefined") {
        log("Script ort.min.js belum dimuat.");
        return false;
    }

    try {
        document.getElementById("ai-status").innerText = "LOADING...";
        const options = { 
            executionProviders: ["webgl","wasm"], 
            graphOptimizationLevel: "all",
            logSeverityLevel: 3, 
            sessionOptions: {
                    enableCpuMemArena: false,
                    enableMemPattern: false,
                    executionMode: "sequential"
                }
        };
        localSession = await ort.InferenceSession.create(LOCAL_MODEL_PATH, options);
        localModelReady = true;
        isModelRunning = true;
        document.getElementById("ai-status").innerText = "AKTIF";
        document.getElementById("ai-status").className = "text-xs font-bold text-green-400";
        log("Model ONNX berhasil dimuat");
        return true;
    } catch (error) {
        console.error(error);
        log("Gagal memuat model lokal: " + error.message);
        document.getElementById("ai-status").innerText = "GAGAL";
        document.getElementById("ai-status").className = "text-xs font-bold text-red-400";
        return false;
    }
}

function ensureDetectionLoop() {
    if (!detectionLoopStarted) {
        detectionLoopStarted = true;
        window.requestAnimationFrame(loopAI);
    }
}

async function loopAI() {
    const toggle = document.getElementById("aiToggle");
    if (isModelRunning && toggle && toggle.checked) {
        await predictLocal();
    }
    window.requestAnimationFrame(loopAI);
}
/**
 * @param {object} box1 - {x1, y1, x2, y2}
 * @param {object} box2 - {x1, y1, x2, y2}
 * @returns {number} Nilai IOU (0 hingga 1).
 */
function calculateIOU(box1, box2) {
    const xA = Math.max(box1.x1, box2.x1);
    const yA = Math.max(box1.y1, box2.y1);
    const xB = Math.min(box1.x2, box2.x2);
    const yB = Math.min(box1.y2, box2.y2);
    const intersectionWidth = Math.max(0, xB - xA);
    const intersectionHeight = Math.max(0, yB - yA);
    const intersectionArea = intersectionWidth * intersectionHeight;
    const area1 = (box1.x2 - box1.x1) * (box1.y2 - box1.y1);
    const area2 = (box2.x2 - box2.x1) * (box2.y2 - box2.y1);
    return intersectionArea / (area1 + area2 - intersectionArea);
}

/**
 * @param {Array} boxes x1, y1, x2, y2
 * @param {number} iouThreshold 
 * @returns {Array}
 */
function nonMaxSuppression(boxes, iouThreshold) {
    if (!boxes || boxes.length === 0) return [];
    boxes.sort((a, b) => b.confidence - a.confidence);
    const pickedBoxes = [];
    const suppressed = new Array(boxes.length).fill(false);
    for (let i = 0; i < boxes.length; i++) {
        if (suppressed[i]) continue;
        const boxI = boxes[i];
        pickedBoxes.push(boxI);
        for (let j = i + 1; j < boxes.length; j++) {
            if (suppressed[j]) continue;
            const boxJ = boxes[j];
            const iou = calculateIOU(boxI.bbox, boxJ.bbox);
            if (iou > iouThreshold) {
                suppressed[j] = true;
            }
        }
    }
    return pickedBoxes;
}
async function predictLocal() {
    const now = Date.now();
    if (now - lastLocalRun < LOCAL_DETECTION_INTERVAL) return;
    if (localProcessing || !localModelReady) return;
    if (!videoElement || videoElement.readyState < 2) return;
    localProcessing = true;
    lastLocalRun = now;
    try {
        tempCtxLocal.drawImage(videoElement, 0, 0, LOCAL_INPUT_SIZE, LOCAL_INPUT_SIZE);
        const imageData = tempCtxLocal.getImageData(0, 0, LOCAL_INPUT_SIZE, LOCAL_INPUT_SIZE);
        
        const inputSizeSquared = LOCAL_INPUT_SIZE * LOCAL_INPUT_SIZE;
        const input = new Float32Array(3 * inputSizeSquared);
        const pixels = imageData.data;

        for (let i = 0; i < inputSizeSquared; i++) {
            const idx = i * 4;
            input[i] = pixels[idx] / 255.0; 
            input[inputSizeSquared + i] = pixels[idx + 1] / 255.0; 
            input[2 * inputSizeSquared + i] = pixels[idx + 2] / 255.0; 
        }
        const tensor = new ort.Tensor("float32", input, [1, 3, LOCAL_INPUT_SIZE, LOCAL_INPUT_SIZE]);
        const feeds = {};
        feeds[localSession.inputNames[0]] = tensor; 
        const results = await localSession.run(feeds);
        const outputName = localSession.outputNames[0];
        const outputTensor = results[outputName];
        let detections = processLocalOutput(
            outputTensor.data,
            outputTensor.dims,
            videoElement.videoWidth,
            videoElement.videoHeight
        );
        detections = nonMaxSuppression(detections, 0.45);
        applyDetections(detections, { frameWidth: videoElement.videoWidth, frameHeight: videoElement.videoHeight });
        
    } catch (error) {
        console.error("Local inference error:", error);
        console.error("Error stack:", error.stack);
        log("Local inference error: " + (error.message || error));
        log("Error location: " + (error.stack?.split('\n')[1] || 'unknown'));
    } finally {
        localProcessing = false;
    }
}
function processLocalOutput(data, dims, imgWidth, imgHeight) {
    const detections = [];
    if (!data || !dims || dims.length < 3) return detections;
    const rows = dims[1];
    const cols = dims[2];
    const isTransposed = rows > cols;
    const numAnchors = isTransposed ? rows : cols;
    const numClasses = (isTransposed ? cols : rows) - 4;
    const CONF_THRESH = LOCAL_CONFIDENCE_THRESHOLD;
    const scaleX = imgWidth / LOCAL_INPUT_SIZE;
    const scaleY = imgHeight / LOCAL_INPUT_SIZE;
    for (let i = 0; i < numAnchors; i++) {
        let maxScore = 0;
        let maxClassIndex = -1;
        let x, y, w, h;
        let offset;
        if (isTransposed) {
            offset = i * (numClasses + 4);
            const boxScore = Math.max(data[offset], data[offset + 1], data[offset + 2], data[offset + 3]);
            if (boxScore < CONF_THRESH * 0.5) continue;
            for (let c = 0; c < numClasses; c++) {
                const score = data[offset + 4 + c];
                if (score > maxScore) {
                    maxScore = score;
                    maxClassIndex = c;
                }
            }
            if (maxScore > CONF_THRESH) {
                x = data[offset];
                y = data[offset + 1];
                w = data[offset + 2];
                h = data[offset + 3];
            }
        } else {
            const stride = numAnchors;
            offset = i;
            const boxScore = Math.max(data[i], data[stride + i], data[2 * stride + i], data[3 * stride + i]);
            if (boxScore < CONF_THRESH * 0.5) continue;
            for (let c = 0; c < numClasses; c++) {
                const score = data[(4 + c) * stride + i];
                if (score > maxScore) {
                    maxScore = score;
                    maxClassIndex = c;
                }
            }
            if (maxScore > CONF_THRESH) {
                x = data[i];
                y = data[stride + i];
                w = data[2 * stride + i];
                h = data[3 * stride + i];
            }
        }

        if (maxScore <= CONF_THRESH || maxClassIndex === -1) continue;
        let x1 = (x - w / 2) * scaleX;
        let y1 = (y - h / 2) * scaleY;
        let wScaled = w * scaleX;
        let hScaled = h * scaleY;
        x1 = clamp(x1, 0, imgWidth);
        y1 = clamp(y1, 0, imgHeight);
        wScaled = clamp(wScaled, 0, imgWidth);
        hScaled = clamp(hScaled, 0, imgHeight);

        const bbox = {
            x1,
            y1,
            x2: clamp(x1 + wScaled, 0, imgWidth),
            y2: clamp(y1 + hScaled, 0, imgHeight),
            width: wScaled,
            height: hScaled
        };
        detections.push({
            class: LOCAL_CLASS_SLUGS[maxClassIndex] || `class_${maxClassIndex}`,
            confidence: maxScore,
            bbox
        });
    }
    return detections;
}

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function applyDetections(det, meta = {}) {
    if (typeof meta.frameWidth === "number") canvas.dataset.lastWidth = meta.frameWidth;
    if (typeof meta.frameHeight === "number") canvas.dataset.lastHeight = meta.frameHeight;
    detectionResults = det;
    updateDiseaseCounting(det);
    drawBoundingBoxes(det);
    updateUIFromDetections(det);
}

function updateDiseaseCounting(det) {
    const now = Date.now();
    detectionHistory = detectionHistory.filter(item => now - item.timestamp < HISTORY_DURATION);
    if (det.length === 0) {
        currentDisease = null;
        console.log('[DISEASE COUNT] No detection');
        return;
    }
    const topDetection = det.reduce((a, b) => (a.confidence > b.confidence ? a : b));
    const detectedClass = topDetection.class;
    currentDisease = detectedClass;
    detectionHistory.push({
        disease: detectedClass,
        timestamp: now,
        confidence: topDetection.confidence
    });
    recalculateDiseaseCounts();
    console.log('[DISEASE COUNT] Current:', diseaseCounts);
}

function recalculateDiseaseCounts() {
    Object.keys(diseaseCounts).forEach(key => {
        diseaseCounts[key] = 0;
    });
    detectionHistory.forEach(item => {
        if (diseaseCounts.hasOwnProperty(item.disease)) {
            diseaseCounts[item.disease]++;
        }
    });
}

function getDisplayName(slug) {
    return CLASS_NAME_DISPLAY[slug] || slug.replace(/_/g, " ").replace(/\b\w/g, c => c.toUpperCase());
}
function drawBoundingBoxes(det) {
    const lastWidth = Number(canvas.dataset.lastWidth) || videoElement.videoWidth || 1;
    const lastHeight = Number(canvas.dataset.lastHeight) || videoElement.videoHeight || 1;
    const scaleX = canvas.width / lastWidth;
    const scaleY = canvas.height / lastHeight;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    det.forEach(d => {
        const bbox = d.bbox;
        if (!bbox) return;
        const x1 = (bbox.x1 || 0) * scaleX;
        const y1 = (bbox.y1 || 0) * scaleY;
        const width = (bbox.width || (bbox.x2 - bbox.x1) || 0) * scaleX;
        const height = (bbox.height || (bbox.y2 - bbox.y1) || 0) * scaleY;
        const name = getDisplayName(d.class);
        const confPercent = Math.round(d.confidence * 100);
        ctx.strokeStyle = "#8B5CF6";
        ctx.lineWidth = 3;
        ctx.strokeRect(x1, y1, width, height);
        ctx.fillStyle = "#8B5CF6";
        ctx.fillRect(x1, y1 - 22, ctx.measureText(name + " " + confPercent + "%").width + 20, 20);
        ctx.fillStyle = "#fff";
        ctx.fillText(`${name} ${confPercent}%`, x1 + 5, y1 - 7);
    });
}

function updateUIFromDetections(det) {
    if (!det.length) {
        document.getElementById("confidence-text").innerText = "-";
        document.getElementById("confidence-percent").innerText = "0";
        document.getElementById("confidence-bar").style.width = "0%";
        return;
    }
    const top = det.reduce((a, b) => (a.confidence > b.confidence ? a : b));
    const percent = Math.round(top.confidence * 100);
    document.getElementById("confidence-text").innerText = getDisplayName(top.class);
    document.getElementById("confidence-percent").innerText = `${percent}%`;
    document.getElementById("confidence-bar").style.width = `${percent}%`;

}
setInterval(() => {
    resetDiseaseCounts();
}, 30000);

function resetDiseaseCounts() {
    const countsBeforeReset = {...diseaseCounts};
    if (Object.values(countsBeforeReset).some(count => count > 0)) {
        const detections = Object.entries(countsBeforeReset)
            .filter(([disease, count]) => count > 0)
            .map(([disease, count]) => ({
                class: disease,
                confidence: 0.8,
                count: count
            }));
        autoSaveDetection(detections, countsBeforeReset);
    }
    Object.keys(diseaseCounts).forEach(key => {
        diseaseCounts[key] = 0;
    });
    detectionHistory = [];
    console.log('[DISEASE COUNT] save after 30s');
}

function showAlert(data) {
    isAlertShown = true;
    const box = document.getElementById("product-alert");
    document.getElementById("rec-image").src = data.image;
    document.getElementById("rec-name").innerText = data.name;
    document.getElementById("rec-price").innerText = data.price;
    document.getElementById("rec-link").href = data.link;
    box.classList.remove("hidden");
    setTimeout(() => box.classList.remove("translate-y-20", "opacity-0"), 50);
}

function closeAlert() {
    const box = document.getElementById("product-alert");
    box.classList.add("translate-y-20", "opacity-0");
    setTimeout(() => { box.classList.add("hidden"); isAlertShown = false; }, 400);
}


async function autoSaveDetection(detections, countsToSave) {
    try {
        let sensorData = {};
        try {
            const sensorResponse = await fetch(window.ROBOT_API_URL + '/sensor/data');
            if (sensorResponse.ok) {
                const sensorResult = await sensorResponse.json();
                if (sensorResult.success && sensorResult.data) {
                    const timestamps = Object.keys(sensorResult.data);
                    if (timestamps.length > 0) {
                        const latestTimestamp = timestamps[timestamps.length - 1];
                        const latestData = sensorResult.data[latestTimestamp];
                        sensorData = {
                            suhu: latestData.suhu,
                            kelembapan: latestData.kelembapan,
                            cahaya: latestData.cahaya
                        };
                    }
                }
            }
        } catch (e) {
            sensorData = {
                suhu: 26.9,
                kelembapan: 83.5,
                cahaya: 42
            };
        }
        const dominantDisease = Object.entries(countsToSave)
            .filter(([disease, count]) => count > 0)
            .sort(([,a], [,b]) => b - a)[0]?.[0] || 'sehat';
        
        const payload = {
            suhu: sensorData.suhu,
            kelembapan: Math.round(sensorData.kelembapan),
            cahaya: Math.round(sensorData.cahaya),
            save: true,
            user_id: window.authUserId || 'autoSimpan',
            dominan_disease: dominantDisease,
            disease_counts: countsToSave
        };
        console.log('[AUTO SAVE] check payload:', JSON.stringify(payload, null, 2));
        const saveResponse = await fetch(window.ROBOT_API_URL + '/detect/auto', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });
        
        if (saveResponse.ok) {
            const result = await saveResponse.json();
            console.log('[AUTO SAVE] Berhasil:', result);
        } else {
            console.error('[AUTO SAVE] Berhasil:', saveResponse.statusText);
        }
    } catch (error) {
        console.error('[AUTO SAVE] Error:', error);
    }
}

function log(msg) {
    const div = document.getElementById("console-log");
    if (!div) return;
    const p = document.createElement("p");
    p.innerText = "> " + msg;
    div.appendChild(p);
    div.scrollTop = div.scrollHeight;
}

const keyMap = {
    w: "btn-w",
    a: "btn-a",
    s: "btn-s",
    d: "btn-d",
    arrowup: "btn-up",
    arrowdown: "btn-down",
    arrowleft: "btn-left",
    arrowright: "btn-right"
};

const actionMap = {
    w: "Engine: FWD",
    a: "Engine: LEFT",
    s: "Engine: REV",
    d: "Engine: RIGHT",
    arrowup: "Cam: UP",
    arrowdown: "Cam: DOWN",
    arrowleft: "Cam: LEFT",
    arrowright: "Cam: RIGHT"
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
