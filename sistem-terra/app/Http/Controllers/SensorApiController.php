<?php

namespace App\Http\Controllers;

use App\Services\SensorDataService;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SensorApiController extends Controller
{
    /**
     * Get current sensor data (real-time simulation)
     */
    public function getCurrent(): JsonResponse
    {
        $sensorData = SensorDataService::getLatestSensorData();
        
        return response()->json([
            'success' => true,
            'data' => $sensorData,
            'timestamp' => now()->toISOString()
        ]);
    }
    
    /**
     * Get sensor history data
     */
    public function getHistory(Request $request): JsonResponse
    {
        $count = $request->get('count', 10);
        $sensorHistory = SensorDataService::generateSensorHistory($count);
        
        return response()->json([
            'success' => true,
            'data' => $sensorHistory,
            'count' => count($sensorHistory)
        ]);
    }
    
    /**
     * Generate and save sensor data to Firebase
     */
    public function generateAndSave(): JsonResponse
    {
        $success = SensorDataService::saveSensorDataToFirebase();
        
        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Sensor data generated and saved to Firebase'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save sensor data to Firebase'
            ], 500);
        }
    }
    
    /**
     * Get sensor data from Firebase
     */
    public function getFromFirebase(): JsonResponse
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
            
            $reference = $database->getReference('sensor_data');
            $snapshot = $reference->getSnapshot();
            $data = $snapshot->getValue();
            
            $sensorData = [];
            if ($data) {
                foreach ($data as $timestamp => $values) {
                    $values['timestamp'] = (int)$timestamp;
                    $sensorData[] = $values;
                }
                
                usort($sensorData, function($a, $b) {
                    return $b['timestamp'] - $a['timestamp'];
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => $sensorData,
                'count' => count($sensorData)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting sensor data: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Auto-update all detections with sensor data
     */
    public function autoUpdateDetections(Request $request)
    {
        try {
            $sensorData = $request->only(['suhu', 'kelembapan', 'cahaya', 'status']);
            $result = SensorDataService::updateAllDetectionsWithSensorData($sensorData);
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('API auto-update-detections error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Internal server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
