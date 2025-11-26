<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SensorDataService
{
    /**
     * Generate random sensor data (mirip dengan sensor.blade.php)
     */
    public static function generateRandomSensorData(): array
    {
        $randomSuhu = (mt_rand(280, 310) / 10);
        $randomHum = mt_rand(55, 65);
        $randomLux = mt_rand(800, 1500);
        //THRESHOLD SENSOR
        $statusSensor = 'Normal';
        if ($randomSuhu > 30.5) {
            $statusSensor = 'Warning - Suhu Tinggi';
        } elseif ($randomHum < 57) {
            $statusSensor = 'Warning - Kelembaban Rendah';
        } elseif ($randomLux > 1400) {
            $statusSensor = 'Warning - Cahaya Tinggi';
        }
        return [
            'suhu' => $randomSuhu,
            'kelembaban' => $randomHum,
            'cahaya' => $randomLux,
            'status' => $statusSensor,
            'timestamp_sensor' => Carbon::now()->format('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Generate multiple sensor data for history
     */
    public static function generateSensorHistory(int $count = 10): array
    {
        $sensors = [];
        $now = Carbon::now();
        
        for ($i = 0; $i < $count; $i++) {
            $timestamp = $now->copy()->subMinutes($i * 10);
            $sensorData = self::generateRandomSensorData();
            $sensorData['time'] = $timestamp->toDateTimeString();
            
            $sensors[] = $sensorData;
        }
        return array_reverse($sensors);
    }
    
    /**
     * Get current sensor data (simulasi real-time)
     */
    public static function getCurrentSensorData(): array
    {
        return self::generateRandomSensorData();
    }
    
    /**
     * Ambil sensor data terbaru dari sensor_data
     */
    public static function getLatestSensorData(): ?array
    {
        try {
            $firebase = new FirebaseService();
            $database = $firebase->getDatabase();
            
            if (!$database) {
                Log::warning('Firebase ga dapet fungsi getLatestSensorData');
                return null;
            }
            
            //SENSOR DATA TERBARU
            $sensorDataRef = $database->getReference('sensor_data');
            $sensorDataSnapshot = $sensorDataRef->orderByKey()->limitToLast(1)->getSnapshot();
            if (!$sensorDataSnapshot->exists()) {
                Log::info('si folder sensor_data gada');
                return null;
            }
            $allSensorData = $sensorDataSnapshot->getValue();
            
            if (empty($allSensorData)) {
                Log::info('Ada tapi isinya kosong');
                return null;
            }
            //PAKE KEY YANG UDAH DIBIKIN
            $latestKey = array_key_last($allSensorData);
            $latestData = $allSensorData[$latestKey];
            
            Log::info('Latest sensor data retrieved', [
                'key' => $latestKey,
                'data' => $latestData
            ]);
            
            return [
                'suhu' => $latestData['suhu'] ?? 0,
                'kelembaban' => $latestData['kelembaban'] ?? 0,
                'cahaya' => $latestData['cahaya'] ?? 0,
                'status' => $latestData['status'] ?? 'Normal',
                'timestamp' => $latestKey,
                'readings_count' => 1,
                'period' => 'latest'
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting latest sensor data: ' . $e->getMessage());
            return null;
        }
    }
        
    /**
     * Generate and save sensor data to Firebase
     */
    public static function saveSensorDataToFirebase(): bool
    {
        try {
            $firebase = new FirebaseService();
            $database = $firebase->getDatabase();
            if (!$database) {
                Log::warning('Firebase gada saveSensorDataToFirebase');
                return true; 
            }
            //AKTIF GENERATE ATAS
            $sensorData = self::generateRandomSensorData();
            
            //SAVE YA
            $timestamp = time();
            $reference = $database->getReference('sensor_data/' . $timestamp);
            $reference->set($sensorData);
            Log::info('Sensor data saved to Firebase', [
                'timestamp' => $timestamp,
                'data' => $sensorData
            ]);
            return true;
            
        } catch (\Exception $e) {
            Log::error('Error saving sensor data to Firebase: ' . $e->getMessage());
            return false;
        }
    }
    
    //UPDATE DATA TERAKHIR SNAPSHOT
    public static function updateAllDetectionsWithSensorData($frontendSensorData = null): array
    {
        try {
            $firebase = new FirebaseService();
            $database = $firebase->getDatabase();
            
            if (!$database) {
                Log::warning('Firebase not available in updateAllDetectionsWithSensorData');
                return ['success' => false, 'message' => 'Firebase not available'];
            }
            $latestSensorData = self::getLatestSensorData();
            
            if (!$latestSensorData) {
                if ($frontendSensorData) {
                    $latestSensorData = [
                        'suhu' => $frontendSensorData['suhu'] ?? 28.5,
                        'kelembaban' => $frontendSensorData['kelembaban'] ?? 60,
                        'cahaya' => $frontendSensorData['cahaya'] ?? 1000,
                        'status' => $frontendSensorData['status'] ?? 'Normal',
                        'readings_count' => 1,
                        'period' => 'fallback'
                    ];
                    Log::info('Using frontend sensor data as fallback', $latestSensorData);
                } else {
                    $latestSensorData = [
                        'suhu' => 28.5,
                        'kelembaban' => 60,
                        'cahaya' => 1000,
                        'status' => 'Normal',
                        'readings_count' => 1,
                        'period' => 'random_fallback'
                    ];
                    Log::info('Using random sensor data as fallback', $latestSensorData);
                }
            }
            $sensorData = [
                'sensor_rata_rata' => [
                    'suhu' => $latestSensorData['suhu'],
                    'kelembapan' => $latestSensorData['kelembaban'],
                    'cahaya' => $latestSensorData['cahaya'],
                    'status' => $latestSensorData['status'],
                    'updated_at' => Carbon::now()->toISOString()
                ]
            ];
            
            $reference = $database->getReference('detections');
            $snapshot = $reference->getSnapshot();
            $allDetections = $snapshot->getValue();
            $updatedCount = 0;
            $errorCount = 0;
            
            if ($allDetections && is_array($allDetections)) {
                foreach ($allDetections as $category => $detections) {
                    if (is_array($detections)) {
                        foreach ($detections as $detectionId => $detectionData) {
                            try {
                                //INI FOLDER autoSimpan
                                if (!isset($detectionData['sensor_rata_rata']) && 
                                    !isset($detectionData['sensor_data']) && 
                                    !isset($detectionData['sensor_rata-rata'])) {
                                    $updateData = [
                                        'sensor_rata_rata' => $sensorData['sensor_rata_rata']
                                    ];
                                    $path = "detections/{$category}/{$detectionId}";
                                    $reference = $database->getReference($path);
                                    $reference->update($updateData);
                                    $updatedCount++;
                                    
                                    Log::info('Detection updated with sensor data (no previous data)', [
                                        'path' => $path,
                                        'detection_id' => $detectionId,
                                        'sensor_data' => $sensorData['sensor_rata_rata']
                                    ]);
                                } else {
                                    Log::info('Detection already has sensor data, skipping', [
                                        'path' => "detections/{$category}/{$detectionId}",
                                        'existing_fields' => array_keys(array_filter($detectionData, function($key) {
                                            return strpos($key, 'sensor') !== false;
                                        }, ARRAY_FILTER_USE_KEY))
                                    ]);
                                }
                                
                            } catch (\Exception $e) {
                                $errorCount++;
                                Log::error('Error updating detection with sensor data: ' . $e->getMessage(), [
                                    'path' => $path ?? 'unknown',
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                }
            } else {
                Log::warning('No detections found to update');
            }
            
            $result = [
                'success' => true,
                'updated' => $updatedCount,
                'errors' => $errorCount,
                'message' => "Updated {$updatedCount} detections with sensor data",
                'sensor_data_used' => $latestSensorData
            ];
            
            Log::info('Sensor data update completed', $result);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Error in updateAllDetectionsWithSensorData: ' . $e->getMessage());
            return [
                'success' => false, 
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ];
        }
    }
}