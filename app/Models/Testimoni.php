<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    protected $table = 'testimoni';

    public $timestamps = false; // karena tidak pakai created_at & updated_at default

    protected $fillable = [
        'nama',
        'angkatan_beswan',
        'sekarang_dimana',
        'isi_testimoni',
        'foto_testimoni',
        'status',
        'tanggal_input',
    ];

    protected $casts = [
        'tanggal_input' => 'datetime',
    ];

    // Accessor untuk foto testimoni agar bisa menampilkan URL lengkap
    public function getFotoTestimoniUrlAttribute()
    {
        if (!$this->foto_testimoni) {
            return asset('storage/defaults/testimoni-default.jpg');
        }
        
        $filename = $this->foto_testimoni;
        
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
        
        // Return full Laravel storage URL untuk admin/testimoni folder
        return asset('storage/admin/testimoni/' . $filename);
    }
}
