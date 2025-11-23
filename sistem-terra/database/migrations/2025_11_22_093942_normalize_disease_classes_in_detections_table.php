<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Constants\DiseaseClasses;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Normalize disease class names untuk konsistensi dengan backendapi
     */
    public function up(): void
    {
        // Mapping untuk normalize class names lama ke format baru
        $classMapping = [
            'Aphids' => 'aphids',
            'aphids' => 'aphids',
            'Cercospora' => 'bercak_cercospora',
            'cercospora' => 'bercak_cercospora',
            'bercak_cercospora' => 'bercak_cercospora',
            'Leaf Wilt' => 'layu_fusarium',
            'leaf wilt' => 'layu_fusarium',
            'layu_fusarium' => 'layu_fusarium',
            'Phytophthora Blight' => 'phytophthora_blight',
            'phytophthora blight' => 'phytophthora_blight',
            'phytophthora_blight' => 'phytophthora_blight',
            'Powdery Mildew' => 'powdery_mildew',
            'powdery mildew' => 'powdery_mildew',
            'powdery_mildew' => 'powdery_mildew',
            'Sehat' => 'sehat',
            'sehat' => 'sehat',
            'TMV' => 'mosaic_virus',
            'tmv' => 'mosaic_virus',
            'mosaic_virus' => 'mosaic_virus',
            'mosaic virus' => 'mosaic_virus',
        ];

        // Update dominan_disease dan label
        DB::table('detections')->chunkById(100, function ($detections) use ($classMapping) {
            foreach ($detections as $detection) {
                $updates = [];
                
                // Normalize dominan_disease
                if ($detection->dominan_disease) {
                    $normalized = DiseaseClasses::normalize($detection->dominan_disease);
                    if ($normalized !== $detection->dominan_disease) {
                        $updates['dominan_disease'] = $normalized;
                    }
                }
                
                // Normalize label
                if ($detection->label) {
                    $normalized = DiseaseClasses::normalize($detection->label);
                    if ($normalized !== $detection->label) {
                        $updates['label'] = $normalized;
                    }
                }
                
                // Normalize jumlah_disease_terdeteksi - pastikan semua class ada
                if ($detection->jumlah_disease_terdeteksi) {
                    $counts = is_string($detection->jumlah_disease_terdeteksi) 
                        ? json_decode($detection->jumlah_disease_terdeteksi, true) 
                        : $detection->jumlah_disease_terdeteksi;
                    
                    if (is_array($counts)) {
                        // Normalize keys dalam counts
                        $normalizedCounts = [];
                        foreach ($counts as $key => $value) {
                            $normalizedKey = DiseaseClasses::normalize($key);
                            $normalizedCounts[$normalizedKey] = $value;
                        }
                        
                        // Pastikan semua class ada
                        $completeCounts = array_merge(DiseaseClasses::DEFAULT_DETECTION_COUNTS, $normalizedCounts);
                        
                        // Hanya update jika ada perubahan
                        if (json_encode($completeCounts) !== json_encode($counts)) {
                            $updates['jumlah_disease_terdeteksi'] = json_encode($completeCounts);
                        }
                    }
                }
                
                // Update jika ada perubahan
                if (!empty($updates)) {
                    DB::table('detections')
                        ->where('id', $detection->id)
                        ->update($updates);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     * Tidak bisa di-reverse karena data sudah dinormalisasi
     */
    public function down(): void
    {
        // Tidak ada reverse karena ini adalah data normalization
    }
};
