<?php

namespace App\Http\Controllers\API;

use App\Client;
use App\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    /**
     * Creación de Cliente.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Estado inicial de variables de respuesta
        $success = true;
        $cod_error = 00;
        $message_error = "";
        $data = null;
        $status_code = 200;
        
        try {            
            //1. Validar si los datos ingresados para el cliente son correctos
            $validations = Client::validateClient($request->all());            
            if (count($validations)>0) {
                //1.1 No pasa la validacion
                $success = false;
                $cod_error = 200;
                $message_error = "error: Algunos datos no son validos para crear el cliente; field: ". $validations[0]["field"]." ; message: ".$validations[0]["message"];
                $status_code = 400; 
            }else{
                //1.2 Creando cliente
                $client = Client::create([
                    'name' => $request->name, 
                    'email' => $request->email, 
                    'document' => $request->document,
                    'cell_phone' => $request->cell_phone,
                ]);
                //1.3 Creando billetera del cliente
                Wallet::create(['client_id' => $client->id]);
                $data = "cliente y billetera generados correctamente";
            }
        } catch (\Throwable $th) {
            $success = false;
            $cod_error = 100;
            $message_error = "error: El cliente no fue registrado \n ". $th->getMessage();
            $status_code = 500;
        }

         return response()->json([
            'success' => $success,
            'cod_error' => $cod_error,
            'message_error' => $message_error,
            'data' => $data
        ],$status_code);
    }

    /**
     * Consultar saldo en la billetera.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function balance(Request $request)
    {
        //Estado inicial de variables de respuesta
        $success = true;
        $cod_error = 00;
        $message_error = "";
        $data = null;
        $status_code = 200;

        try {
            //1. Consultar cliente que va a realizar la consulta
            $client_bill = Client::where([
                "document" => $request->document,
                'cell_phone' => $request->cell_phone

            ])->first();
            
            //1.1 Validar si son validos los parametros de consulta del saldo del cliente
            if (!empty($client_bill)) {
                //2. Identificar billetera (en este caso solo una) y realizar la consulta      
                $wallet = $client_bill->wallets->first();
                $data = $wallet;

            }else{
                $success = false;
                $cod_error = 150;
                $message_error = "error: Esta billetera actualmente no existe";
                $status_code = 400;
            }
        } catch (\Throwable $th) {
            $success = false;
            $cod_error = 140;
            $message_error = "error: Lo sentimos, no pudimos consultar su saldo \n ". $th->getMessage();
            $status_code = 500;
        }
        return response()->json([
            'success' => $success,
            'cod_error' => $cod_error,
            'message_error' => $message_error,
            'data' => $data
        ],$status_code);       
    }

}
