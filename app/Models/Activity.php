<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description','photo', 'start_date', 'end_date', 'location'];

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(Gallery::class);
    }
}
