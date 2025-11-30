<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;
    
    protected $guarded = []; // Biar bisa simpan semua kolom
    
    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Kategori produk (static mapping)
    public static $categories = [
        'pupuk_nutrisi' => [
            'name' => 'Pupuk & Nutrisi Tanaman',
            'icon' => 'fa-solid fa-flask',
            'color' => '#10b981',
            'description' => 'Pupuk organik, kimia, dan nutrisi tanaman'
        ],
        'pestisida_obat' => [
            'name' => 'Pestisida & Obat Tanaman',
            'icon' => 'fa-solid fa-shield-virus',
            'color' => '#ef4444', 
            'description' => 'Insektisida, fungisida, herbisida'
        ],
        'benih_bibit' => [
            'name' => 'Benih & Bibit Unggul',
            'icon' => 'fa-solid fa-seedling',
            'color' => '#f59e0b',
            'description' => 'Benih sayuran, buah, padi, dan palawija'
        ],
        'alat_tani' => [
            'name' => 'Alat Pertanian',
            'icon' => 'fa-solid fa-tools',
            'color' => '#3b82f6',
            'description' => 'Alat tanam, sprayer, alat panen, dan peralatan lainnya'
        ],
        'sarana_produksi' => [
            'name' => 'Sarana Produksi',
            'icon' => 'fa-solid fa-warehouse',
            'color' => '#8b5cf6',
            'description' => 'Mulsa, polybag, media tanam, dan perlengkapan lainnya'
        ]
    ];
    
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function getCategoryName(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['name'] ?? 'Tidak Dikategorikan';
    }
    
    public function getCategoryIcon(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['icon'] ?? 'fa-solid fa-box';
    }
    
    public function getCategoryColor(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['color'] ?? '#6b7280';
    }
    
    public function getCategoryDescription(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['description'] ?? 'Tidak ada deskripsi kategori';
    }
    
    public function getFormattedPrice(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
    
    public function getStockStatus(): string
    {
        if ($this->stock > 10) {
            return 'Tersedia';
        } elseif ($this->stock > 0) {
            return 'Terbatas (' . $this->stock . ')';
        } else {
            return 'Habis';
        }
    }
    
    public function getStockStatusColor(): string
    {
        if ($this->stock > 10) {
            return '#10b981'; // green
        } elseif ($this->stock > 0) {
            return '#f59e0b'; // yellow
        } else {
            return '#ef4444'; // red
        }
    }
}
