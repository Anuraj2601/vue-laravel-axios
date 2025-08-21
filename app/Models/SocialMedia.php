<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    use HasFactory;

    protected $table = 'social_media';

    protected $fillable = [
        'platform',
        'url',
        'location',
        'date',
        'user_id',
    ];

    public function posts() {
       return $this->hasMany(Post::class, 'social_media_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
