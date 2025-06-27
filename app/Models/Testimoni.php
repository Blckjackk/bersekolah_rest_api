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

    // Accessor untuk foto testimoni URL
    public function getFotoTestimoniUrlAttribute()
    {
        if (!$this->foto_testimoni || $this->foto_testimoni === 'null' || $this->foto_testimoni === '') {
            return '/assets/image/testimoni/default.jpg';
        }
        
        // If it's already 'default.jpg', return the correct path
        if ($this->foto_testimoni === 'default.jpg') {
            return '/assets/image/testimoni/default.jpg';
        }
        
        // If the path already starts with /assets, return as is
        if (str_starts_with($this->foto_testimoni, '/assets')) {
            return $this->foto_testimoni;
        }
        
        // If the path already includes the domain, return as is
        if (str_starts_with($this->foto_testimoni, 'http')) {
            return $this->foto_testimoni;
        }
        
        // Extract filename if it contains path separators
        $filename = $this->foto_testimoni;
        if (str_contains($filename, '/') || str_contains($filename, '\\')) {
            $filename = basename($filename);
        }
        
        // Return local path for frontend assets
        return '/assets/image/testimoni/' . $filename;
    }
}
