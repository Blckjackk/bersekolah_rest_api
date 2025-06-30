<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'photo'
    ];

    // Accessor untuk foto mentor agar bisa menampilkan URL lengkap
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return asset('storage/mentor/default.jpg');
    }
}
