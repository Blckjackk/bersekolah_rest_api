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
        if ($this->foto_testimoni) {
            return asset('storage/' . $this->foto_testimoni);
        }
        return null;
    }
}
