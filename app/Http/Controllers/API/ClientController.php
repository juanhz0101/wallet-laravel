<?php

namespace App\Http\Controllers\API;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA'
        ]);
    }

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
        $status_code = 200;
        try {
            
            //1. Validar si los datos ingresados para el cliente son correctos
            $validations = Client::validateClient($request->all());            
            if (count($validations)>0) {
                //1.1 No pasa la validacion
                $success = false;
                $cod_error = 02;//Error: El usuario no fue registrado
                $message_error = "Error: Algunos datos no son validos para crear el cliente; field: ". $validations[0]["field"]." ; message: ".$validations[0]["message"];
                $status_code = 400; 
            }else{
               //1.2 Creando cliente
               Client::create([
                    'name' => $request->name, 
                    'email' => $request->email, 
                    'document' => $request->document,
                    'cell_phone' => $request->cell_phone,
                ]);
            }
        } catch (\Throwable $th) {
            $success = false;
            $cod_error = 01;//Error: El usuario no fue registrado
            $message_error = "Error: El cliente no fue registrado \n ". $th->getMessage();
            $status_code = 500;
        }

         return response()->json([
            'success' => $success,
            'cod_error' => $cod_error,
            'message_error' => $message_error,
            'data' => ''
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
