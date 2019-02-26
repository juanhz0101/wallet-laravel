<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
     /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id',
        'balance',
    ];

    /**
     * Regresar el cliente dueño de una billetera.
     */
    public function client()
    {
        return $this->belongsTo('App\Client');
    }

    /**
     * Regresar los pagos de un cliente.
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }
}
