<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Companies;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'identify',
        'person_company',
        'user_company',
    ];

    public function company() {
        return $this->belongsTo(Companies::class,'company_id');
    }

    public function carts () {
        return $this->hasMany(Cart::class);
    }
}
