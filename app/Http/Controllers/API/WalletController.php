<?php

namespace App\Http\Controllers\API;

use Mail;
use App\Client;
use App\Wallet;
use App\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    /**
     * Cargar dinero en la billetera.
     *
     * @param  \Illuminate\Http\Request  $request
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
     * Solicitar pago.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function payRequest(Request $request)
    {
        //Estado inicial de variables de respuesta
        $success = true;
        $cod_error = 00;
        $message_error = "";
        $data = null;
        $status_code = 200;
 
        try 
        {
            //1. Consultar cliente que va a realizar la recarga
            $client_bill = Client::where([
                "document" => $request->document,
                "cell_phone" => $request->cell_phone
            ])->first();
            
            
            //1.1 Validar si son validos los parametros de consulta del cliente
            if (!empty($client_bill)) {
                //2. Identificar billetera (en este caso solo una) y realizar la solicitud de pago
                $wallet = $client_bill->wallets->first();
                //3. Pre validacion de saldo disponible para realizar el pago
                if ($wallet->balance > $request->to_pay) {
                    
                    //4. Creacion de solicitud de pago
                    $request_payment = Payment::create([
                        "amount_payment" => $request->to_pay,
                        "token_payment" => rand(000000, 999999),
                        "wallet_id" => $wallet->id,                       
                        "status" => 0
                    ]);
                    
                    //5. Consulta de solicitud creada para complementar envio de email
                    $created_payment = Payment::findOrFail($request_payment->id);
                
                    $email_data = 
                    [ 
                        'session_payment'    => $created_payment->id,
                        'token_payment'   => $created_payment->token_payment,
                        'amount_payment'   => $request->to_pay,
                        'client_email' => $client_bill->email
                    ];

                    //6. Envio de sesion y token al correo del cliente
                    Mail::send('notifications.payrequest',$email_data,function($message) use ($email_data){
                        $message->from('wallet-noreply@walletservice.com');
                        $message->to($email_data["client_email"])
                            ->subject('Servicio de billetera - Solicitud de pago');
                    });

                }else{
                    $success = false;
                    $cod_error = 05;
                    $message_error = "error: Esta billetera tiene fondos insuficientes - pre-validación";
                    $status_code = 400;
                }

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
    
}
