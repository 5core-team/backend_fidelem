<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteDemande extends Model
{
    protected $table = 'notes_demande';

    protected $fillable = ['demande_financement_id', 'user_id', 'texte'];

    public function demande(): BelongsTo
    {
        return $this->belongsTo(DemandeFinancement::class, 'demande_financement_id');
    }

    public function auteur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
