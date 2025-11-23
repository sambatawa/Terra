<?php

namespace App\Constants;

/**
 * Class names mapping untuk penyakit tanaman
 * Harus sama dengan backendapi/main.py CLASS_NAMES_MAPPED
 */
class DiseaseClasses
{
    /**
     * Mapping dari class original ke class name yang digunakan di database
     */
    public const CLASS_NAMES_MAPPED = [
        "Aphids" => "aphids",
        "Cercospora" => "bercak_cercospora",
        "Leaf Wilt" => "layu_fusarium",
        "Phytophthora Blight" => "phytophthora_blight",
        "Powdery Mildew" => "powdery_mildew",
        "Sehat" => "sehat",
        "TMV" => "mosaic_virus"
    ];

    /**
     * Semua class names yang valid (dalam format database)
     */
    public const VALID_CLASSES = [
        "aphids",
        "bercak_cercospora",
        "layu_fusarium",
        "phytophthora_blight",
        "powdery_mildew",
        "sehat",
        "mosaic_virus"
    ];

    /**
     * Default detection counts structure (sesuai backendapi)
     */
    public const DEFAULT_DETECTION_COUNTS = [
        "layu_fusarium" => 0,
        "bercak_cercospora" => 0,
        "mosaic_virus" => 0,
        "aphids" => 0,
        "phytophthora_blight" => 0,
        "powdery_mildew" => 0,
        "sehat" => 0
    ];

    /**
     * Cek apakah class name valid
     */
    public static function isValid(string $className): bool
    {
        return in_array($className, self::VALID_CLASSES);
    }

    /**
     * Normalize class name (convert ke format database)
     */
    public static function normalize(string $className): string
    {
        // Jika sudah dalam format yang benar, return as is
        if (self::isValid($className)) {
            return $className;
        }

        // Coba mapping dari original
        $lower = strtolower($className);
        foreach (self::CLASS_NAMES_MAPPED as $original => $mapped) {
            if (strtolower($original) === $lower) {
                return $mapped;
            }
        }

        // Jika tidak ditemukan, return as is (untuk backward compatibility)
        return $className;
    }

    /**
     * Get display name untuk class
     */
    public static function getDisplayName(string $className): string
    {
        $displayNames = [
            "aphids" => "Aphids (Kutu Daun)",
            "bercak_cercospora" => "Bercak Cercospora",
            "layu_fusarium" => "Layu Fusarium",
            "phytophthora_blight" => "Phytophthora Blight",
            "powdery_mildew" => "Powdery Mildew",
            "sehat" => "Sehat",
            "mosaic_virus" => "Mosaic Virus"
        ];

        return $displayNames[$className] ?? $className;
    }
}

