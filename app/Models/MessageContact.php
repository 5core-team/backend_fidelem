<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageContact extends Model
{
    protected $table = 'messages_contact';

    protected $fillable = ['prenom', 'nom', 'telephone', 'email', 'objet', 'message', 'rendez_vous'];

    protected $casts = [
        'rendez_vous' => 'array',
    ];
}
