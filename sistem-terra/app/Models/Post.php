<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Kategori diskusi (sama seperti marketplace)
    public static $categories = [
        'pupuk_nutrisi' => [
            'name' => 'Pupuk dan Nutrisi Tanaman',
            'icon' => 'fa-solid fa-flask',
            'color' => '#10b981',
            'description' => 'Diskusi tentang pupuk organik, kimia, dan nutrisi tanaman'
        ],
        'pestisida_obat' => [
            'name' => 'Pestisida dan Obat Tanaman',
            'icon' => 'fa-solid fa-shield-virus',
            'color' => '#ef4444', 
            'description' => 'Pembahasan tentang insektisida, fungisida, herbisida'
        ],
        'benih_bibit' => [
            'name' => 'Benih dan Bibit Unggul',
            'icon' => 'fa-solid fa-seedling',
            'color' => '#f59e0b',
            'description' => 'Berbagi pengalaman tentang benih sayuran, buah, padi, dan palawija'
        ],
        'alat_tani' => [
            'name' => 'Alat Pertanian',
            'icon' => 'fa-solid fa-tools',
            'color' => '#3b82f6',
            'description' => 'Diskusi alat tanam, sprayer, alat panen, dan peralatan lainnya'
        ],
        'sarana_produksi' => [
            'name' => 'Sarana Produksi',
            'icon' => 'fa-solid fa-warehouse',
            'color' => '#8b5cf6',
            'description' => 'Informasi mulsa, polybag, media tanam, dan perlengkapan lainnya'
        ],
        'umum' => [
            'name' => 'Diskusi Umum',
            'icon' => 'fa-solid fa-comments',
            'color' => '#6b7280',
            'description' => 'Diskusi pertanian umum dan topik lainnya'
        ]
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function likes() { return $this->hasMany(Like::class); }

    // Cek apakah user yang sedang login sudah like post ini
    public function isLikedByAuthUser() {
        return $this->likes->where('user_id', auth()->id())->isNotEmpty();
    }
    
    public function getCategoryName(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['name'] ?? 'Diskusi Umum';
    }
    
    public function getCategoryIcon(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['icon'] ?? 'fa-solid fa-comments';
    }
    
    public function getCategoryColor(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['color'] ?? '#6b7280';
    }
    
    public function getCategoryDescription(): string
    {
        $category = self::$categories[$this->category] ?? null;
        return $category['description'] ?? 'Diskusi pertanian umum';
    }
}
