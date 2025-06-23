<?php

// app/Models/CreditRequest.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'duration',
        'purpose',
        'additional_details',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
}

