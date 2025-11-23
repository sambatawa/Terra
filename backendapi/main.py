from fastapi import FastAPI, File, UploadFile, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from pydantic import BaseModel
from typing import Optional, Dict, List
import cv2
import numpy as np
from PIL import Image
import io
import os
from datetime import datetime, timezone
import json
import re
from pathlib import Path
import onnxruntime as ort
from ultralytics import YOLO
import asyncio
from dotenv import load_dotenv
from groq import Groq
from contextlib import asynccontextmanager
import firebase_admin
from firebase_admin import credentials, db as firebase_db
import traceback, sys, logging
import base64
import time 

# --- KONFIGURASI DAN INISIALISASI ---
ENV_PATH = Path(__file__).parent / ".env"
load_dotenv(dotenv_path=str(ENV_PATH))

FIREBASE_CONFIG = {"databaseURL": "https://terra-145a1-default-rtdb.asia-southeast1.firebasedatabase.app"}
FIREBASE_CREDENTIALS_PATH = os.getenv("FIREBASE_CREDENTIALS_PATH") or Path(__file__).parent / "firebase-credentials.json"
FIREBASE_DB_URL = FIREBASE_CONFIG.get("databaseURL", "https://terra-145a1-default-rtdb.asia-southeast1.firebasedatabase.app/")

firebase_app = None
last_firebase_error = None

#FIREBASE
def initialize_firebase():
    global firebase_app, last_firebase_error
    try:
        if firebase_app is not None:
            print("[FIREBASE] already initialized")
            return
        try:
            existing = firebase_admin.get_app()
            firebase_app = existing
            print("[FIREBASE] using existing default app")
            return
        except ValueError:
            pass
        
        cred_path = FIREBASE_CREDENTIALS_PATH
        cred_path = Path(cred_path) if isinstance(cred_path, str) else cred_path
        print(f"[CREDENTIAL] path = {cred_path.resolve()}")
        if not cred_path.exists():
            print(f"[CREDENTIAL] tidak ada: {cred_path.name} -> auto-save gagal")
            return

        cred = credentials.Certificate(str(cred_path))
        firebase_app = firebase_admin.initialize_app(cred, {"databaseURL": FIREBASE_DB_URL})
        print(f"[FIREBASE] inisialisasi berhasil, DB URL: {FIREBASE_DB_URL}")
        
    except Exception as e:
        try:
            firebase_app = firebase_admin.get_app()
            print("[FIREBASE] recovered existing default app after exception")
            last_firebase_error = None
            return
        except Exception:
            pass
        print("[FIREBASE] CRITICAL initialization error:", e)
        traceback.print_exc()
        last_firebase_error = str(e)
        firebase_app = None

initialize_firebase() 

#MAPPING
CLASS_NAMES_ORIGINAL = ["Aphids", "Cercospora", "Leaf Wilt", "Phytophthora Blight", "Powdery Mildew", "Sehat", "TMV"]
CLASS_NAMES_MAPPED = {
    "Aphids": "aphids",
    "Cercospora": "bercak_cercospora",
    "Leaf Wilt": "layu_fusarium",
    "Phytophthora Blight": "phytophthora_blight",
    "Powdery Mildew": "powdery_mildew",
    "Sehat": "sehat",
    "TMV": "mosaic_virus"
}

model_dir = Path(__file__).parent / "model"
model_dir.mkdir(exist_ok=True)
ONNX_MODEL_PATH = model_dir / "best.onnx"
PT_MODEL_PATH = model_dir / "best.pt"
#INISIALISASI MODEL
onnx_session = None
yolo_model = None
def load_models(force_reload: bool = False):
    global onnx_session, yolo_model
    try:
        print("load_models: ONNX_MODEL_PATH =", ONNX_MODEL_PATH.resolve())
        if onnx_session is None or force_reload:
            if ONNX_MODEL_PATH.exists():
                try:
                    print(f"Loading ONNX model from {ONNX_MODEL_PATH}")
                    onnx_session = ort.InferenceSession(str(ONNX_MODEL_PATH), providers=["CPUExecutionProvider"])
                    print("✓ ONNX model loaded")
                except Exception as e:
                    print("Error loading ONNX model:", e)
                    import traceback as _tb; _tb.print_exc()
                    onnx_session = None
            else:
                print(f"ONNX model not found at {ONNX_MODEL_PATH} — place your .onnx file there")
        if yolo_model is None or force_reload:
            if PT_MODEL_PATH.exists():
                try:
                    print(f"Loading PyTorch YOLO model from {PT_MODEL_PATH}")
                    yolo_model = YOLO(str(PT_MODEL_PATH))
                    print("PyTorch YOLO model loaded")
                except Exception as e:
                    print("Error loading PyTorch YOLO model:", e)
                    import traceback as _tb; _tb.print_exc()
                    yolo_model = None
            else:
                print(f"PyTorch model not found at {PT_MODEL_PATH} — skipping PT load")
    except Exception as e:
        print("Error loading models (outer):", e)
        import traceback as _tb; _tb.print_exc()



GROQ_API_KEY = os.getenv("GROQ_API_KEY", "").strip()
GROQ_ENABLED = bool(GROQ_API_KEY)
if GROQ_ENABLED and Groq is not None:
    groq_client = Groq(api_key=GROQ_API_KEY)
else:
    groq_client = None

def get_fallback_explanation(disease_name: str, confidence: float) -> Dict[str, str]:
    expert_knowledge = {
        "layu_fusarium": {
            "ciri": "Daun menguning, layu, dan pertumbuhan terhambat. Batang menunjukkan garis-garis coklat.",
            "rekomendasi_penanganan": "Cabut tanaman terinfeksi, gunakan varietas tahan, dan praktikkan rotasi tanaman."
        },
        "bercak_cercospora": {
            "ciri": "Bercak kecil bulat berwarna coklat dengan pusat abu-abu pada daun. Daun menguning dan rontok.",
            "rekomendasi_penanganan": "Semprot fungisida tembaga, buang daun sakit, dan jaga kebersihan kebun."
        },
        "mosaic_virus": {
            "ciri": "Pola mosaik hijau-kuning tidak beraturan pada daun. Daun keriting dan pertumbuhan kerdil.",
            "rekomendasi_penanganan": "Buang tanaman terinfeksi, kendalikan serangga vektor (kutu daun), dan gunakan benih sehat."
        },
        "aphids": {
            "ciri": "Koloni serangga kecil hijau/hitam pada pucuk daun. Daun keriting, lengket, dan pertumbuhan terhambat.",
            "rekomendasi_penanganan": "Semprot air sabun, gunakan insektisida organik, atau lepaskan predator alami (kumbang koksi)."
        },
        "phytophthora_blight": {
            "ciri": "Busuk basah pada batang dekat tanah. Daun layu mendadak dan buah membusuk.",
            "rekomendasi_penanganan": "Tingkatkan drainase, hindari penyiraman berlebihan, dan gunakan fungisida sistemik."
        },
        "powdery_mildew": {
            "ciri": "Lapisan putih seperti tepung pada permukaan daun. Daun mengering dan keriting.",
            "rekomendasi_penanganan": "Semprot campuran soda kue dan sabun, tingkatkan sirkulasi udara, dan kurangi kelembapan."
        },
        "sehat": {
            "ciri": "Daun hijau segar, pertumbuhan normal dan seragam. Tidak ada bercak atau perubahan warna abnormal.",
            "rekomendasi_penanganan": "Lanjutkan perawatan rutin, pantau secara berkala, dan jaga kondisi tanah optimal."
        }
    }
    return expert_knowledge.get(disease_name, {
        "ciri": f"Penyakit {disease_name} terdeteksi dengan keyakinan {confidence:.0%}.",
        "rekomendasi_penanganan": "Konsultasikan dengan ahli pertanian untuk diagnosis dan penanganan lebih lanjut."
    })

async def get_ai_explanation(disease_name: str, confidence: float) -> Dict[str, str]:
    if GROQ_ENABLED and groq_client:
        try:
            disease_descriptions = {
                "layu_fusarium": "penyakit layu fusarium pada tanaman",
                "bercak_cercospora": "penyakit bercak cercospora pada daun",
                "mosaic_virus": "penyakit mosaic virus pada tanaman",
                "aphids": "serangan kutu daun (aphids)",
                "phytophthora_blight": "penyakit phytophthora blight",
                "powdery_mildew": "penyakit powdery mildew (embun tepung)",
                "sehat": "tanaman dalam kondisi sehat"
            }
            disease_desc = disease_descriptions.get(disease_name, disease_name)
            prompt = f"""Sebagai ahli pertanian, berikan penjelasan detail tentang {disease_desc} yang terdeteksi dengan tingkat keyakinan {confidence:.0%}.

                        Berikan respons dalam format JSON dengan dua field:
                        1. "ciri": uraian rinci ciri-ciri visual dalam 4–6 kalimat.
                        2. "rekomendasi_penanganan": langkah penanganan terstruktur dalam 4–6 kalimat.

                        Gunakan bahasa Indonesia yang mudah dipahami petani."""
            response = groq_client.chat.completions.create(
                model="llama-3.1-8b-instant",
                messages=[
                    {"role": "system", "content": "Anda adalah ahli pertanian. Kembalikan HANYA objek JSON dengan field 'ciri' dan 'rekomendasi_penanganan'. Tanpa teks lain, tanpa markdown, tanpa code fence."},
                    {"role": "user", "content": prompt}
                ],
                temperature=0.7,
                max_tokens=1000,
                timeout=10
            )
            content = response.choices[0].message.content or ""
            txt = content.strip()
            if txt.startswith("```"):
                parts = txt.split("```")
                if len(parts) >= 3:
                    inner = parts[1]
                    if inner.strip().lower().startswith("json") and len(parts) >= 3:
                        txt = parts[2].strip()
                    else:
                        txt = inner.strip()
            start = txt.find("{")
            end = txt.rfind("}")
            candidate = txt[start:end+1] if start != -1 and end != -1 and end > start else txt
            try:
                info = json.loads(candidate)
            except Exception:
                try:
                    info = json.loads(candidate.replace("'", '"'))
                except Exception as e2:
                    print(f"[WARN] Gagal parsing output Groq: {e2}")
                    raise
            return {
                "ciri": info.get("ciri", ""),
                "rekomendasi_penanganan": info.get("rekomendasi_penanganan", "")
            }
        except Exception as e:
            print(f"[WARN] Gagal memanggil Groq: {e}")
    return get_fallback_explanation(disease_name, confidence)


def preprocess_image(image_bytes: bytes) -> np.ndarray:
    image = Image.open(io.BytesIO(image_bytes))
    image = image.convert('RGB')
    image_np = np.array(image)
    return image_np

def predict_with_onnx(image: np.ndarray) -> List[Dict]:
    global onnx_session, yolo_model
    if onnx_session is None:
        load_models(force_reload=False)
        if onnx_session is None:
            if yolo_model is not None:
                return predict_with_yolo(image)
            raise HTTPException(status_code=500, detail=f"ONNX model not loaded (tried {ONNX_MODEL_PATH.resolve()})")

    img_resized = cv2.resize(image, (640, 640))
    img_rgb = img_resized
    img_normalized = img_rgb.astype(np.float32) / 255.0
    img_transposed = np.transpose(img_normalized, (2, 0, 1))
    img_batch = np.expand_dims(img_transposed, axis=0).astype(np.float32)
    input_name = onnx_session.get_inputs()[0].name
    outputs = onnx_session.run(None, {input_name: img_batch})

    preds = outputs[0]
    preds = np.squeeze(preds)
    if preds.ndim == 2 and preds.shape[0] in (11, 12):
        preds = preds.T
    elif preds.ndim == 3:
        preds = np.squeeze(preds)
        if preds.ndim == 2 and preds.shape[0] in (11, 12):
            preds = preds.T

    if preds.ndim != 2 or preds.shape[1] < 6:
        return []

    conf_thresh = 0.5
    iou_thresh = 0.5

    x_center = preds[:, 0]
    y_center = preds[:, 1]
    width = preds[:, 2]
    height = preds[:, 3]
    obj_conf = preds[:, 4]
    n_cols = preds.shape[1]
    class_scores_all = preds[:, 5:n_cols]

    if class_scores_all.size == 0:
        return []

    class_ids = np.argmax(class_scores_all, axis=1)
    class_scores = class_scores_all[np.arange(class_scores_all.shape[0]), class_ids]
    scores = class_scores * obj_conf

    keep_basic = scores >= conf_thresh
    x_center = x_center[keep_basic]
    y_center = y_center[keep_basic]
    width = width[keep_basic]
    height = height[keep_basic]
    scores = scores[keep_basic]
    class_ids = class_ids[keep_basic]

    if x_center.size == 0:
        return []

    x1 = x_center - width / 2.0
    y1 = y_center - height / 2.0
    x2 = x_center + width / 2.0
    y2 = y_center + height / 2.0

    def nms_numpy(boxes_xyxy: np.ndarray, sc: np.ndarray, thr: float) -> List[int]:
        if boxes_xyxy.size == 0:
            return []
        x1b = boxes_xyxy[:, 0]
        y1b = boxes_xyxy[:, 1]
        x2b = boxes_xyxy[:, 2]
        y2b = boxes_xyxy[:, 3]
        areas = (x2b - x1b) * (y2b - y1b)
        order = sc.argsort()[::-1]
        keep_idx = []
        while order.size > 0:
            i = int(order[0])
            keep_idx.append(i)
            if order.size == 1:
                break
            xx1 = np.maximum(x1b[i], x1b[order[1:]])
            yy1 = np.maximum(y1b[i], y1b[order[1:]])
            xx2 = np.minimum(x2b[i], x2b[order[1:]])
            yy2 = np.minimum(y2b[i], y2b[order[1:]])
            w = np.maximum(0.0, xx2 - xx1)
            h = np.maximum(0.0, yy2 - yy1)
            inter = w * h
            iou = inter / (areas[i] + areas[order[1:]] - inter + 1e-6)
            remain = np.where(iou <= thr)[0]
            order = order[remain + 1]
        return keep_idx

    detections = []
    max_boxes = 100
    for cls in np.unique(class_ids):
        cls_name = CLASS_NAMES_ORIGINAL[cls] if cls < len(CLASS_NAMES_ORIGINAL) else f"class_{int(cls)}"
        mapped_name = CLASS_NAMES_MAPPED.get(cls_name, cls_name.lower())

        idx = np.where(class_ids == cls)[0]
        boxes_cls = np.stack([x1[idx], y1[idx], x2[idx], y2[idx]], axis=1)
        scores_cls = scores[idx]
        keep = nms_numpy(boxes_cls, scores_cls, iou_thresh)
        for k in keep:
            i = idx[k]
            cx = float((boxes_cls[k, 0] + boxes_cls[k, 2]) / 2.0)
            cy = float((boxes_cls[k, 1] + boxes_cls[k, 3]) / 2.0)
            w = float(boxes_cls[k, 2] - boxes_cls[k, 0])
            h = float(boxes_cls[k, 3] - boxes_cls[k, 1])
            conf_val = float(scores_cls[k])
            detections.append({
                "class": mapped_name,
                "confidence": conf_val,
                "bbox": [cx, cy, w, h]
            })
            if len(detections) >= max_boxes:
                break
        if len(detections) >= max_boxes:
            break

    return detections

def predict_with_yolo(image: np.ndarray) -> List[Dict]:
    global yolo_model
    if yolo_model is None:
        raise HTTPException(status_code=500, detail="YOLO model not loaded for fallback")
    try:
        results = yolo_model.predict(source=image, imgsz=640, conf=0.5, iou=0.5, verbose=False)
        detections = []
        if len(results) == 0:
            return detections
        r = results[0]
        boxes = getattr(r, "boxes", None)
        if boxes is None:
            return detections
        xywh = getattr(boxes, "xywh", None)
        confs = getattr(boxes, "conf", None)
        classes = getattr(boxes, "cls", None)
        n = len(boxes)
        for i in range(n):
            try:
                bb = xywh[i].cpu().numpy() if hasattr(xywh[i], "cpu") else np.array(xywh[i])
                x_center, y_center, width, height = float(bb[0]), float(bb[1]), float(bb[2]), float(bb[3])
            except Exception:
                continue
            conf = float(confs[i]) if confs is not None else 0.0
            class_id = int(classes[i]) if classes is not None else 0
            class_name = CLASS_NAMES_ORIGINAL[class_id] if class_id < len(CLASS_NAMES_ORIGINAL) else f"class_{class_id}"
            mapped_name = CLASS_NAMES_MAPPED.get(class_name, class_name.lower())
            detections.append({
                "class": mapped_name,
                "confidence": float(conf),
                "bbox": [float(x_center), float(y_center), float(width), float(height)]
            })
        return detections
    except Exception as e:
        print("predict_with_yolo error:", e)
        import traceback as _tb
        _tb.print_exc()
        raise HTTPException(status_code=500, detail="YOLO fallback failed")


async def process_detection(image_bytes: bytes, sensor_data: Optional[Dict] = None) -> Dict:
    image = preprocess_image(image_bytes)
    detections = predict_with_onnx(image)
    detection_counts = {
        "layu_fusarium": 0, "bercak_cercospora": 0, "mosaic_virus": 0, "aphids": 0,
        "phytophthora_blight": 0, "powdery_mildew": 0, "sehat": 0
    }
    confidences_by_class = {k: [] for k in detection_counts.keys()}
    
    for det in detections:
        class_name = det["class"]
        if class_name in detection_counts:
            detection_counts[class_name] += 1
            confidences_by_class[class_name].append(det["confidence"])
    disease_classes = {k: v for k, v in detection_counts.items() if k != "sehat"}
    
    if not any(disease_classes.values()):
        dominan_disease = "sehat"
        dominan_confidence_avg = np.mean(confidences_by_class["sehat"]) if confidences_by_class["sehat"] else 0.0
    else:
        dominan_disease = max(disease_classes, key=disease_classes.get)
        dominan_confidence_avg = np.mean(confidences_by_class[dominan_disease]) if confidences_by_class[dominan_disease] else 0.0
    
    status = "sehat" if dominan_disease == "sehat" else "tidak sehat"
    
    dominan_confidence_avg = float(dominan_confidence_avg)
    info = await get_ai_explanation(dominan_disease, dominan_confidence_avg)
    if sensor_data is None:
        sensor_data = {"suhu": 26.9, "kelembapan": 83.5, "cahaya": 42}
    sample_image_url = f"https://your-domain.com/snapshots/snapshot_{datetime.now(timezone.utc).isoformat().replace(':', '-')}.jpg"
    result = {
        "timestamp": datetime.now(timezone.utc).isoformat(),
        "dominan_disease": dominan_disease,
        "dominan_confidence_avg": dominan_confidence_avg,
        "jumlah_disease_terdeteksi": detection_counts,
        "sensor_rata-rata": sensor_data,
        "status": status,
        "sample_image": sample_image_url,
        "info": info
    }
    return result

last_save_time = datetime.min.replace(tzinfo=timezone.utc)
SAVE_INTERVAL_SECONDS = 30 
#SIMPAN DETEKSI 30S
async def save_detection_result(result: Dict, user_id: Optional[str] = None):
    try:
        if firebase_app is None:
            print("[SAVE] Firebase not initialized - skipping save")
            return {"ok": False, "reason": "firebase not initialized"}
        
        loop = asyncio.get_running_loop()
        
        def _push():
            ts_key = str(int(datetime.now(timezone.utc).timestamp() * 1000))
            user_key = user_id if user_id else "anonymous"
            ref_path = f"detections/{user_key}"
            ref = firebase_db.reference(ref_path)
            ref.child(ts_key).set(result)
            return ts_key
            

        key = await loop.run_in_executor(None, _push)
        print(f"[SAVE SUCCESS] saved detection to path {user_id}/{key}")
        return {"ok": True, "key": str(key)}
        
    except Exception as e:
        print(f"[SAVE ERROR] Failed to save detection to Firebase: {e}")
        traceback.print_exc()
        return {"ok": False, "reason": str(e)}

def fetch_detections_from_firebase(limit: int = 100) -> List[Dict]:
    if firebase_app is None:
        raise HTTPException(status_code=500, detail="Firebase not initialized")
    try:
        root = firebase_db.reference("detections").get()
        if not root:
            return []
        flattened = []
        for user_id, entries in root.items():
            if not isinstance(entries, dict):
                continue
            for ts_key, data in entries.items():
                ts_iso = data.get("timestamp", datetime.min.isoformat())
                flattened.append({
                    "firebase_key": ts_key,
                    "user_id": user_id,
                    "timestamp": ts_iso,
                    "dominan_disease": data.get("dominan_disease", data.get("dominan", "")),
                    "dominan_confidence_avg": data.get("dominan_confidence_avg", 0.0),
                    "jumlah_disease_terdeteksi": data.get("jumlah_disease_terdeteksi", data.get("class_counts", {})),
                    "sensor_rata_rata": data.get("sensor_rata-rata", data.get("sensor_avg", {})),
                    "status": data.get("status", ""),
                    "info": data.get("info", {})
                })
        
        flattened.sort(key=lambda x: x.get("timestamp", datetime.min.isoformat()), reverse=True)
        return flattened[:limit]
    except Exception as e:
        print(f"Error fetching detections from Firebase: {e}")
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail="Error fetching detections from Firebase")

def fetch_detection_by_key(firebase_key: str) -> Optional[Dict]:
    if firebase_app is None:
        raise HTTPException(status_code=500, detail="Firebase not initialized")
    try:
        root = firebase_db.reference("detections").get()
        if not root:
            return None 
        for user_id, entries in root.items():
            if entries and firebase_key in entries:
                data = entries[firebase_key]
                return {
                    "firebase_key": firebase_key,
                    "user_id": user_id,
                    "timestamp": data.get("timestamp", ""),
                    "dominan_disease": data.get("dominan_disease", data.get("dominan", "")),
                    "dominan_confidence_avg": data.get("dominan_confidence_avg", 0.0),
                    "jumlah_disease_terdeteksi": data.get("jumlah_disease_terdeteksi", data.get("class_counts", {})),
                    "sensor_rata-rata": data.get("sensor_rata-rata", data.get("sensor_avg", {})),
                    "status": data.get("status", ""),
                    "info": data.get("info", {})
                }
        return None
    except Exception as e:
        print(f"Error fetching deteksi dari Firebase: {e}")
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail="Error fetching deteksi dari Firebase")

@asynccontextmanager
async def lifespan(app: FastAPI):
    load_models() 
    print("FastAPI Lifespan started.")
    yield
    print("FastAPI Lifespan shutdown.")
app = FastAPI(title="Terra YOLOv8 Detection API", version="1.0.0", lifespan=lifespan)
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"], 
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

#ENDPOINTS API
@app.get("/")
async def root():
    return {"message": "Welcome to Terra YOLOv8 Detection API", "status": "active"}

@app.get("/health")
async def health_check():
    model_status = {
        "onnx_loaded": onnx_session is not None,
        "yolo_loaded": yolo_model is not None,
        "groq_enabled": GROQ_ENABLED,
        "firebase_initialized": firebase_app is not None
    }
    return {"status": "healthy", "models": model_status}

# ENDPOINT API GROQ
@app.get("/test-groq")
async def test_groq():
    """Test Groq connection"""
    if not GROQ_ENABLED or groq_client is None:
        return {"status": "error", "message": "GROQ_API_KEY not set or client unavailable"}
    try:
        response = groq_client.chat.completions.create(
            model="llama-3.1-8b-instant",
            messages=[{"role": "user", "content": "Sebutkan 2 penyakit tanaman tomat dalam satu kalimat."}],
            max_tokens=1000
        )
        return {
            "status": "success", 
            "message": "Groq connection working",
            "response": response.choices[0].message.content,
            "model": "llama-3.1-8b-instant"
        }
        
    except Exception as e:
        return {"status": "error", "message": str(e)}

# ENDPOINT API DETECTION
@app.post("/detect")
async def detect_disease(
    background_tasks: BackgroundTasks,
    file: UploadFile = File(...),
    suhu: Optional[float] = None,
    kelembapan: Optional[float] = None,
    cahaya: Optional[float] = None,
    user_id: Optional[str] = None
):
    try:
        image_bytes = await file.read()
        sensor_data = None
        if suhu is not None or kelembapan is not None or cahaya is not None:
            sensor_data = {
                "suhu": suhu if suhu is not None else 26.9,
                "kelembapan": kelembapan if kelembapan is not None else 83.5,
                "cahaya": cahaya if cahaya is not None else 42
            }   
        result = await process_detection(image_bytes, sensor_data)
        background_tasks.add_task(save_detection_result, result, user_id=user_id)
        
        return JSONResponse(content=result)
    except Exception as e:
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail=f"Detection failed: {str(e)}")

@app.post("/detect/realtime")
async def detect_realtime(
    file: UploadFile = File(...)
):
    try:
        image_bytes = await file.read()
        image = preprocess_image(image_bytes)
        detections = predict_with_onnx(image)
        formatted_detections = []
        for det in detections:
            try:
                bbox = det.get("bbox")
                if not bbox or len(bbox) != 4:
                    continue
                x_center, y_center, width, height = map(float, bbox)
                x1 = x_center - width / 2
                y1 = y_center - height / 2
                
                formatted_detections.append({
                    "class": det.get("class", "unknown"),
                    "confidence": float(det.get("confidence", 0.0)),
                    "bbox": {
                        "x1": float(x1),
                        "y1": float(y1),
                        "x2": float(x1 + width),
                        "y2": float(y1 + height),
                        "width": float(width),
                        "height": float(height)
                    }
                })
            except Exception as e_row:
                logging.error("/detect/realtime - row formatting error: %s\n%s", e_row, traceback.format_exc())
                continue
        return JSONResponse(content={"detections": formatted_detections, "timestamp": datetime.now(timezone.utc).isoformat()})
    except Exception as e:
        tb = traceback.format_exc()
        logging.error("/detect/realtime - unhandled exception:\n" + tb)
        return JSONResponse(status_code=500, content={"error": str(e), "trace": tb})


@app.post("/detect/auto")
async def detect_auto(
    background_tasks: BackgroundTasks,
    file: UploadFile = File(None),
    image_base64: Optional[str] = None,
    suhu: Optional[float] = None,
    kelembapan: Optional[float] = None,
    cahaya: Optional[float] = None,
    save: bool = True
):    
    if file is None and not image_base64:
        raise HTTPException(status_code=400, detail="No image provided (file or image_base64 required)")
        
    try:
        if file is not None:
            image_bytes = await file.read()
        else:
            try:
                if image_base64 and "," in image_base64:
                    image_base64 = image_base64.split(",")[1]
                image_bytes = base64.b64decode(image_base64)
            except Exception:
                raise HTTPException(status_code=400, detail="Invalid base64 image")

        sensor_data = None
        if any(v is not None for v in (suhu, kelembapan, cahaya)):
            sensor_data = {
                "suhu": suhu if suhu is not None else 26.9,
                "kelembapan": kelembapan if kelembapan is not None else 83.5,
                "cahaya": cahaya if cahaya is not None else 42
            }
        result = await process_detection(image_bytes, sensor_data)
        global last_save_time
        now = datetime.now(timezone.utc)
        
        saved = False
        skip_reason = None
        
        if save:
            time_since_last_save = (now - last_save_time).total_seconds()
            if time_since_last_save >= SAVE_INTERVAL_SECONDS:
                save_res = await save_detection_result(result, user_id="autoSimpan")
                saved = bool(save_res and save_res.get("ok"))
                if saved:
                    last_save_time = now
                    print(f"[AUTO SAVE OK] Saved frame to Firebase. Next save in {SAVE_INTERVAL_SECONDS}s.")
                else:
                    skip_reason = f"save failed: {save_res.get('reason', 'unknown') if save_res else 'unknown'}"
                    print(f"[AUTO SAVE FAIL] {skip_reason}")
            else:
                skip_reason = f"Next save in {SAVE_INTERVAL_SECONDS - time_since_last_save:.1f}s"
                print(f"[AUTO SKIP] Skipped save. {skip_reason}")
            
        return JSONResponse(content={"saved_this_frame": saved, "result": result, "skip_reason": skip_reason})
        
    except HTTPException:
        raise
    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=f"Auto detection failed: {str(e)}")

@app.get("/detections")
async def get_detections(limit: int = 100):
    try:
        loop = asyncio.get_running_loop()
        results = await loop.run_in_executor(None, fetch_detections_from_firebase, limit)
        return {"detections": results, "count": len(results)}
    except HTTPException:
        raise
    except Exception as e:
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail=f"Failed to retrieve detections: {str(e)}")

@app.get("/detections/{detection_id}")
async def get_detection(detection_id: str):
    try:
        loop = asyncio.get_running_loop()
        det = await loop.run_in_executor(None, fetch_detection_by_key, detection_id) 
        if not det:
            raise HTTPException(status_code=404, detail="Detection not found")
        return det
    except HTTPException:
        raise
    except Exception as e:
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail=f"Failed to retrieve detection: {str(e)}")

@app.get("/firebase-test")
async def firebase_test():
    try:
        if firebase_app is None:
            return JSONResponse(status_code=200, content={"ok": False, "message": "firebase not initialized", "credentials_path": str(FIREBASE_CREDENTIALS_PATH)})
        loop = asyncio.get_running_loop()
        def _push_test():
            ref = firebase_db.reference("test_connection")
            key_ref = ref.push({"ok": True, "ts": datetime.now(timezone.utc).isoformat()})
            return key_ref.key
            
        key = await loop.run_in_executor(None, _push_test)
        return JSONResponse(content={"ok": True, "key": str(key)})
    except Exception as e:
        import traceback as _tb; tb = _tb.format_exc()
        print("[FIREBASE TEST] error:", e, tb)
        return JSONResponse(status_code=500, content={"ok": False, "error": str(e), "trace": tb})

if __name__ == "__main__":
    import uvicorn
    host = os.getenv("API_HOST", "0.0.0.0")
    port = int(os.getenv("API_PORT", 8001)) 
    
    uvicorn.run("main:app", host=host, port=port, reload=True, log_level="info")