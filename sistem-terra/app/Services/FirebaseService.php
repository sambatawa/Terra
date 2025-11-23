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

    public function __construct()
    {
        try {
            $credentialsPath = config('firebase.credentials');
            if (!file_exists($credentialsPath)) {
                $candidates = [
                    storage_path('app/firebase-credentials.json'),
                    base_path('backendapi/firebase-credentials.json'),
                    base_path('firebase-credentials.json')
                ];
                foreach ($candidates as $p) {
                    if (file_exists($p)) { $credentialsPath = $p; break; }
                }
            }
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Firebase credentials file not found at: {$credentialsPath}");
            }

            $databaseUrl = config('firebase.database_url') ?: config('firebase.realtime_database.url');
            if (!$databaseUrl) {
                $databaseUrl = 'https://terra-145a1-default-rtdb.asia-southeast1.firebasedatabase.app';
            }

            $this->firebase = (new Factory)
                ->withServiceAccount($credentialsPath)
                ->withDatabaseUri($databaseUrl);

            $this->database = $this->firebase->createDatabase();
        } catch (\Exception $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Save detection result to Firebase Realtime Database
     */
    public function saveDetection($userId, $detectionData)
    {
        try {
            $path = "detections/{$userId}/" . time();
            
            $data = [
                'user_id' => $userId,
                'label' => $detectionData['label'] ?? '',
                'dominan_disease' => $detectionData['dominan_disease'] ?? '',
                'confidence' => $detectionData['confidence'] ?? 0,
                'dominan_confidence_avg' => $detectionData['dominan_confidence_avg'] ?? 0,
                'jumlah_disease_terdeteksi' => $detectionData['jumlah_disease_terdeteksi'] ?? [],
                'sensor_rata_rata' => $detectionData['sensor_rata_rata'] ?? [],
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
            throw $e;
        }
    }

    /**
     * Get detections for a user
     */
    public function getDetections($userId, $limit = 50)
    {
        try {
            $path = "detections/{$userId}";
            $reference = $this->database->getReference($path);
            $snapshot = $reference->orderByChild('timestamp')->limitToLast($limit)->getValue();

            if (!$snapshot) {
                return [];
            }

            // Convert to array and reverse to get latest first
            $detections = array_reverse($snapshot, true);
            
            return array_map(function ($key, $value) {
                return array_merge($value, ['id' => $key]);
            }, array_keys($detections), $detections);
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

