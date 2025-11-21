<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
    protected $guarded = [];

    public function user() { return $this->belongsTo(User::class); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function likes() { return $this->hasMany(Like::class); }

    // Cek apakah user yang sedang login sudah like post ini
    public function isLikedByAuthUser() {
        return $this->likes->where('user_id', auth()->id())->isNotEmpty();
    }
}
