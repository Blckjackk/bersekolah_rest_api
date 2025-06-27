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

    protected $appends = ['photo_url'];

    /**
     * Get the photo URL accessor
     */
    public function getPhotoUrlAttribute()
    {
        // If no photo is set, return default
        if (!$this->photo || empty($this->photo)) {
            return '/assets/image/mentor/default.jpg';
        }

        // Extract filename (remove mentor/ prefix if exists)
        $filename = str_replace('mentor/', '', $this->photo);
        
        // Check if the actual file exists in the bersekolah_website mentor directory
        $fullPath = 'C:\Users\mp2k5\Documents\GitHub\Project_Prokon\bersekolah_website\public\assets\image\mentor\\' . $filename;
        if (!file_exists($fullPath)) {
            // If file doesn't exist, return default
            return '/assets/image/mentor/default.jpg';
        }

        // Return the correct path with /assets prefix
        return '/assets/image/mentor/' . $filename;
    }

    /**
     * Override toArray to include photo_url in JSON responses
     */
    public function toArray()
    {
        $array = parent::toArray();
        $array['photo_url'] = $this->photo_url;
        return $array;
    }
}
