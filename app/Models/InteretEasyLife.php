<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteretEasyLife extends Model
{
    protected $table = 'interets_easylife';

    protected $fillable = ['prenom', 'nom', 'telephone', 'email', 'profil', 'pole', 'message', 'rendez_vous'];

    protected $casts = [
        'rendez_vous' => 'array',
    ];
}
