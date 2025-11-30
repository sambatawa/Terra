<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DetectionService;
use App\Services\FirebaseService;
use App\Constants\DiseaseClasses;
use Illuminate\Support\Facades\Log;
use App\Models\Detection;
use App\Models\ProductClick;
use App\Models\Post;
use App\Models\User;

class HistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $data = [];

        // 1. LOGIKA PETANI & TEKNISI (Lihat Deteksi & Sensor)
        if ($role == 'petani' || $role == 'teknisi') {
            // Ambil data dari Firebase Real-time Database
            $firebaseDetections = [];
            try {
                $firebaseService = new FirebaseService();
                // Auto save sekarang menggunakan user ID yang login
                $userData = $firebaseService->getDetections($user->id, null); // Ambil semua data
                $manualData = $firebaseService->getDetections('manual_user', null); // Ambil semua data
                $autoSimpanData = $firebaseService->getDetections('autoSimpan', 100); // Ambil 100 data terbaru
                $allFirebaseData = array_merge($userData, $manualData, $autoSimpanData);
                //sorting dengan timestamp terbaru
                usort($allFirebaseData, function($a, $b) {
                    $timeA = isset($a['timestamp']) ? (int)$a['timestamp'] : 0;
                    $timeB = isset($b['timestamp']) ? (int)$b['timestamp'] : 0;
                    return $timeB - $timeA;
                });
                $firebaseDetections = collect($allFirebaseData)->map(function($item) {
                    return (object) $item;
                });
                
                Log::info('Firebase data loaded', [
                    'autoSimpan_count' => count($autoSimpanData),
                    'user_count' => count($userData),
                    'manual_count' => count($manualData),
                    'total_count' => count($allFirebaseData)
                ]);
            } catch (\Exception $e) {
                Log::error('Firebase error: ' . $e->getMessage());
                $firebaseDetections = Detection::where('user_id', $user->id)->latest()->get();
            }
            
            $data['detections'] = $firebaseDetections;
            //GENERATE SENSOR ADA CONTROLLER DAN BLADE
            $generateSensor = function($time) {
                $suhu = mt_rand(280, 310) / 10;
                $hum = mt_rand(55, 65);
                $lux = mt_rand(800, 1500);
                $status = 'Normal';
                if ($suhu > 30.5) {
                    $status = 'Warning - Suhu Tinggi';
                } elseif ($hum < 57) {
                    $status = 'Warning - Kelembapan Rendah';
                } elseif ($lux > 1400) {
                    $status = 'Warning - Cahaya Tinggi';
                }
                return [
                    'time' => $time,
                    'suhu' => $suhu,
                    'kelembapan' => $hum,
                    'cahaya' => $lux,
                    'status' => $status
                ];
            };
            
            $data['sensors'] = [
                $generateSensor(now()->subMinutes(10)),
                $generateSensor(now()->subMinutes(30)),
                $generateSensor(now()->subHour(1))
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

    // FUNGSI EXPORT PDF (UPDATE JUGA BIAR PENYULUH BISA DOWNLOAD)
    public function export()
    {
        $user = Auth::user();
        $role = $user->role;
        $fileName = 'Laporan-Terra-' . ucfirst($role) . '-' . date('Y-m-d') . '.pdf';

        $headers = [
            "Content-type"        => "application/pdf",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];
        $processedData = [];

        if ($role == 'petani' || $role == 'teknisi') {
            try {
                $firebaseService = new FirebaseService();
                $userData = $firebaseService->getDetections($user->id, null);
                $manualData = $firebaseService->getDetections('manual_user', null);
                $autoSimpanData = $firebaseService->getDetections('autoSimpan', 100);
                $allFirebaseData = array_merge($userData, $manualData, $autoSimpanData);
                // Sorting seperti di index
                usort($allFirebaseData, function($a, $b) {
                    $timeA = isset($a['timestamp']) ? (int)$a['timestamp'] : 0;
                    $timeB = isset($b['timestamp']) ? (int)$b['timestamp'] : 0;
                    return $timeB - $timeA;
                });
                
                foreach ($allFirebaseData as $row) {
                    $timestamp = isset($row['timestamp']) ? 
                        \Carbon\Carbon::createFromTimestamp($row['timestamp']) : 
                        (isset($row['created_at']) ? $row['created_at'] : now());
                    
                    $detection = $row['dominan_disease'] ?? $row['label'] ?? 'Tidak Dikenali';
                    $confidence = $row['dominan_confidence_avg'] ?? $row['confidence'] ?? 0;
                    if ($confidence < 1) $confidence = $confidence * 100;
                    $sensorDataArray = $row['sensor_data'] ?? $row['sensor_rata_rata'] ?? $row->{'sensor_rata-rata'} ?? [];
                    if (is_string($sensorDataArray)) {
                        $sensorDataArray = json_decode($sensorDataArray, true) ?? [];
                    }
                    $suhu = $sensorDataArray['suhu'] ?? $row['suhu'] ?? 'N/A';
                    $kelembapan = $sensorDataArray['kelembapan'] ?? $row['kelembapan'] ?? 'N/A';
                    $cahaya = $sensorDataArray['cahaya'] ?? $row['cahaya'] ?? 'N/A';

                    $info = $row['info'] ?? [];
                    $ciri = $info['ciri'] ?? 'Tidak ada informasi';
                    $rekomendasi = $info['rekomendasi_penanganan'] ?? 'Tidak ada rekomendasi';
                    $isHealthy = (strtolower($detection) === 'sehat') || 
                                (isset($row['status']) && strtolower($row['status']) === 'sehat');
                    $statusText = $isHealthy ? 'Aman' : 'Perlu Tindakan';
                    $processedData[] = [
                        'waktu' => $timestamp->format('d M Y H:i'),
                        'detection' => $detection,
                        'confidence' => number_format($confidence, 1) . '%',
                        'status' => $statusText,
                        'suhu' => $suhu . '°C',
                        'kelembapan' => $kelembapan . '%', 
                        'cahaya' => $cahaya . ' Lux',
                        'ciri' => $ciri,
                        'rekomendasi' => $rekomendasi
                    ];
                }
            } catch (\Exception $e) {
                $detections = Detection::where('user_id', $user->id)->latest()->get();
                foreach ($detections as $row) {
                    $processedData[] = [
                        'waktu' => $row->created_at->format('d M Y H:i'),
                        'detection' => $row->label ?? 'Tidak Dikenali',
                        'confidence' => number_format($row->confidence, 1) . '%',
                        'status' => 'Recorded',
                        'suhu' => 'N/A',
                        'kelembapan' => 'N/A',
                        'cahaya' => 'N/A',
                        'ciri' => 'Tidak ada informasi',
                        'rekomendasi' => 'Tidak ada rekomendasi'
                    ];
                }
            }
        } elseif ($role == 'penjual') {
            $clicks = ProductClick::where('seller_id', $user->id)->latest()->get();
            foreach ($clicks as $row) {
                $processedData[] = [
                    'waktu' => $row->created_at->format('d M Y H:i'),
                    'product' => $row->product_name ?? 'Unknown',
                    'action' => 'User Click WA'
                ];
            }
        } elseif ($role == 'penyuluh') {
            $posts = Post::where('user_id', $user->id)->withCount('comments', 'likes')->latest()->get();
            foreach ($posts as $row) {
                $processedData[] = [
                    'waktu' => $row->created_at->format('d M Y H:i'),
                    'content' => substr($row->content ?? '', 0, 100) . '...',
                    'likes' => $row->likes_count ?? 0,
                    'comments' => $row->comments_count ?? 0
                ];
            }
        }
        try {
            $pdf = \Barryvdh\DomPDF\Facade\PDF::loadView('history.export-pdf', [
                'data' => $processedData,
                'role' => $role,
                'user' => $user,
                'fileName' => $fileName
            ]);
            
            return $pdf->download($fileName);
        } catch (\Exception $e) {
            Log::error('PDF generation error: ' . $e->getMessage());
            return $this->generateCSVFallback($processedData, $role, $user, $fileName);
        }
    }
    //Track Confidence and Label from FastAPI
    public function storeDetection(Request $request) {
        Detection::create(['user_id' => Auth::id(), 'label' => $request->label, 'confidence' => $request->confidence]);
        return response()->json(['success' => true]);
    }
    public function trackClick(Request $request) {
        ProductClick::create(['seller_id' => $request->seller_id, 'product_name' => $request->product_name]);
        return response()->json(['success' => true]);
    }

    /**
     * Refresh data dari Firebase untuk AJAX request
     */
    public function refresh(Request $request)
    {
        $user = Auth::user();
        try {
            $firebaseService = new FirebaseService();
            $autoSimpanData = $firebaseService->getDetections('autoSimpan', 100);
            $userData = $firebaseService->getDetections($user->id, 50);
            $manualData = $firebaseService->getDetections('manual_user', 50);
            $allFirebaseData = array_merge($autoSimpanData, $userData, $manualData);
            usort($allFirebaseData, function($a, $b) {
                $timeA = isset($a['timestamp']) ? (int)$a['timestamp'] : 0;
                $timeB = isset($b['timestamp']) ? (int)$b['timestamp'] : 0;
                return $timeB - $timeA;
            });
            Log::info('Firebase refresh data loaded', [
                'autoSimpan_count' => count($autoSimpanData),
                'user_count' => count($userData),
                'manual_count' => count($manualData),
                'total_count' => count($allFirebaseData)
            ]);
            return response()->json([
                'success' => true,
                'detections' => $allFirebaseData
            ]);
            
        } catch (\Exception $e) {
            Log::error('Firebase refresh error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data dari Firebase'
            ], 500);
        }
    }

    /**
     * API endpoint untuk auto-save detection dari FastAPI backend
     * Menggunakan API token sederhana untuk security
     */
    public function autoSaveDetection(Request $request) {
        $apiToken = $request->header('X-API-Token');
        $expectedToken = env('FASTAPI_TOKEN', 'terra-api-token-2024');
        if ($apiToken !== $expectedToken) {
            return response()->json(['success' => false, 'message' => 'Invalid API token'], 401);
        }

        $request->validate([
            'label' => 'required|string',
            'confidence' => 'required|numeric|min:0|max:100',
            'user_id' => 'nullable|integer|exists:users,id',
            'image_snapshot' => 'nullable|string'
        ]);
        $userId = $request->user_id ?? 1;
        // Simpan detection
        Detection::create([
            'user_id' => $userId,
            'label' => $request->label,
            'confidence' => (int)$request->confidence,
            'image_snapshot' => $request->image_snapshot
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Detection saved successfully'
        ]);
    }

    /**
     * Delete detection from Firebase
     */
    public function destroy($id)
    {
        try {
            $firebase = new FirebaseService();
            $database = $firebase->getDatabase();
            
            if (!$database) {
                return response()->json([
                    'success' => false,
                    'message' => 'Firebase not available'
                ], 500);
            }
            $paths = [
                "detections/autoSimpan/{$id}",
                "detections/manual_user/{$id}",
                "detections/{$id}"
            ];
            $deleted = false;
            foreach ($paths as $path) {
                try {
                    $reference = $database->getReference($path);
                    $snapshot = $reference->getSnapshot();
                    if ($snapshot->exists()) {
                        $reference->remove();
                        $deleted = true;
                        Log::info('Detection deleted from Firebase', ['path' => $path, 'id' => $id]);
                        break;
                    }
                } catch (\Exception $e) {
                    continue; 
                }
            }
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting detection: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data'
            ], 500);
        }
    }
}