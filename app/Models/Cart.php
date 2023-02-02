<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable=[
        'code',
        'created',
        'amount',
        'qrcode',
        'company_id',
        'client_id',
        'status',
    ];
}