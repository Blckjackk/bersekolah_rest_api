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
        if (!$this->photo) {
            return asset('storage/defaults/mentor-default.jpg');
        }
        
        $filename = $this->photo;
        
        // Jika sudah full path, gunakan langsung
        if (str_starts_with($filename, 'http')) {
            return $filename;
        }
        
        // Jika sudah ada /storage/ di awal, buat full URL
        if (str_starts_with($filename, '/storage/')) {
            return url($filename);
        }
        
        // Extract filename saja jika ada path
        if (str_contains($filename, '/')) {
            $filename = basename($filename);
        }
        
        // Return full Laravel storage URL untuk admin/mentor folder
        return asset('storage/admin/mentor/' . $filename);
    }
}
