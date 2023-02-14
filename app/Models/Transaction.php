<?php

namespace App\Models;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
  protected $fillable =[
    'title',
    'amount',
    'status',
    'cart_id',
    'costumer_id',
    'created',
    'company_id',
    'cartcode',
    'code_tansaction',
  ];
  public function cart() {
      return $this->belongsTo(Cart::class,'cart_id');
  }

}