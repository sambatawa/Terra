<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Detection extends Model
{
    protected $guarded = [];
    protected $casts = [
        'jumlah_disease_terdeteksi' => 'array',
        'sensor_rata_rata' => 'array',
        'info' => 'array',
        'dominan_confidence_avg' => 'decimal:4',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
