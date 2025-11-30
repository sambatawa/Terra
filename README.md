<div align="center">
   <img src="https://raw.githubusercontent.com/sambatawa/Terra-Sm5Pro/main/sistem-terra/public/img/logo.png" alt="Terra Ecosystem" width="200">
  <h1>Terra</h1>
  <p><strong>Monitoring dan AI Detection Disease menggunakan Eggplant Leaf</strong></p>
  
  [![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20.svg?style=flat&logo=Laravel)](https://laravel.com)
  [![FastAPI](https://img.shields.io/badge/FastAPI-0.104-009688.svg?style=flat&logo=FastAPI)](https://fastapi.tiangolo.com)
  [![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC.svg?style=flat&logo=Tailwind-CSS)](https://tailwindcss.com)
  [![Firebase](https://img.shields.io/badge/Firebase-Realtime%20Database-FFCA28.svg?style=flat&logo=Firebase)](https://firebase.google.com)
  [![ONNX](https://img.shields.io/badge/ONNX-Runtime-005CED.svg?style=flat&logo=ONNX)](https://onnx.ai)
  [![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
</div>

## Tentang Proyek

**Terra** adalah platform pertanian cerdas yang mengintegrasikan teknologi AI, IoT, dan machine learning untuk membantu petani Indonesia dalam monitoring kesehatan tanaman dan meningkatkan produktivitas.

### Visi & Misi
- **Visi**: Menjadi leading smart farming platform di Indonesia
- **Misi**: Memberdayakan petani Terung Ungu dengan teknologi modern untuk pertanian berkelanjutan

### Nilai Utama
- **AI-Powered Disease Detection** - Deteksi penyakit tanaman real-time
- **IoT Sensor Integration** - Monitoring kondisi lingkungan
- **Digital Marketplace** - Platform jual beli produk pertanian
- **Expert Community** - Forum diskusi dengan ahli pertanian
- **Data Analytics** - Insight untuk pengambilan keputusan

## Arsitektur Sistem

```
Terra-Sm5Pro/
├── sistem-terra/              # Laravel Frontend Application
│   ├── app/                   # Core application logic
│   ├── resources/             # Views & assets
│   ├── public/                # Static files & AI models
│   └── database/              # Migrations & seeders
├── backendapi/                # FastAPI Backend Services
│   ├── main.py               # FastAPI application entry
│   ├── requirements.txt      # Python dependencies
│   └── (AI/ML services)      # Model inference services
```

### Komunikasi Sistem
- **Frontend (Laravel)** ↔ **Backend (FastAPI)** via REST API
- **Real-time Data** melalui **Firebase Realtime Database**
- **AI Model Inference** via **ONNX Runtime** (client-side & server-side)
- **IoT Sensors** → **Firebase** → **Frontend Dashboard**

## Fitur Utama

### AI Disease Detection
- **7 Disease Classes**: Aphids, Bercak Cercospora, Layu Fusarium, Mosaic Virus, Phytophthora, Powdery Mildew, Sehat
- **Real-time Processing**: WebRTC camera feed dengan ONNX.js
- **Confidence Scoring**: Probability analysis untuk setiap deteksi
- **Auto-save Integration**: Hasil deteksi tersimpan otomatis ke Firebase
- **Historical Tracking**: Statistik penyakit per periode waktu

### IoT Monitoring System
- **Multi-sensor Support**: Suhu, kelembapan, intensitas cahaya
- **Real-time Dashboard**: Live data streaming setiap 2 detik
- **Alert System**: Notifikasi untuk threshold violations
- **Geolocation Integration**: GPS tracking dengan Google Maps
- **Data Export**: PDF reporting untuk analisis

### Digital Marketplace
- **Product Management**: Upload, edit, hapus produk pertanian
- **Image Processing**: Resize & optimize product images
- **Stock Tracking**: Real-time inventory management
- **Recommendation Engine**: AI-powered product suggestions
- **Transaction History**: Complete audit trail

### Community Forum
- **Q&A Platform**: Tanya jawab dengan expert moderation
- **Discussion Threads**: Kategorisasi topik permasalahan
- **Engagement Metrics**: Like, comment, view tracking
- **Expert Badges**: Verification system untuk ahli pertanian
- **Search & Filter**: Advanced content discovery

## Tech Stack

### Frontend Stack
```yaml
Framework: Laravel 12.0
Language: PHP 8.2+
Styling: TailwindCSS 3.x
JavaScript: Alpine.js + Vanilla JS
Build Tool: Vite 7.0
AI/ML: ONNX Runtime Web
Real-time: Firebase SDK
Authentication: Laravel Breeze
PDF: DomPDF
```

### Backend Stack
```yaml
Framework: FastAPI 0.104+
Language: Python 3.11+
AI/ML: PyTorch, ONNX, OpenCV
Database: Firebase Realtime Database
Documentation: OpenAPI/Swagger
Async: asyncio support
```

### Infrastructure
```yaml
Web Server: Apache/Nginx
Database: MySQL 8.0+
Cache: Redis/File Storage
Real-time: Firebase Realtime Database
```

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

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
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

# Create virtual environment
python -m venv venv

# Activate virtual environment
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

## Security & Authentication

### Laravel Authentication
- **Breeze Stack**: Laravel Breeze dengan Blade templates
- **Email Verification**: Required untuk account activation
- **Session Management**: Secure session handling
- **CSRF Protection**: Built-in CSRF token validation
- **Password Hashing**: bcrypt dengan proper salting

### Firebase Security
- **Service Account**: Private key authentication
- **Database Rules**: Role-based data access
- **API Key Restrictions**: Limited ke specific domains
- **Data Validation**: Client & server-side validation

### API Security
- **Rate Limiting**: Prevent abuse dan DDoS
- **CORS Configuration**: Proper cross-origin setup
- **Input Validation**: Comprehensive data sanitization
- **Error Handling**: Secure error responses

## 🌐 Deployment Guide

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
# 1. Optimize Dependencies
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# 2. Cache Configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Environment Setup
APP_ENV=production
APP_DEBUG=false

```

## 🧪 Testing & Quality Assurance

### Laravel Testing
```bash
# Run all tests
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
  <h3>🌱 Dibuat dengan ❤️ untuk Petani Indonesia</h3>
  <p><strong>Memberdayakan Pertanian dengan Teknologi</strong></p>
  <p>© 2025 Terra (AI DETECTIONS EGGPLANT LEAF). Semua hak dilindungi.</p>
  
  <p>
    <a href="#top">⬆️ Kembali ke Atas</a> •
    <a href="https://github.com/sambatawa/Terra-Sm5Pro">📁 Source Code</a> •
    <a href="https://docs.terra-pro5.com">📖 Dokumentasi</a>
  </p>
</div>
