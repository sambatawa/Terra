<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    protected $database;
    protected $firebase;
    protected bool $initialized = false;
    public function __construct()
    {
        try {
            $credentialsPath = env('FIREBASE_CREDENTIALS');
            if (!$credentialsPath || !file_exists($credentialsPath)) {
                $possiblePaths = [
                    storage_path('app/firebase/firebase-credentials.json'),
                    storage_path('app/firebase-credentials.json'),
                    base_path('firebase-credentials.json'),
                    base_path('backendapi/firebase-credentials.json'),
                ];
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $credentialsPath = $path;
                        Log::info('Firebase credentials found at: ' . $path);
                        break;
                    }
                }
            }

            if (!$credentialsPath || !file_exists($credentialsPath)) {
                Log::error('COBA CEK CREDENTIALS DATABASE FIREBASE .env', ['configured_path' => $credentialsPath]);
                $this->database = null;
                $this->initialized = false;
                return;
            }
            Log::info('Firebase di path: ' . $credentialsPath);

            $databaseUrl = env('FIREBASE_DATABASE_URL');

            if (!$databaseUrl) {
                Log::error('COBA CEK URL DATABASE FIREBASE .env');
                $this->database = null;
                $this->initialized = false;
                return;
            }

            $this->firebase = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri($databaseUrl);

            $this->database = $this->firebase->createDatabase();
            $this->initialized = true;
        } catch (\Exception $e) {
            Log::error('Firebase gagal inisialisasi: ' . $e->getMessage());
            $this->database = null;
            $this->initialized = false;
        }
    }

    /**
     * Save detection result to Firebase Realtime Database
     */
    public function saveDetection($userId, $detectionData)
    {
        try {
            if (!$this->database) {
                return [
                    'success' => false,
                    'message' => 'firebase not initialized'
                ];
            }
            $path = "detections/{$userId}/" . time();
            $randomSuhu = (mt_rand(280, 310) / 10);
            $randomHum = mt_rand(55, 65);
            $randomLux = mt_rand(800, 1500);
            $statusSensor = 'Normal';
            if ($randomSuhu > 30.5) {
                $statusSensor = 'Warning - Suhu Tinggi';
            } elseif ($randomHum < 57) {
                $statusSensor = 'Warning - Kelembapan Rendah';
            } elseif ($randomLux > 1400) {
                $statusSensor = 'Warning - Cahaya Tinggi';
            }
            
            $sensorData = [
                'suhu' => $randomSuhu,
                'kelembapan' => $randomHum, 
                'cahaya' => $randomLux,
                'status' => $statusSensor,
                'timestamp_sensor' => date('Y-m-d H:i:s')
            ];

            $data = [
                'user_id' => $userId,
                'label' => $detectionData['label'] ?? '',
                'dominan_disease' => $detectionData['dominan_disease'] ?? '',
                'confidence' => $detectionData['confidence'] ?? 0,
                'dominan_confidence_avg' => $detectionData['dominan_confidence_avg'] ?? 0,
                'jumlah_disease_terdeteksi' => $detectionData['jumlah_disease_terdeteksi'] ?? [],
                'sensor_data' => $sensorData,
                'status' => $detectionData['status'] ?? 'sehat',
                'image_snapshot' => $detectionData['image_snapshot'] ?? '',
                'info' => $detectionData['info'] ?? [],
                'created_at' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
            ];

            $reference = $this->database->getReference($path);
            $reference->set($data);
            Log::info('Detection saved to Firebase', [
                'user_id' => $userId,
                'path' => $path,
                'dominan_disease' => $detectionData['dominan_disease'] ?? ''
            ]);
            return [
                'success' => true,
                'path' => $path,
                'data' => $data
            ];
        } catch (\Exception $e) {
            Log::error('Firebase save error: ' . $e->getMessage(), [
                'user_id' => $userId,
                'error' => $e->getTraceAsString()
            ]);
            return [
                'success' => false,
                'message' => 'save failed'
            ];
        }
    }

    /**
     * Get detections for a user
     */
    public function getDetections($userId, $limit = 50)
    {
        try {
            Log::info('getDetections called', [
                'userId' => $userId,
                'limit' => $limit,
                'database_initialized' => $this->database ? true : false
            ]);  
            if (!$this->database) {
                Log::warning('Firebase database not initialized in getDetections', ['userId' => $userId]);
                return [];
            }

            $path = "detections/{$userId}";
            Log::info('Getting detections from path', ['path' => $path]);
            $reference = $this->database->getReference($path);
            $snapshot = $reference->getValue();
            Log::info('Firebase snapshot result', [
                'path' => $path,
                'snapshot_exists' => $snapshot ? true : false,
                'snapshot_count' => $snapshot ? count($snapshot) : 0,
                'snapshot_keys' => $snapshot ? array_keys($snapshot) : []
            ]);

            if (!$snapshot) {
                Log::info('No snapshot data found', ['path' => $path]);
                return [];
            }
            $detections = array_reverse($snapshot, true);
            $result = array_map(function ($key, $value) {
                return array_merge($value, ['id' => $key]);
            }, array_keys($detections), $detections);
            Log::info('Returning detections', [
                'path' => $path,
                'result_count' => count($result)
            ]);
            return $result;
        } catch (\Exception $e) {
            Log::error('Firebase get detections error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get database reference for real-time listener
     */
    public function getDatabase()
    {
        return $this->database;
    }
}

