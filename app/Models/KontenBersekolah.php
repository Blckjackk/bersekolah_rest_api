<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KontenBersekolah extends Model
{
    use HasFactory;

    protected $table = 'konten_bersekolah';
    
    protected $fillable = [
        'judul_halaman',
        'slug',
        'deskripsi',
        'category',
        'gambar',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessor untuk gambar artikel agar bisa menampilkan URL lengkap
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return asset('storage/artikel/' . $this->gambar);
        }
        return asset('storage/artikel/default.jpg');
    }
}
