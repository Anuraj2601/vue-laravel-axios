<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id',
        'social_media_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function socialMedia() {
        return $this->belongsTo(SocialMedia::class,'social_media_id');
    }

    public function tags() {
        return $this->belongsToMany(Tag::class, 'post_tag');
    }

    
}
