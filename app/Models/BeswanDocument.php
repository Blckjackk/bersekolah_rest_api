<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BeswanDocument extends Model
{
    use HasFactory;

    protected $table = 'beswan_documents';

    protected $fillable = [
        'beswan_id',
        'document_type_id', 
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'status',
        'keterangan',
        'verified_at',
        'verified_by',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // PERBAIKAN: beswan_id merujuk ke tabel beswans, bukan users
    public function beswan()
    {
        return $this->belongsTo(Beswan::class, 'beswan_id');
    }

    // TAMBAHAN: Relasi langsung ke user melalui beswan
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Beswan::class,
            'id', // Foreign key di tabel beswans
            'id', // Foreign key di tabel users  
            'beswan_id', // Local key di tabel beswan_documents
            'user_id' // Local key di tabel beswans
        );
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
