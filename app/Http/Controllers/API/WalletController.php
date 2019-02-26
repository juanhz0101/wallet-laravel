<?php

namespace App\Http\Controllers\API;

use Mail;
use App\Client;
use App\Wallet;
use App\Payment;
use Carbon\Carbon;
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
                $cod_error = 400;
                $message_error = "error: Recarga no procesada, esta billetera actualmente no existe";
                $status_code = 400;
            }
        } catch (\Throwable $th) {
            $success = false;
            $cod_error = 300;
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

                    $data = "Solicitud de verificación de pago creada correctamente";

                }else{
                    $success = false;
                    $cod_error = 700;
                    $message_error = "error: Esta billetera tiene fondos insuficientes - pre-validación";
                    $status_code = 400;
                }

            }else{
                $success = false;
                $cod_error = 600;
                $message_error = "error: Solicitud de pago no procesada, esta billetera actualmente no existe";
                $status_code = 400;
            }
        } catch (\Throwable $th) {
            $success = false;
            $cod_error = 500;
            $message_error = "error: Lo sentimos, no se pudo crear la solicitud de pago a su billetera \n ". $th->getMessage();
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
     * Confirmar pago.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function payCheck(Request $request)
    {
        //Estado inicial de variables de respuesta
        $success = true;
        $cod_error = 00;
        $message_error = "";
        $data = null;
        $status_code = 200;
 
        try 
        {
            //1. Consultar pago por confirmar
            $payment = Payment::where([
                "id" => $request->session_payment,
                "token_payment" => $request->token_payment
            ])->first();

            //1.1 Validar si son validos los parametros de consulta del pago
            if (!empty($payment)) {

                //3. Verificar estado de solicitud en "pendiente" = 0
                if ($payment->status == 0) {

                    //4. Validar que el tiempo de expiracion del pago sea menor a 30 minutos               
                    //convertimos la fecha de creacion de solicitud a objeto Carbon
                    $created = new \Carbon\Carbon($payment->created_at);
                     //convertimos la fecha actual a objeto Carbon
                    $now = Carbon::now();
                    //de esta manera sacamos la diferencia en minutos
                    $minutesDiff = $created->diffInMinutes($now);

                    if ($minutesDiff <= 30) {

                        //5. Consultar la billetera que sera utilizada para descontar el dinero
                        $wallet = Wallet::findOrFail($payment->wallet_id);

                        //6. Post validacion de saldo disponible para realizar el pago
                        if ($wallet->balance > $payment->amount_payment) {       

                            //7. Descontar pago de la billetera y cambiar estado de solicitud
                            $real_balance = ($wallet->balance - $payment->amount_payment);                  $wallet->balance = $real_balance;
                            $wallet->save();

                            //Cambiar estado de pago a confirmado
                            $payment->status = 1;
                            $payment->save();

                            $data = "Solicitud de pago verificada correctamente";
                        }else{
                            $success = false;
                            $cod_error = 130;
                            $message_error = "error: Esta billetera tiene fondos insuficientes - post-validación";
                            $status_code = 400;
                        }
                    }else{
                        $success = false;
                        $cod_error = 120;
                        $message_error = "error: Esta solicitud de pago ya expiro, por favor genere una nueva";
                        $status_code = 400;
                    }
                }else if ($payment->status == 1){
                    $success = false;
                    $cod_error = 110;
                    $message_error = "error: Esta solicitud de pago ya fue confirmada";
                    $status_code = 400;
                }              

            }else{
                $success = false;
                $cod_error = 900;
                $message_error = "error: Este pago por confirmar no existe";
                $status_code = 400;
            }            
            
        } catch (\Throwable $th) {
            $success = false;
            $cod_error = 800;
            $message_error = "error: Lo sentimos, no se realizo la confirmación del pago \n ". $th->getMessage();
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
