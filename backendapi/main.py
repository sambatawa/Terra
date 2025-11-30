from fastapi import FastAPI, File, UploadFile, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from pydantic import BaseModel
from typing import Optional, Dict, List
import numpy as np # Masih diperlukan jika ada manipulasi array, tapi bisa dihapus jika tidak ada
from PIL import Image # Masih diperlukan jika ingin membaca image_bytes, tapi fungsi preprocess_image tidak dipakai
import io # Masih diperlukan jika ingin membaca image_bytes, tapi fungsi preprocess_image tidak dipakai
import os
from datetime import datetime, timezone, timedelta # Tambah timedelta yang hilang
import json
from pathlib import Path
import asyncio
from dotenv import load_dotenv
from groq import Groq
from contextlib import asynccontextmanager
import firebase_admin
from firebase_admin import credentials, db as firebase_db
import traceback
import time
import re

# --- PENGHAPUSAN: cv2, sys, logging, base64 (tidak digunakan) ---

# --- SENSOR DATA MODEL ---
class SensorData(BaseModel):
    suhu: float
    kelembapan: int
    cahaya: int
    status: str
    timestamp: str

# --- GPS DATA MODEL ---
class GPSData(BaseModel):
    latitude: float
    longitude: float
    accuracy: Optional[float] = None
    altitude: Optional[float] = None
    altitude_accuracy: Optional[float] = None
    heading: Optional[float] = None
    speed: Optional[float] = None
    timestamp: str
    user_id: Optional[str] = None

class GPSDetectionData(BaseModel):
    gps: GPSData
    suhu: Optional[float] = None
    kelembapan: Optional[float] = None
    cahaya: Optional[float] = None
    disease: Optional[str] = None
    confidence: Optional[float] = None
    timestamp: str

load_dotenv()
FIREBASE_CONFIG = {"databaseURL": os.getenv("FIREBASE_DATABASE_URL")}
FIREBASE_CREDENTIALS_PATH = os.getenv("FIREBASE_CREDENTIALS_PATH") or Path(__file__).parent / "firebase-credentials.json"
FIREBASE_DB_URL = FIREBASE_CONFIG["databaseURL"]

firebase_app = None
last_firebase_error = None

# INISIALISASI FIREBASE
def initialize_firebase():
    global firebase_app, last_firebase_error
    try:
        if firebase_app is not None:
            # print("[FIREBASE] tidak siap") # Diubah agar tidak membingungkan
            return
        try:
            # Cek apakah sudah ada app yang diinisialisasi
            firebase_app = firebase_admin.get_app()
            # print("[FIREBASE] gunakan default") # Diubah agar tidak membingungkan
            return
        except ValueError:
            pass
        
        cred_path = FIREBASE_CREDENTIALS_PATH
        cred_path = Path(cred_path) if isinstance(cred_path, str) else cred_path
        print(f"[FIREBASE] creds path = {cred_path.resolve()}")
        if not cred_path.exists():
            print(f"[FIREBASE] file credential tak ada: {cred_path.name} -> auto-save gagal")
            return
        cred = credentials.Certificate(str(cred_path))
        firebase_app = firebase_admin.initialize_app(cred, {"databaseURL": FIREBASE_DB_URL})
        print(f"[FIREBASE] inisialisasi berhasil, DB URL: {FIREBASE_DB_URL}")
        
    except Exception as e:
        try:
            # Coba ambil app yang sudah ada (untuk kasus error inisialisasi ganda)
            firebase_app = firebase_admin.get_app()
            print("[FIREBASE] gunakan default setelah error")
            last_firebase_error = None
            return
        except Exception:
            pass
        print("[FIREBASE] inisialisasi gagal:", e)
        traceback.print_exc()
        last_firebase_error = str(e)
        firebase_app = None

initialize_firebase() 

# MAPPING
# CLASS_NAMES_ORIGINAL dihapus (tidak digunakan)
CLASS_NAMES_MAPPED = {
    "Aphids": "aphids",
    "Cercospora": "bercak_cercospora",
    "Leaf Wilt": "layu_fusarium",
    "Phytophthora Blight": "phytophthora_blight",
    "Powdery Mildew": "powdery_mildew",
    "Sehat": "sehat",
    "TMV": "mosaic_virus"
}

# Variabel ONNX dihapus
# Fungsi preprocess_image dihapus (tidak digunakan)

GROQ_API_KEY = os.getenv("GROQ_API_KEY", "").strip()
GROQ_ENABLED = bool(GROQ_API_KEY)
groq_client = Groq(api_key=GROQ_API_KEY) if GROQ_ENABLED and Groq is not None else None


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
        "ciri": f"Penyakit {disease_name} terdeteksi dengan keyakinan {confidence:.0%} (data fallback).",
        "rekomendasi_penanganan": "Konsultasikan dengan ahli pertanian untuk diagnosis dan penanganan lebih lanjut."
    })

def calculate_dominant_percentage(disease_counts: Dict[str, int]) -> float:
    """
    Menghitung persentase penyakit dominan dari total penyakit terdeteksi
    """
    if not disease_counts:
        return 0.0
    
    total_detections = sum(disease_counts.values())
    if total_detections == 0:
        return 0.0
    
    # Cari penyakit dengan jumlah terbanyak
    dominant_count = max(disease_counts.values()) if disease_counts else 0
    
    # Hitung persentase
    percentage = (dominant_count / total_detections) * 100
    return round(percentage, 2)

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
            
            # Parsing output Groq, menangani code fence dan non-JSON chars
            start = txt.find("{")
            end = txt.rfind("}")
            candidate = txt[start:end+1] if start != -1 and end != -1 and end > start else txt
            
            # Coba hapus markdown code fence jika ada
            if candidate.startswith("```json"):
                candidate = candidate[len("```json"):].strip()
            if candidate.endswith("```"):
                candidate = candidate[:-len("```")].strip()

            try:
                info = json.loads(candidate)
            except Exception as e:
                print("[DEBUG] Original JSON error: {}".format(e))
                print("[DEBUG] Raw candidate: {}".format(candidate[:500]))
                
                # Multiple fix attempts
                fixes = [
                    # Fix 1: Basic quote escaping
                    lambda x: x.replace("'", '"').replace('"', '\\"').replace('\\\\"', '\\"'),
                    
                    # Fix 2: Regex-based quote fixing for JSON values
                    lambda x: re.sub(r'(\"[^\"]*)\"([^\"]*\":)', r'\1\\\\"\2', x),
                    
                    # Fix 3: Remove problematic characters and normalize
                    lambda x: re.sub(r'[\x00-\x1f\x7f-\x9f]', '', x).replace('\n', ' ').replace('\r', ''),
                    
                    # Fix 4: Handle multiline JSON
                    lambda x: '\\'.join(line.strip() for line in x.split('\n') if line.strip()),
                    
                    # Fix 5: Aggressive comma and whitespace fixing
                    lambda x: re.sub(r',\s*([}\]])', r'\1', re.sub(r'\s+', ' ', x))
                ]
                
                for i, fix_func in enumerate(fixes):
                    try:
                        fixed = fix_func(candidate)
                        # Additional safety fixes
                        fixed = re.sub(r',\s*}', '}', fixed)  # Trailing commas
                        fixed = re.sub(r',\s*]', ']', fixed)  # Trailing commas in arrays
                        fixed = re.sub(r'\n\s*', ' ', fixed)  # Remove newlines
                        fixed = re.sub(r'\s+', ' ', fixed)  # Normalize whitespace
                        
                        print("[DEBUG] Attempt {} with fix: {}".format(i+1, fixed[:200]))
                        info = json.loads(fixed)
                        print("[SUCCESS] Parsed with attempt {}".format(i+1))
                        break
                    except Exception as fix_error:
                        print("[DEBUG] Attempt {} failed: {}".format(i+1, fix_error))
                        if i == len(fixes) - 1:  # Last attempt
                            print("[WARNING] Gagal parsing output Groq: {} | Original: {}".format(fix_error, e))
                            print("[DEBUG] Final candidate JSON: {}".format(candidate[:500]))
                            return get_fallback_explanation(disease_name, confidence)
            return {
                "ciri": info.get("ciri", ""),
                "rekomendasi_penanganan": info.get("rekomendasi_penanganan", "")
            }
        except Exception as e:
            print("[WARNING] Gagal memanggil Groq: {}".format(e))
    return get_fallback_explanation(disease_name, confidence)

# process_detection: Fungsi ini dipertahankan karena digunakan di /detect, namun isinya diubah 
# karena inferensi dilakukan di client-side.
async def process_detection(sensor_data: Optional[Dict] = None) -> Dict:
    # Server-side processing disabled - using client-side detection
    
    # Default to healthy since server doesn't process detections
    detection_counts = {
        "layu_fusarium": 0, "bercak_cercospora": 0, "mosaic_virus": 0, "aphids": 0,
        "phytophthora_blight": 0, "powdery_mildew": 0, "sehat": 1  # Default to healthy
    }
    
    dominan_disease = "sehat"
    dominan_confidence_avg = 0.0
    status = "sehat"
    
    info = await get_ai_explanation(dominan_disease, dominan_confidence_avg)
    result = {
        "timestamp": datetime.now(timezone.utc).isoformat(),
        "dominan_disease": dominan_disease,
        "dominan_confidence_avg": dominan_confidence_avg,
        "jumlah_disease_terdeteksi": detection_counts,
        "sensor_rata_rata": sensor_data,
        "status": status,
        "info": info
    }
    return result

last_save_time = datetime.min.replace(tzinfo=timezone.utc)
SAVE_INTERVAL_SECONDS = 30 
# SIMPAN DETEKSI 30S
async def save_detection_result(result: Dict, user_id: Optional[str] = None):
    try:
        if firebase_app is None:
            print("[SAVE] firebase belum inisialisasi, lewati simpan")
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
        print(f"[SAVE] simpan berhasil ke firebase: {user_id}/{key}")
        return {"ok": True, "key": str(key)}
        
    except Exception as e:
        print(f"[SAVE] simpan gagal:{e}")
        traceback.print_exc()
        return {"ok": False, "reason": str(e)}

def get_latest_sensor_data_from_firebase() -> Optional[Dict]:
    if firebase_app is None:
        return None
    try:
        # Order by key (timestamp) dan ambil yang terakhir
        root = firebase_db.reference("sensor_data").order_by_key().limit_to_last(1).get()
        if not root or len(root) == 0:
            print("[SENSOR] No sensor data found in Firebase")
            return None
        
        # Ambil data dari key terakhir
        latest_key = list(root.keys())[0]
        latest_data = root[latest_key]
        
        # print(f"[SENSOR] Latest sensor data: {latest_data}") # Hapus log berulang
        
        return {
            "suhu": float(latest_data.get("suhu", 26.9)),
            "kelembapan": int(latest_data.get("kelembapan", 83)),
            "cahaya": int(latest_data.get("cahaya", 42))
        }
    except Exception as e:
        print(f"[SENSOR] Error fetching sensor data dari Firebase: {e}")
        import traceback as _tb
        _tb.print_exc()
        return None

def count_detections_last_30_seconds() -> Dict[str, int]:
    """Count detections untuk setiap disease class dalam 30 detik terakhir"""
    # Catatan: Fungsi ini mengambil semua data 'detections' dari root, yang bisa lambat.
    # Sebaiknya gunakan order_by_key().start_at() jika struktur Firebase mengizinkan.
    if firebase_app is None:
        print("[COUNT] Firebase not available, returning default counts")
        return {
            'aphids': 0, 'bercak_cercospora': 0, 'layu_fusarium': 0,
            'mosaic_virus': 0, 'phytophthora_blight': 0, 'powdery_mildew': 0, 'sehat': 0
        }
    
    try:
        # Ambil detections dari 30 detik terakhir
        now = datetime.now(timezone.utc)
        thirty_seconds_ago = now - timedelta(seconds=30)
        
        # NOTE: Ini adalah operasi yang sangat lambat jika data 'detections' besar!
        # Mengambil seluruh data 'detections' dari root.
        root = firebase_db.reference("detections").get()
        if not root:
            return {
                'aphids': 0, 'bercak_cercospora': 0, 'layu_fusarium': 0,
                'mosaic_virus': 0, 'phytophthora_blight': 0, 'powdery_mildew': 0, 'sehat': 0
            }
        
        # Count per disease class
        counts = {
            'aphids': 0, 'bercak_cercospora': 0, 'layu_fusarium': 0,
            'mosaic_virus': 0, 'phytophthora_blight': 0, 'powdery_mildew': 0, 'sehat': 0
        }
        
        for user_id, entries in root.items():
            if not isinstance(entries, dict):
                continue
            for ts_key, data in entries.items():
                ts_str = data.get("timestamp", "")
                if not ts_str:
                    continue
                        
                try:
                    # Parse timestamp dari Firebase
                    # Menghilangkan 'Z' dan menggantinya dengan timezone +00:00 untuk kompatibilitas datetime.fromisoformat
                    ts = datetime.fromisoformat(ts_str.replace('Z', '+00:00'))
                    if ts >= thirty_seconds_ago:
                        disease = data.get("dominan_disease", "sehat")
                        if disease in counts:
                            counts[disease] += 1
                except Exception as e:
                    # print(f"[COUNT] Error parsing timestamp {ts_str}: {e}") # Hapus log berulang
                    continue
        
        print(f"[COUNT] 30s counts: {counts}") # Tambah log untuk debugging
        return counts
        
    except Exception as e:
        print(f"[COUNT] Error counting detections: {e}")
        import traceback as _tb
        _tb.print_exc()
        return {
            'aphids': 0, 'bercak_cercospora': 0, 'layu_fusarium': 0,
            'mosaic_virus': 0, 'phytophthora_blight': 0, 'powdery_mildew': 0, 'sehat': 0
        }

def fetch_detections_from_firebase(limit: int = 100) -> List[Dict]:
    if firebase_app is None:
        raise HTTPException(status_code=500, detail="Firebase belum inisialisasi uyy")
    try:
        # Mengambil semua data detections (berpotensi lambat)
        root = firebase_db.reference("detections").get()
        if not root:
            return []
        
        flattened = []
        
        # Handle both list and dictionary structures from Firebase
        if isinstance(root, list):
            # If root is a list, treat each item as a detection entry
            for item in root:
                if not isinstance(item, dict):
                    continue
                
                # Try to extract timestamp from various possible fields
                ts_iso = item.get("timestamp", datetime.min.isoformat())
                
                flattened.append({
                    "firebase_key": item.get("firebase_key", str(len(flattened))),
                    "user_id": item.get("user_id", "unknown"),
                    "timestamp": ts_iso,
                    "dominan_disease": item.get("dominant_disease", item.get("dominan", "")),
                    "jumlah_disease_terdeteksi": item.get("jumlah_disease_terdeteksi", item.get("class_counts", {})),
                    "sensor_data": item.get("sensor_rata_rata", item.get("sensor_rata-rata", {})),
                    "status": item.get("status", ""),
                    "info": item.get("info", {}),
                    "dominan_confidence_avg": item.get("dominan_confidence_avg", 0)
                })
        
        elif isinstance(root, dict):
            # Original dictionary structure: {user_id: {timestamp_key: data}}
            for user_id, entries in root.items():
                if not isinstance(entries, dict):
                    continue
                for ts_key, data in entries.items():
                    if not isinstance(data, dict):
                        continue
                    
                    ts_iso = data.get("timestamp", datetime.min.isoformat())
                    flattened.append({
                        "firebase_key": ts_key,
                        "user_id": user_id,
                        "timestamp": ts_iso,
                        "dominan_disease": data.get("dominant_disease", data.get("dominant", "")),
                        "jumlah_disease_terdeteksi": data.get("jumlah_disease_terdeteksi", data.get("class_counts", {})),
                        "sensor_data": data.get("sensor_rata_rata", data.get("sensor_rata-rata", {})),
                        "status": data.get("status", ""),
                        "info": data.get("info", {}),
                        "dominan_confidence_avg": data.get("dominan_confidence_avg", 0)
                    })
        
        # Sort by timestamp (newest first) and limit
        flattened.sort(key=lambda x: x.get("timestamp", datetime.min.isoformat()), reverse=True)
        return flattened[:limit]
        
    except Exception as e:
        print(f"Error fetching deteksi dari Firebase:{e}")
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail="Error fetching deteksi dari firebase")

def fetch_detection_by_key(firebase_key: str) -> Optional[Dict]:
    if firebase_app is None:
        raise HTTPException(status_code=500, detail="Firebase belum inisialisasi uyy")
    try:
        # Mengambil semua data detections (berpotensi lambat)
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
                    "dominan_disease": data.get("dominan_disease", data.get("dominant", "")),
                    "jumlah_disease_terdeteksi": data.get("jumlah_disease_terdeteksi", data.get("class_counts", {})),
                    "sensor_data": data.get("sensor_rata_rata", data.get("sensor_rata-rata", {})),
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
    # load_models() removed
    print("FastAPI Lifespan started - server-side inference disabled.")
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

# --- ENDPOINTS API ---

@app.get("/")
async def root():
    return {"message": "Halo ini adalah Terra YOLOv8 Detection", "status": "active"}

@app.get("/health")
async def health_check():
    model_status = {
        "onnx_loaded": False,  # Server-side ONNX disabled
        "groq_enabled": GROQ_ENABLED,
        "firebase_initialized": firebase_app is not None
    }
    return {"status": "healthy", "models": model_status}
# ENDPOINT API DETECTION
# Digunakan untuk simulasi deteksi/mendapatkan info, karena client-side detection sudah memberikan hasil akhir
@app.post("/detect")
async def detect_disease(
    background_tasks: BackgroundTasks,
    suhu: Optional[float] = None,
    kelembapan: Optional[float] = None,
    cahaya: Optional[float] = None,
    user_id: Optional[str] = None
):
    try:
        # Ambil sensor data dari request atau Firebase
        sensor_data = None
        if suhu is not None or kelembapan is not None or cahaya is not None:
            sensor_data = {
                "suhu": suhu if suhu is not None else 26.9,
                "kelembapan": kelembapan if kelembapan is not None else 83.5,
                "cahaya": cahaya if cahaya is not None else 42
            }
        else:
            # Fungsi get_latest_sensor_data_from_firebase() dipanggil jika sensor data tidak disertakan
            sensor_data = get_latest_sensor_data_from_firebase()
        
        # NOTE: process_detection selalu mengembalikan 'sehat' karena inferensi di client-side
        result = await process_detection(sensor_data)
        background_tasks.add_task(save_detection_result, result, user_id=user_id)
        
        return JSONResponse(content=result)
    except Exception as e:
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail=str(e))

@app.post("/detect/auto")
async def detect_auto(
    background_tasks: BackgroundTasks,
    data: dict 
):     
    suhu = data.get('suhu')
    kelembapan = data.get('kelembapan')
    cahaya = data.get('cahaya')
    save = data.get('save', True)
    user_id = data.get('user_id')
    dominan_disease = data.get('dominant_disease', 'sehat')
    # Gunakan disease_counts dari frontend jika ada, fallback ke default
    frontend_counts = data.get('disease_counts', {})
    
    print(f"[DETECT] Request: disease={dominan_disease}, save={save}")
        
    try:
        sensor_data = None
        # Use Firebase data if any sensor value is null
        if suhu is not None and kelembapan is not None and cahaya is not None:
            sensor_data = {
                "suhu": float(suhu),
                "kelembapan": int(kelembapan),
                "cahaya": int(cahaya)
            }
        else:
            sensor_data = get_latest_sensor_data_from_firebase()
            missing = []
            if suhu is None: missing.append("suhu")
            if kelembapan is None: missing.append("kelembapan")
            if cahaya is None: missing.append("cahaya")
        default_counts = {
            'aphids': 0, 'bercak_cercospora': 0, 'layu_fusarium': 0,
            'mosaic_virus': 0, 'phytophthora_blight': 0, 'powdery_mildew': 0, 'sehat': 0
        }
        jumlah_penyakit_terdeteksi = frontend_counts if frontend_counts else default_counts
        
        # Hitung dominan_confidence_avg sebagai persentase penyakit dominan
        dominan_confidence_avg = calculate_dominant_percentage(jumlah_penyakit_terdeteksi)
        
        # Fix dominant disease berdasarkan counts terbanyak
        if jumlah_penyakit_terdeteksi:
            disease_entries = [(disease, count) for disease, count in jumlah_penyakit_terdeteksi.items() if count > 0]
            if disease_entries:
                disease_entries.sort(key=lambda x: x[1], reverse=True)
                dominan_disease = disease_entries[0][0]
            else:
                dominan_disease = "sehat"
        else:
            dominan_disease = "sehat"
        
        status = "warning" if dominan_disease != "sehat" else "sehat"

        # Ambil info AI/Fallback SETELAH fix dominant disease
        info = await get_ai_explanation(dominan_disease, dominan_confidence_avg)

        result = {
            "timestamp": datetime.now(timezone.utc).isoformat(),
            "dominan_disease": dominan_disease,
            "dominan_confidence_avg": dominan_confidence_avg,
            "jumlah_disease_terdeteksi": jumlah_penyakit_terdeteksi,
            "sensor_rata_rata": sensor_data,
            "status": status,
            "info": info
        }
        
        global last_save_time
        now = datetime.now(timezone.utc)
        
        saved = False
        skip_reason = None
        
        if save:
            time_since_last_save = (now - last_save_time).total_seconds()
            if time_since_last_save >= SAVE_INTERVAL_SECONDS:
                save_res = await save_detection_result(result, user_id=user_id if user_id else "autoSimpan")
                saved = bool(save_res and save_res.get("ok"))
                if saved:
                    last_save_time = now
                    # print("[AUTO SAVE] berhasil. Next simpan {}s lagi.".format(SAVE_INTERVAL_SECONDS)) # Hapus log berulang
                else:
                    skip_reason = "save failed: {}".format(save_res.get('reason', 'unknown') if save_res else 'unknown')
                    print("[AUTO SAVE] gagal {}".format(skip_reason))
            else:
                skip_reason = "skipped: only {}s since last save (need {}s)".format(int(time_since_last_save), SAVE_INTERVAL_SECONDS)
                # print("[AUTO SKIP] {}".format(skip_reason)) # Hapus log berulang
            
        return JSONResponse(content={"saved_this_frame": saved, "result": result, "skip_reason": skip_reason})
        
    except HTTPException:
        raise
    except Exception as e:
        traceback.print_exc()
        raise HTTPException(status_code=500, detail=str(e))

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
        raise HTTPException(status_code=500, detail=str(e))

@app.get("/detections/{detection_id}")
async def get_detection(detection_id: str):
    try:
        loop = asyncio.get_running_loop()
        det = await loop.run_in_executor(None, fetch_detection_by_key, detection_id) 
        if not det:
            raise HTTPException(status_code=404, detail="Deteksi ga masuk ke firebase")
        return det
    except HTTPException:
        raise
    except Exception as e:
        import traceback as _tb; _tb.print_exc()
        raise HTTPException(status_code=500, detail={"gagal":str(e)})

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

@app.post("/sensor/data")
async def save_sensor_data(sensor_data: SensorData):
    """Save sensor data dari simulator/IoT device"""
    try:
        # print(f"[SENSOR] Received data: {sensor_data}") # Hapus log berulang
        
        # Save ke Firebase sensor_data/{timestamp}
        if firebase_app is not None:
            loop = asyncio.get_running_loop()
            def _save_sensor():
                # Menggunakan integer timestamp sebagai key
                timestamp = int(time.time()) 
                ref = firebase_db.reference("sensor_data/" + str(timestamp))
                return ref.set(sensor_data.dict()) # Menggunakan .dict() dari Pydantic
            
            await loop.run_in_executor(None, _save_sensor)
            # print(f"[SENSOR] Saved to sensor_data/{int(time.time())}") # Hapus log berulang
        
        return JSONResponse(content={
            "success": True,
            "message": "Sensor data berhasil tersimpan yeay",
            "data": sensor_data.dict()
        })
        
    except Exception as e:
        print(f"[SENSOR] Error simpan data: {e}")
        import traceback as _tb; tb = _tb.format_exc()
        print("[SENSOR] Traceback:", tb)
        return JSONResponse(
            status_code=500, 
            content={
                "success": False, 
                "error": str(e), 
                "trace": tb
            }
        )
        
# ENDPOINT AMBIL DATA SENSOR
@app.get("/sensor/data")
async def get_sensor_data():
    """Get 10 data sensor terakhir dari sensor_data"""
    try:
        if firebase_app is None:
            return JSONResponse(content={"success": False, "message": "Ga nemu firebasenya"})
        loop = asyncio.get_running_loop()
        def _get_sensor():
            # order_by_key().limit_to_last(10) untuk data 10 terakhir (berdasarkan timestamp integer key)
            ref = firebase_db.reference("sensor_data").order_by_key().limit_to_last(10)
            return ref.get()
        data = await loop.run_in_executor(None, _get_sensor)
        
        return JSONResponse(content={
            "success": True,
            "data": data,
            "count": len(data) if data else 0
        })
        
    except Exception as e:
        print(f"[SENSOR] Error getting sensor data: {e}")
        import traceback as _tb
        print(f"[SENSOR] Traceback: {_tb.format_exc()}")
        return JSONResponse(
            status_code=500,
            content={"success": False, "error": str(e), "trace": _tb.format_exc()}
        )

# ENDPOINT BACA DATA SENSOR AVERAGE
@app.get("/sensor/average")
async def get_sensor_average():
    try:
        if firebase_app is None:
            return JSONResponse(content={"success": False, "message": "Firebase not available"})
        
        loop = asyncio.get_running_loop()
        def _get_average():
            # order_by_key().limit_to_last(15) untuk 15 data terakhir
            ref = firebase_db.reference("sensor_data").order_by_key().limit_to_last(15)
            data = ref.get()
            if not data:
                return None
            
            suhu_values = []
            kelembapan_values = []
            cahaya_values = []
            
            for timestamp in data:
                sensor = data[timestamp]
                # Pastikan konversi tipe data
                suhu_values.append(float(sensor.get("suhu", 0)))
                kelembapan_values.append(int(sensor.get("kelembapan", 0)))
                cahaya_values.append(int(sensor.get("cahaya", 0)))
                
            count = len(suhu_values)
            if count == 0:
                return None

            avg_suhu = round(sum(suhu_values) / count, 1)
            avg_kelembapan = round(sum(kelembapan_values) / count)
            avg_cahaya = round(sum(cahaya_values) / count)
            
            status = "Normal"
            if avg_suhu > 30.5:
                status = "Warning - Suhu Tinggi"
            elif avg_kelembapan < 57:
                status = "Warning - Kelembapan Rendah"
            elif avg_cahaya > 1400:
                status = "Warning - Cahaya Tinggi"
            return {
                "suhu": avg_suhu,
                "kelembapan": avg_kelembapan,
                "cahaya": avg_cahaya,
                "status": status,
                "readings_count": count,
                "period": "15 readings", # Diubah ke jumlah readings, bukan 30s
                "timestamp": datetime.now(timezone.utc).isoformat()
            }
        average = await loop.run_in_executor(None, _get_average)
        
        if average:
            return JSONResponse(content={
                "success": True,
                "data": average
            })
        else:
            return JSONResponse(content={
                "success": False,
                "message": "No sensor data available"
            })
        
    except Exception as e:
        print(f"[SENSOR] Error getting average: {e}")
        import traceback as _tb
        print(f"[SENSOR] Traceback: {_tb.format_exc()}")
        return JSONResponse(
            status_code=500,
            content={"success": False, "error": str(e), "trace": _tb.format_exc()}
        )

if __name__ == "__main__":
    import uvicorn
    host = os.getenv("API_HOST", "0.0.0.0")
    port = int(os.getenv("API_PORT", 8001)) 
    
    uvicorn.run("main:app", host=host, port=port, reload=True, log_level="info")