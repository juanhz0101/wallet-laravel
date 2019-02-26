<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'amount_payment',
        'token_payment',
        'status',
        'wallet_id'        
    ];

     /**
     * Regresar la billetera que hizo el pago.
     */
    public function wallet()
    {
        return $this->belongsTo('App\Wallet');
    }
}
