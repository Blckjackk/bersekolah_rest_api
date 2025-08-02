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
        $filename = $this->photo;
        if ($filename) {
            $filename = basename($filename);
            return asset('storage/admin/mentor/' . $filename);
        }
        return asset('storage/defaults/mentor-default.jpg');
    }
}
