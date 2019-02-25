<?php

namespace App;

use Validator;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'email', 
        'document',
        'cell_phone',
    ];

    public static function validateClient($request)
    {

        $transformed = [];  
        //Validacion de  campos requeridos
        $validator = Validator::make($request, [
            'document' => 'required|unique:clients|max:255',
            'email' => 'required|unique:clients|max:255',
            'cell_phone' => 'required|unique:clients|max:255',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();               
            foreach ($errors->messages() as $field => $message) {
                
                $transformed[] = [
                    'field' => $field,
                    'message' => $message[0]
                ];
            }
        }
        return $transformed;
    }

}
