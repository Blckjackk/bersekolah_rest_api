<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScholarshipAcceptance extends Model
{
    use HasFactory;

    protected $table = 'scholarship_acceptance';

    protected $fillable = [
        'user_id',
        'has_accepted_scholarship',
        'has_joined_whatsapp_group',
        'accepted_at',
        'joined_group_at',
    ];

    protected $casts = [
        'has_accepted_scholarship' => 'boolean',
        'has_joined_whatsapp_group' => 'boolean',
        'accepted_at' => 'datetime',
        'joined_group_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
