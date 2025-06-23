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
}
