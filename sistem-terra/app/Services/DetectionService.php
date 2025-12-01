<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class DetectionService
{
    private string $apiUrl;
    private int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('services.detection_api.url', env('BACKEND_API_URL', 'http://localhost:8001'));
        $this->timeout = config('services.detection_api.timeout', 30);
    }

    /**
     * @param string 
     * @param array|null 
     * @return array 
     * @throws Exception
     */
    public function detect(string $imagePath, ?array $sensorData = null): array
    {
        try {
            if (!file_exists($imagePath)) {
                throw new Exception("File gambar tidak ditemukan: {$imagePath}");
            }

            $request = Http::timeout($this->timeout)
                ->attach('file', file_get_contents($imagePath), basename($imagePath));
            if ($sensorData) {
                if (isset($sensorData['suhu'])) {
                    $request = $request->attach('suhu', (string)$sensorData['suhu']);
                }
                if (isset($sensorData['kelembapan'])) {
                    $request = $request->attach('kelembapan', (string)$sensorData['kelembapan']);
                }
                if (isset($sensorData['cahaya'])) {
                    $request = $request->attach('cahaya', (string)$sensorData['cahaya']);
                }
            }

            $response = $request->post("{$this->apiUrl}/detect");

            if ($response->successful()) {
                return $response->json();
            }

            $errorMessage = $response->json()['detail'] ?? $response->body();
            throw new Exception("Detection API error: {$errorMessage}");
        } catch (Exception $e) {
            Log::error('Detection Service Error', [
                'message' => $e->getMessage(),
                'image_path' => $imagePath,
                'api_url' => $this->apiUrl
            ]);
            throw $e;
        }
    }

    /**
     * @param int 
     * @return array
     */
    public function getRecentDetections(int $limit = 100): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->apiUrl}/detections", [
                    'limit' => $limit
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['detections' => [], 'count' => 0];
        } catch (Exception $e) {
            Log::error('Get Detections Error', [
                'message' => $e->getMessage(),
                'api_url' => $this->apiUrl
            ]);
            return ['detections' => [], 'count' => 0];
        }
    }

    /**
     * @param string 
     * @return array 
     * @throws Exception
     */
    public function detectRealtime(string $imagePath): array
    {
        try {
            if (!file_exists($imagePath)) {
                throw new Exception("File gambar tidak ditemukan: {$imagePath}");
            }

            $response = Http::timeout(5) 
                ->attach('file', file_get_contents($imagePath), basename($imagePath))
                ->post("{$this->apiUrl}/detect/realtime");

            if ($response->successful()) {
                return $response->json();
            }

            return ['detections' => [], 'timestamp' => now()->toIso8601String()];
        } catch (Exception $e) {
            Log::error('Realtime Detection Error', [
                'message' => $e->getMessage(),
                'image_path' => $imagePath
            ]);
            return ['detections' => [], 'timestamp' => now()->toIso8601String()];
        }
    }

    /**
     * @return array
     */
    public function healthCheck(): array
    {
        try {
            $response = Http::timeout(5)->get("{$this->apiUrl}/health");
            
            if ($response->successful()) {
                return $response->json();
            }

            return ['status' => 'unhealthy', 'models' => []];
        } catch (Exception $e) {
            return ['status' => 'unreachable', 'error' => $e->getMessage()];
        }
    }
}

