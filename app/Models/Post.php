<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Post extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'title', 'content', 'image'];


    protected static function booted()
    {
        static::creating(function ($post) {
            // تعيين user_id تلقائيًا للمستخدم المسجل دخوله
            if (!$post->user_id) {
                $post->user_id = auth()->id();
            }
        });
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
