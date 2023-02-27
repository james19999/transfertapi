<?php

namespace App\Models;

use App\Models\Companies;
use App\Models\Transaction;
use App\Models\CompanieCostumer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function company() {
        return $this->belongsTo(Companies::class, 'company_id');
    }
    public function client() {
        return $this->belongsTo(CompanieCostumer::class, 'client_id');
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}