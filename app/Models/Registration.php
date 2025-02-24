<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    
    use HasFactory;
        protected $table = 'registrations'; // تحديد اسم الجدول في قاعدة البيانات

    protected $fillable = ['user_id', 'activity_id', 'registration_date', 'status'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }
}
