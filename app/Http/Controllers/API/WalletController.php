<?php

namespace App\Http\Controllers\API;

use App\Client;
use App\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Cargar dinero en la billetera.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function charge(Request $request)
    {
        //Estado inicial de variables de respuesta
        $success = true;
        $cod_error = 00;
        $message_error = "";
        $data = null;
        $status_code = 200;

        try {
            //1. Consultar cliente que va a realizar la recarga
            $client_bill = Client::where([
                "document" => $request->document,
                'cell_phone' => $request->cell_phone

            ])->first();

            //1.1 Validar si son validos los parametros de consulta del cliente
            if (!empty($client_bill)) {
                //2. Identificar billetera (en este caso solo una) y realizar el abono al saldo
                $wallet = $client_bill->wallets->first();
                $wallet->balance = ($request->value + $wallet->balance);
                $wallet->save();
                $data = "Su recarga se ha realizado correctamente";
            }else{
                $success = false;
                $cod_error = 04;
                $message_error = "error: Esta billetera actualmente no existe";
                $status_code = 400;
            }
        } catch (\Throwable $th) {
            $success = false;
            $cod_error = 03;
            $message_error = "error: Lo sentimos, no se realizo la carga en su billetera \n ". $th->getMessage();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
