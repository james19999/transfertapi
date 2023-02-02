<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class CompanieCostumer extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guard = "companiecostumer";
    protected $fillable=[
        'name',
        'ville',
        'phone',
        'adress',
        'email',
        'quartier',
        'company_id',
        'identify'
    ];
}