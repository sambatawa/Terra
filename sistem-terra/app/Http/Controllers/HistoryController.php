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
                $autoSimpanData = $firebaseService->getDetections('autoSimpan', 100);
                $userData = $firebaseService->getDetections($user->id, 50);
                $manualData = $firebaseService->getDetections('manual_user', 50);
                $allFirebaseData = array_merge($autoSimpanData, $userData, $manualData);
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
                
                // Debug: Log sample data dari autoSimpan
                if (!empty($autoSimpanData)) {
                    Log::info('Sample autoSimpan data:', [
                        'first_item' => $autoSimpanData[0],
                        'total_items' => count($autoSimpanData)
                    ]);
                } else {
                    Log::warning('autoSimpan data bisa', [
                        'autoSimpanData_type' => gettype($autoSimpanData),
                        'autoSimpanData_value' => $autoSimpanData
                    ]);
                }    
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
                    $status = 'Warning - Kelembaban Rendah';
                } elseif ($lux > 1400) {
                    $status = 'Warning - Cahaya Tinggi';
                }
                return [
                    'time' => $time,
                    'suhu' => $suhu,
                    'kelembaban' => $hum,
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
            $columns = ['Waktu', 'Hasil Deteksi', 'Confidence', 'Status'];
            $query = Detection::where('user_id', $user->id)->latest()->get();
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
                    $dataRow = [$row->created_at, $row->label, $row->confidence . '%', 'Recorded'];
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
            
            // Sort berdasarkan timestamp terbaru
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