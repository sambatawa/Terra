<div align="center">
  <h1>Terra</h1>
  <p><strong>Monitoring dan AI Detection Disease menggunakan Eggplant Leaf</strong></p>
    <p>
    <a href="https://drive.google.com/file/d/1SkTklubLvwbqTvXfpLog8QmYiTl9U5Ps/view?usp=sharing">📁 Source Code lengkap di drive</a>
    <a href="https://terra.cervosys.app/">Terra website</a>
  </p>
</div>

## Tentang Proyek

**Terra** adalah platform pertanian cerdas yang mengintegrasikan teknologi AI, IoT, dan machine learning untuk membantu petani Indonesia dalam monitoring kesehatan tanaman dan meningkatkan produktivitas.
- **AI-Powered Disease Detection** - Deteksi penyakit tanaman real-time
- **IoT Sensor Integration** - Monitoring kondisi lingkungan
- **Digital Marketplace** - Platform jual beli produk pertanian
- **Expert Community** - Forum diskusi dengan ahli pertanian
- **Data Analytics** - Insight untuk pengambilan keputusan

## Arsitektur Sistem

```
Terra-Sm5Pro/
├── sistem-terra/            
│   ├── app/                   
│   ├── resources/           
│   ├── public/            
│   └── database/       
├── backendapi/     
│   ├── main.py          
│   └── requirements.txt
```

### Komunikasi Sistem
- **Frontend (Laravel)** ↔ **Backend (FastAPI)** via REST API
- **Real-time Data** melalui **Firebase Realtime Database**
- **AI Model Inference** via **ONNX Runtime** (client-side & server-side)
- **IoT Sensors** → **Firebase** → **Frontend Dashboard**

## Fitur Utama

### AI Disease Detection
- **7 Disease Classes**: Aphids, Bercak Cercospora, Layu Fusarium, Mosaic Virus, Phytophthora, Powdery Mildew, Sehat
- **Real-time Processing**: WASM onnxruntime
- **Confidence Scoring**: Probability analysis untuk setiap deteksi
- **Auto-save Integration**: Hasil deteksi tersimpan otomatis ke Firebase
- **Historical Tracking**: Statistik penyakit per periode waktu

### IoT Monitoring System
- **Multi-sensor Support**: Suhu, kelembapan, intensitas cahaya
- **Real-time Dashboard**: Live data streaming setiap 2 detik
- **Alert System**: Notifikasi untuk threshold violations
- **Geolocation Integration**: GPS tracking dengan Google Maps

### Digital Marketplace
- **Product Management**: Upload, edit, hapus produk pertanian
- **Image Processing**: Resize & optimize product images
- **Stock Tracking**: Real-time inventory management
- **Recommendation Engine**: AI-powered product suggestions
- **Transaction History**: Complete audit trail

### Community Forum
- **Discussion Threads**: Kategorisasi topik permasalahan
- **Engagement Metrics**: Like, comment, view tracking
- **Expert Badges**: Verification system untuk ahli pertanian
- **Search & Filter**: Advanced content discovery


## 📦 Instalasi & Setup

### 1. Clone Repository
```bash
git clone https://github.com/sambatawa/Terra-Sm5Pro.git
cd Terra-Sm5Pro
```

### 2. Frontend Setup (Laravel)
```bash
cd sistem-terra

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Env
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed

# Build 
npm run build

# Link storage
php artisan storage:link

# Start development server
php artisan serve
# Frontend: http://localhost:8000
```

### 3. Backend Setup (FastAPI)
```bash
cd ../backendapi

# env venv
python -m venv venv

# Aktivasi venv
# Windows: venv\Scripts\activate
# Linux/Mac: source venv/bin/activate

# Install dependencies
pip install -r requirements.txt

# Start FastAPI server
uvicorn main:app --reload --host 0.0.0.0 --port=8001
# Backend: http://localhost:8001
```

### 4. Firebase Configuration
```bash
# 1. Buat project di Firebase Console
# 2. Enable Realtime Database
# 3. Download service account key
# 4. Simpan ke sistem-terra/storage/app/firebase-credentials.json
# 5. Update .env configuration
```

### AI Model Configuration
```yaml
Primary Model: public/models/best.onnx
Backup Model: public/models/best.pt
Model Classes: 7 disease types + healthy
Input Size: 640x640 pixels
Confidence Threshold: 0.3 (configurable)
Inference Engine: ONNX Runtime Web
```

### Sensor Integration
```yaml
Supported Sensors:
  - Temperature: BME280
  - Humidity: BME280
  - Light: BH1750
Data Format: JSON
Storage: Firebase Realtime Database
```

## Deployment Guide

### Development Environment
```bash
# Frontend (Laravel)
cd sistem-terra
php artisan serve --host=0.0.0.0 --port=8000

# Backend (FastAPI)
cd backendapi
uvicorn main:app --reload --host=0.0.0.0 --port=8001

# Assets (Vite)
npm run dev
```

### Production Setup
```bash
# 1. Depedencies
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# 2. Cache konfigurasi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Environment Setup
APP_ENV=production
APP_DEBUG=false

```

## Testing & Quality Assurance

### Laravel Testing
```bash
php artisan test

# Run specific test suite
php artisan test --filter Feature/AuthTest
php artisan test --filter Unit/ServiceTest

# Generate coverage report
php artisan test --coverage
```

### FastAPI Testing
```bash
# Install test dependencies
pip install pytest pytest-asyncio httpx

# Run tests
pytest tests/

# Run with coverage
pytest --cov=. tests/
```

### Quality Checks
```bash
# PHP Code Style
php artisan pint

# JavaScript Linting
npm run lint

# Security Audit
composer audit
npm audit
```
<div align="center">
  <p><strong>Memberdayakan Pertanian Terung Ungu dengan Teknologi</strong></p>
</div>
