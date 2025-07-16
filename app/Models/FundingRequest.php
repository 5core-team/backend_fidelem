<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundingRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'companyName',
        'email',
        'phone',
        'mission',
        'vision',
        'sector',
        'productDescription',
        'productStatus',
        'amountRequested',
        'useOfFunds',
        'businessPlan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amountRequested' => 'decimal:2',
    ];
}
