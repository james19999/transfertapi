<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Companies extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    protected $guard = "companie";

    protected $fillable =[

        'name',
        'phone',
        'adress',
        'email',
        'raison',
        'domaine',
        'password',
        'quartier',
        'status',
    ];
}