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
        $this->apiUrl = config('services.detection_api.url', 'http://localhost:8001');
        $this->timeout = config('services.detection_api.timeout', 30);
    }

    /**
     * Deteksi penyakit dari gambar
     * 
     * @param string $imagePath Path lengkap ke file gambar
     * @param array|null $sensorData Data sensor (suhu, kelembapan, cahaya)
     * @return array Hasil deteksi dari API
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

            // Tambahkan data sensor jika ada (sesuai JSON structure)
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
     * Ambil riwayat deteksi dari API
     * 
     * @param int $limit Jumlah data yang diambil
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
     * Real-time detection untuk video frame - return bounding boxes saja
     * 
     * @param string $imagePath Path lengkap ke file gambar (frame)
     * @return array Detections dengan bounding boxes
     * @throws Exception
     */
    public function detectRealtime(string $imagePath): array
    {
        try {
            if (!file_exists($imagePath)) {
                throw new Exception("File gambar tidak ditemukan: {$imagePath}");
            }

            $response = Http::timeout(5) // Timeout pendek untuk real-time
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
     * Cek status API
     * 
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

