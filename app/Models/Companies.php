<?php

namespace App\Models;

use App\Models\CompanieCostumer;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
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
        'description',
        'password',
        'quartier',
        'status',
        'img'
    ];

    public function client () {
        return $this->hasMany(CompanieCostumer::class);
    }
}