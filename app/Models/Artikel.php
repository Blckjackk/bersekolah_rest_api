<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    // Set the table name to match migration
    protected $table = 'konten_bersekolah';

    protected $fillable = [
        'judul_halaman',
        'slug',
        'deskripsi',
        'category',
        'status',
        'gambar',
        'user_id'
    ];

    protected $appends = ['gambar_url'];

    /**
     * Get the image URL accessor
     */
    public function getGambarUrlAttribute()
    {
        // If no image is set, return default
        if (!$this->gambar || empty($this->gambar)) {
            return '/assets/image/artikel/default.jpg';
        }

        // Extract filename
        $filename = $this->gambar;
        
        // Check if the actual file exists in the bersekolah_website artikel directory
        $fullPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\artikel\\' . $filename;
        if (!file_exists($fullPath)) {
            // If file doesn't exist, return default
            return '/assets/image/artikel/default.jpg';
        }

        // Return the correct path with /assets prefix
        return '/assets/image/artikel/' . $filename;
    }

    /**
     * Override toArray to include gambar_url in JSON responses
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['gambar_url'] = $this->gambar_url;
        return $array;
    }
}
