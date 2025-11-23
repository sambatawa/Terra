<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Detection;
use App\Models\ProductClick;
use App\Models\Post;
use App\Models\User;
use App\Services\DetectionService;
use App\Services\FirebaseService;
use App\Constants\DiseaseClasses;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $data = [];
        $data['detections'] = collect([]);

        // Ambil detections untuk SEMUA role dari Firebase (user + manual_user + auto_feed)
        try {
            $firebaseService = new FirebaseService();
            $userDet = $firebaseService->getDetections($user->id, 200);
            $manualDet = $firebaseService->getDetections('manual_user', 200);
            $autoDet = $firebaseService->getDetections('auto_feed', 200);
            $anonDet = $firebaseService->getDetections('anonymous', 200);

            $all = array_merge($userDet ?: [], $manualDet ?: [], $autoDet ?: [], $anonDet ?: []);

            usort($all, function ($a, $b) {
                $getTs = function ($x) {
                    $t = null;
                    if (is_array($x)) {
                        $t = $x['timestamp'] ?? ($x['created_at'] ?? null);
                    } else {
                        $t = $x->timestamp ?? ($x->created_at ?? null);
                    }
                    if (is_numeric($t)) {
                        return (int)$t;
                    } elseif ($t instanceof \Carbon\Carbon) {
                        return $t->getTimestamp();
                    } elseif (is_string($t)) {
                        return strtotime($t) ?: 0;
                    }
                    return 0;
                };
                return $getTs($b) <=> $getTs($a);
            });

            $data['detections'] = collect(array_slice($all, 0, 100))->map(function ($item) {
                return (object) $item;
            });
        } catch (\Exception $e) {
            Log::error('Firebase error getting detections', ['error' => $e->getMessage()]);
            $data['detections'] = collect([]);
        }

        // Fallback: jika kosong, ambil dari backend API (FastAPI) via DetectionService
        if ($data['detections']->isEmpty()) {
            try {
                $detService = new DetectionService();
                $apiRes = $detService->getRecentDetections(100);
                $list = $apiRes['detections'] ?? [];
                usort($list, function ($a, $b) {
                    $aTs = isset($a['timestamp']) ? strtotime($a['timestamp']) : 0;
                    $bTs = isset($b['timestamp']) ? strtotime($b['timestamp']) : 0;
                    return $bTs <=> $aTs;
                });
                $data['detections'] = collect(array_slice($list, 0, 100))->map(function ($item) {
                    return (object) $item;
                });
            } catch (\Exception $e) {
                Log::error('Fallback API get detections failed', ['error' => $e->getMessage()]);
            }
        }

        // 1. LOGIKA PETANI & TEKNISI (Log Sensor)
        if ($role == 'petani' || $role == 'teknisi') {
            // Dummy Sensor Data
            $data['sensors'] = [
                ['time' => now()->subMinutes(10), 'suhu' => 29.5, 'kelembaban' => 60, 'status' => 'Normal'],
                ['time' => now()->subMinutes(30), 'suhu' => 31.2, 'kelembaban' => 55, 'status' => 'Warning'],
                ['time' => now()->subHour(1), 'suhu' => 28.8, 'kelembaban' => 65, 'status' => 'Normal'],
            ];
        }
        // 2. LOGIKA PENJUAL (Lihat Klik Produk)
        elseif ($role == 'penjual') {
            $data['clicks'] = ProductClick::where('seller_id', $user->id)->latest()->get();
            $data['total_clicks'] = $data['clicks']->count();
        } 
        // 3. LOGIKA PENYULUH (Lihat Aktivitas Forum) -> INI YANG BARU
        elseif ($role == 'penyuluh') {
            $data['posts'] = Post::where('user_id', $user->id)
                                 ->withCount('comments', 'likes')
                                 ->latest()
                                 ->get();
            
            // Hitung total interaksi (Like + Komen yang diterima)
            $data['total_posts'] = $data['posts']->count();
            $data['total_interactions'] = $data['posts']->sum('likes_count') + $data['posts']->sum('comments_count');
        }

        return view('history.index', compact('data', 'role'));
    }

    // FUNGSI EXPORT CSV (UPDATE JUGA BIAR PENYULUH BISA DOWNLOAD)
    public function export()
    {
        $user = Auth::user();
        $role = $user->role;
        $fileName = 'Laporan-Terra-' . ucfirst($role) . '-' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [];
        $query = [];

        if ($role == 'petani' || $role == 'teknisi') {
            $columns = ['Waktu', 'Hasil Deteksi', 'Confidence', 'Status', 'Ciri', 'Rekomendasi'];
            // Get from Firebase
            try {
                $firebaseService = new FirebaseService();
                $userDet = $firebaseService->getDetections($user->id, 1000);
                $manualDet = $firebaseService->getDetections('manual_user', 1000);
                $autoDet = $firebaseService->getDetections('auto_feed', 1000);
                $anonDet = $firebaseService->getDetections('anonymous', 1000);

                $all = array_merge($userDet ?: [], $manualDet ?: [], $autoDet ?: [], $anonDet ?: []);

                usort($all, function ($a, $b) {
                    $getTs = function ($x) {
                        $t = null;
                        if (is_array($x)) {
                            $t = $x['timestamp'] ?? ($x['created_at'] ?? null);
                        } else {
                            $t = $x->timestamp ?? ($x->created_at ?? null);
                        }
                        if (is_numeric($t)) {
                            return (int)$t;
                        } elseif ($t instanceof \Carbon\Carbon) {
                            return $t->getTimestamp();
                        } elseif (is_string($t)) {
                            return strtotime($t) ?: 0;
                        }
                        return 0;
                    };
                    return $getTs($b) <=> $getTs($a);
                });

                $query = collect(array_slice($all, 0, 1000))->map(function ($item) {
                    return (object) $item;
                });
            } catch (\Exception $e) {
                Log::error('Firebase error in export', ['error' => $e->getMessage()]);
                $query = collect([]);
            }
        } elseif ($role == 'penjual') {
            $columns = ['Waktu', 'Nama Produk', 'Ket'];
            $query = ProductClick::where('seller_id', $user->id)->latest()->get();
        } elseif ($role == 'penyuluh') {
            $columns = ['Tanggal Post', 'Isi Konten', 'Jumlah Like', 'Jumlah Komentar'];
            $query = Post::where('user_id', $user->id)->withCount('comments', 'likes')->latest()->get();
        }

        $callback = function() use ($query, $columns, $role) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($query as $row) {
                $dataRow = [];
                if ($role == 'petani' || $role == 'teknisi') {
                    $ciri = (is_array($row->info) ? ($row->info['ciri'] ?? '-') : '-');
                    $rekomendasi = (is_array($row->info) ? ($row->info['rekomendasi_penanganan'] ?? '-') : '-');
                    // Normalize class name untuk konsistensi
                    $diseaseName = DiseaseClasses::normalize($row->dominan_disease ?? $row->label ?? '');
                    // Handle created_at - bisa dari Firebase timestamp atau created_at
                    $createdAt = isset($row->created_at) 
                        ? (is_string($row->created_at) ? \Carbon\Carbon::parse($row->created_at) : $row->created_at)
                        : (isset($row->timestamp) ? \Carbon\Carbon::createFromTimestamp($row->timestamp) : now());
                    $dataRow = [
                        $createdAt->format('Y-m-d H:i:s'),
                        DiseaseClasses::getDisplayName($diseaseName),
                        ($row->confidence ?? 0) . '%',
                        $row->status ?? 'Recorded',
                        $ciri,
                        $rekomendasi
                    ];
                } elseif ($role == 'penjual') {
                    $dataRow = [$row->created_at, $row->product_name, 'User Click WA'];
                } elseif ($role == 'penyuluh') { // DATA CSV PENYULUH
                    $dataRow = [
                        $row->created_at->format('Y-m-d H:i'),
                        substr($row->content, 0, 50) . '...', // Potong teks biar rapi
                        $row->likes_count,
                        $row->comments_count
                    ];
                }
                fputcsv($file, $dataRow);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Simpan hasil deteksi dari frontend (robot.blade.php)
     * Bisa menerima data langsung atau gambar untuk diproses oleh API
     */
    public function storeDetection(Request $request, DetectionService $detectionService) {
        try {
            $user = Auth::user();
            
            // Jika ada file gambar
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('detections', 'public');
                $fullPath = storage_path('app/public/' . $imagePath);
                
                // Data sensor (opsional) - sesuai JSON structure
                $sensorData = null;
                if ($request->has('suhu') || $request->has('kelembapan') || $request->has('cahaya')) {
                    $sensorData = [
                        'suhu' => $request->input('suhu'),
                        'kelembapan' => $request->input('kelembapan'),
                        'cahaya' => $request->input('cahaya'),
                    ];
                }
                
                // Jika data deteksi sudah lengkap dari frontend (auto-save), gunakan data tersebut
                if ($request->has('dominan_disease') && $request->has('jumlah_disease_terdeteksi')) {
                    // Data deteksi sudah lengkap, tidak perlu memproses ulang
                    $normalizedDisease = DiseaseClasses::normalize($request->input('dominan_disease'));
                    $detectionCounts = json_decode($request->input('jumlah_disease_terdeteksi'), true) ?? [];
                    $completeCounts = array_merge(DiseaseClasses::DEFAULT_DETECTION_COUNTS, $detectionCounts);
                    
                    // Confidence: jika sudah dalam persen (0-100), gunakan langsung; jika dalam decimal (0-1), konversi ke persen
                    $confidenceInput = $request->input('confidence');
                    $confidencePercent = is_numeric($confidenceInput) 
                        ? (float)$confidenceInput > 1 ? (int)$confidenceInput : (int)((float)$confidenceInput * 100)
                        : 0;
                    
                    // dominan_confidence_avg: nilai asli dari model AI (0-1)
                    $dominanConfidenceAvg = (float)($request->input('dominan_confidence_avg') ?? 0);
                    
                    // Buat info sederhana jika tidak ada
                    $info = [
                        'ciri' => 'Deteksi otomatis dari sistem realtime',
                        'rekomendasi_penanganan' => 'Pantau kondisi tanaman secara berkala'
                    ];
                    
                    Log::info('Saving detection (auto-save)', [
                        'user_id' => $user->id,
                        'dominan_disease' => $normalizedDisease,
                        'confidence' => $confidencePercent,
                        'dominan_confidence_avg' => $dominanConfidenceAvg,
                        'status' => $request->input('status', 'sehat'),
                        'image_path' => $imagePath
                    ]);
                    
                    try {
                        // Prepare detection data
                        $detectionData = [
                            'label' => $normalizedDisease,
                            'dominan_disease' => $normalizedDisease,
                            'confidence' => $confidencePercent, // 0-100
                            'dominan_confidence_avg' => $dominanConfidenceAvg, // 0-1 (nilai asli dari model AI)
                            'jumlah_disease_terdeteksi' => $completeCounts,
                            'sensor_rata_rata' => $sensorData ?? [
                                'suhu' => $request->input('suhu', 26.9),
                                'kelembapan' => $request->input('kelembapan', 83.5),
                                'cahaya' => $request->input('cahaya', 42)
                            ],
                            'status' => $request->input('status', 'sehat'),
                            'image_snapshot' => $imagePath,
                            'info' => $info,
                        ];

                        // Save to Firebase only (real-time)
                        $firebaseService = new FirebaseService();
                        $firebaseResult = $firebaseService->saveDetection($user->id, $detectionData);
                        
                        Log::info('Detection saved to Firebase', [
                            'user_id' => $user->id,
                            'firebase_path' => $firebaseResult['path'] ?? 'N/A',
                            'dominan_disease' => $normalizedDisease
                        ]);
                        
                        return response()->json([
                            'success' => true,
                            'detection' => $firebaseResult['data'] ?? null,
                            'firebase_path' => $firebaseResult['path'] ?? null,
                            'message' => 'Deteksi berhasil disimpan ke Firebase (auto-save)'
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to save detection to Firebase', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e;
                    }
                }
                
                // Jika data belum lengkap, proses melalui API
                $result = $detectionService->detect($fullPath, $sensorData);
                
                // Normalize class name untuk konsistensi dengan backendapi
                $normalizedDisease = DiseaseClasses::normalize($result['dominan_disease']);
                
                // Pastikan jumlah_disease_terdeteksi memiliki semua class
                $detectionCounts = $result['jumlah_disease_terdeteksi'] ?? [];
                $completeCounts = array_merge(DiseaseClasses::DEFAULT_DETECTION_COUNTS, $detectionCounts);
                
                // Prepare detection data
                $detectionData = [
                    'label' => $normalizedDisease,
                    'dominan_disease' => $normalizedDisease,
                    'confidence' => (int)($result['dominan_confidence_avg'] * 100),
                    'dominan_confidence_avg' => $result['dominan_confidence_avg'],
                    'jumlah_disease_terdeteksi' => $completeCounts,
                    'sensor_rata_rata' => $result['sensor_rata-rata'] ?? $result['sensor_rata_rata'] ?? null,
                    'status' => $result['status'],
                    'image_snapshot' => $imagePath,
                    'info' => $result['info'],
                ];
                
                // Save to Firebase only
                $firebaseService = new FirebaseService();
                $firebaseResult = $firebaseService->saveDetection($user->id, $detectionData);
                
                Log::info('Detection saved to Firebase (from API)', [
                    'user_id' => $user->id,
                    'firebase_path' => $firebaseResult['path'] ?? 'N/A',
                    'dominan_disease' => $normalizedDisease
                ]);
                
                return response()->json([
                    'success' => true,
                    'detection' => $firebaseResult['data'] ?? null,
                    'firebase_path' => $firebaseResult['path'] ?? null,
                    'message' => 'Deteksi berhasil disimpan ke Firebase'
                ]);
            }
            
            // Jika data sudah lengkap dari frontend (backward compatibility)
            if ($request->has('label') && $request->has('confidence')) {
                // Normalize class names
                $normalizedDisease = DiseaseClasses::normalize($request->dominan_disease ?? $request->label);
                $detectionCounts = $request->jumlah_disease_terdeteksi ?? [];
                $completeCounts = array_merge(DiseaseClasses::DEFAULT_DETECTION_COUNTS, $detectionCounts);
                
                // Prepare detection data
                $detectionData = [
                    'label' => $normalizedDisease,
                    'dominan_disease' => $normalizedDisease,
                    'confidence' => $request->confidence,
                    'dominan_confidence_avg' => $request->dominan_confidence_avg ?? ($request->confidence / 100),
                    'jumlah_disease_terdeteksi' => $completeCounts,
                    'sensor_rata_rata' => $request->sensor_rata_rata ?? [],
                    'status' => $request->status ?? 'sehat',
                    'image_snapshot' => $request->image_snapshot ?? '',
                    'info' => $request->info ?? [],
                ];
                
                // Save to Firebase only
                $firebaseService = new FirebaseService();
                $firebaseResult = $firebaseService->saveDetection($user->id, $detectionData);
                
                return response()->json([
                    'success' => true,
                    'detection' => $firebaseResult['data'] ?? null,
                    'firebase_path' => $firebaseResult['path'] ?? null
                ]);
            }
            
            return response()->json(['success' => false, 'message' => 'Data tidak lengkap'], 400);
            
        } catch (\Exception $e) {
            Log::error('Store Detection Error', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan deteksi: ' . $e->getMessage()
            ], 500);
        }
    }
    public function trackClick(Request $request) {
        ProductClick::create(['seller_id' => $request->seller_id, 'product_name' => $request->product_name]);
        return response()->json(['success' => true]);
    }

    /**
     * API endpoint untuk mendapatkan detections (untuk real-time listener)
     */
    public function getDetections() {
        try {
            $user = Auth::user();
            $firebaseService = new FirebaseService();
            $u = $firebaseService->getDetections($user->id, 300);
            $m = $firebaseService->getDetections('manual_user', 300);
            $a = $firebaseService->getDetections('auto_feed', 300);
            $n = $firebaseService->getDetections('anonymous', 300);

            $all = array_merge($u ?: [], $m ?: [], $a ?: [], $n ?: []);

            usort($all, function ($a, $b) {
                $getTs = function ($x) {
                    $t = null;
                    if (is_array($x)) {
                        $t = $x['timestamp'] ?? ($x['created_at'] ?? null);
                    } else {
                        $t = $x->timestamp ?? ($x->created_at ?? null);
                    }
                    if (is_numeric($t)) {
                        return (int)$t;
                    } elseif ($t instanceof \Carbon\Carbon) {
                        return $t->getTimestamp();
                    } elseif (is_string($t)) {
                        return strtotime($t) ?: 0;
                    }
                    return 0;
                };
                return $getTs($b) <=> $getTs($a);
            });

            $detections = array_slice($all, 0, 100);

            return response()->json([
                'success' => true,
                'detections' => $detections
            ]);
        } catch (\Exception $e) {
            Log::error('Get detections error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}