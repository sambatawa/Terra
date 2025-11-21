<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Relasi ke User (Siapa yang lapor)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Chat/Pesan (INI YANG TADI HILANG) -> Wajib Ada!
    public function messages()
    {
        return $this->hasMany(ReportMessage::class);
    }
}