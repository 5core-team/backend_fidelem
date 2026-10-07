<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidatureConseiller extends Model
{
    protected $table = 'candidatures_conseiller';

    protected $fillable = [
        'user_id', 'prenom', 'nom', 'telephone', 'email',
        'niveau_vise', 'situation', 'experience', 'rendez_vous',
    ];

    protected $casts = [
        'rendez_vous' => 'array',
    ];

    /** Compte conseiller créé avec la candidature. */
    public function compte(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
