<?php

namespace App\Http\Controllers;
use App\Models\BilleteraCliente;
use App\Models\Cliente;
use App\Models\MovimientoBilletera;
use App\Models\SaldoBilletera;
use App\Models\Sys_Secuencias;
use App\Models\Transaccion;
use App\Models\Billetera;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Retiro;

class RetiroController extends Controller{

    public function create(Request $request)
    {
        // Buscar la billetera vinculada a un cliente
        $billeteraExistente = BilleteraCliente::where("ID_BILLETERA", "="
            , $request->input("ID_BILLETERA"));

        // Buscar billetera
        $billetera = Billetera::where("ID_BILLETERA", "="
            , $request->input("ID_BILLETERA"));

        // Buscar cliente
        $cliente = Cliente::where("ID_CLIENTE", "=",
            $billeteraExistente->value("ID_CLIENTE"))->first();

        // Si el cliente no cuenta con billetera
        if ($cliente == null) {
            // la variable secuencial me sirve para octener la secuencia para el id de la transacción
            $secuencial = DB::selectOne("select (f_obtener_secuencias_rt('transacciones')) as secuencia");
            //  la variable $dateTime me ayuda a obtener el formato de fecha y hora para el id de la transaccón
            $dateTime = Carbon::now()->format('Ymd.Hi');

            // Insertar en la tabla Transacciones
            $transaccionFallida = new Transaccion();
            $transaccionFallida->ID_TRANSACCION =  'RT'.$dateTime.'.'.$secuencial->secuencia;
            $transaccionFallida->FECHA_TRANSACCION = Carbon::now();
            $transaccionFallida->TIPO_TRANSACCION = "RT";
            $transaccionFallida->ID_BILLETERA = $billetera->value("ID_BILLETERA");
            $transaccionFallida->ESTADO_TRANSACCION = "F";
            $transaccionFallida->ID_ERROR = 203;
            $transaccionFallida->USU_CRE = "Admin";
            $transaccionFallida->FEC_CRE = Carbon::now();
            $transaccionFallida->save();

            $secuencia = Sys_Secuencias::where("id", "=",2)->first();
            $secuencia -> SECUENCIA_ANTERIOR =  $secuencia -> SECUENCIA_ANTERIOR + 1;
            $secuencia -> SECUENCIA_SIGUIENTE =  $secuencia -> SECUENCIA_SIGUIENTE + 1;
            $secuencia -> save();

            return response()->json(["ID: " => $transaccionFallida->ID_TRANSACCION = 'RT'.$dateTime.'.'.$secuencial->secuencia,
                "FECHA" => $transaccionFallida->FECHA_TRANSACCION = Carbon::now(),
                "TIPO: " => $transaccionFallida->TIPO_TRANSACCION = "RT",
                "ESTADO: " => $transaccionFallida->ESTADO_TRANSACCION = "F" ,
                "MONTO: " => $request->input("MONTO_TRANSACCION"),
                "ID ERROR: " => $transaccionFallida->ID_ERROR = 203,
                "ERROR: " => "El cliente no tiene ninguna billetera asignada aún"], 200);
        }

        // Si el cliente esta Activo, insertara el retiro exitoso
        if ($cliente->ESTADO_CLIENTE == "A") {

            $saldoBilletCliente = SaldoBilletera::where("ID_BILLETERA",
                "=", $billeteraExistente->value("ID_BILLETERA"))
                ->value("SALDO_BILLETERA");

            // Si el monto es menor al saldo
            if ($saldoBilletCliente >= $request->MONTO_TRANSACCION) {
                // la variable secuencial me sirve para octener la secuencia para el id de la transacción
                $secuencial = DB::selectOne("select (f_obtener_secuencias_rt('transacciones')) as secuencia");
                //  la variable $dateTime me ayuda a obtener el formato de fecha y hora para el id de la transaccón
                $dateTime = Carbon::now()->format('Ymd.Hi');

                // Insertar en la tabla Transacciones
                $transaccionExitosa = new Transaccion();
                $transaccionExitosa->ID_TRANSACCION =  'RT'.$dateTime.'.'.$secuencial->secuencia;
                $transaccionExitosa->FECHA_TRANSACCION = Carbon::now();
                $transaccionExitosa->TIPO_TRANSACCION = "RT";
                $transaccionExitosa->ID_BILLETERA = $billeteraExistente->value("ID_BILLETERA");
                $transaccionExitosa->ESTADO_TRANSACCION = "E";
                $transaccionExitosa->USU_CRE = "Admin";
                $transaccionExitosa->FEC_CRE = Carbon::now();
                $transaccionExitosa->save();

                // Insertar en la tabla Movimientos Billeteras
                $movimiento = new MovimientoBilletera();
                $movimiento->FECHA_MOVIMIENTO = Carbon::now();
                $movimiento->ID_TRANSACCION =  'RT'.$dateTime.'.'.$secuencial->secuencia;
                $movimiento->MONTO_TRANSACCION = $request->MONTO_TRANSACCION;
                $movimiento->SALDO_ANTERIOR = $saldoBilletCliente;

                // Calcular el nuevo saldo en la billetera del cliente
                // Saldo Posterior = Saldo Anterior - Monto Transaccion
                    $saldoPosterior = $saldoBilletCliente - $request->MONTO_TRANSACCION;

                // El saldo posterior tomara el valor nuevo de $saldoPosterior
                $movimiento->SALDO_POSTERIOR = $saldoPosterior;
                $movimiento->USU_CRE = "Admin";
                $movimiento->FEC_CRE = Carbon::now();
                $movimiento->save();

                // -- Actualizar el saldo de la billetera
                $saldoBilltera = SaldoBilletera::where("ID_BILLETERA", $billeteraExistente->value("ID_BILLETERA"))->first();
                $saldoBilltera->SALDO_BILLETERA = $saldoPosterior;
                $saldoBilltera->save();

                $secuencia = Sys_Secuencias::where("id", "=",2)->first();
                $secuencia -> SECUENCIA_ANTERIOR =  $secuencia -> SECUENCIA_ANTERIOR + 1;
                $secuencia -> SECUENCIA_SIGUIENTE =  $secuencia -> SECUENCIA_SIGUIENTE + 1;
                $secuencia -> save();

                return response()->json(["EXITO" => "Transaccion realizada Exitosamente",
                    "ID: " => $transaccionExitosa->ID_TRANSACCION = 'RT'.$dateTime.'.'.$secuencial->secuencia,
                    "FECHA" => $transaccionExitosa->FECHA_TRANSACCION = Carbon::now(),
                    "MONTO" => $request->input("MONTO_TRANSACCION"),
                    "TIPO: " => $transaccionExitosa->TIPO_TRANSACCION = "RT",
                    "ESTADO: " => $transaccionExitosa->ESTADO_TRANSACCION = "E"], 200);
            }

            if ($saldoBilletCliente < $request->MONTO_TRANSACCION) {
                // Si el monto es mayor al saldo

                // la variable secuencial me sirve para octener la secuencia para el id de la transacción
                $secuencial = DB::selectOne("select (f_obtener_secuencias_rt('transacciones')) as secuencia");
                //  la variable $dateTime me ayuda a obtener el formato de fecha y hora para el id de la transaccón
                $dateTime = Carbon::now()->format('Ymd.Hi');

                // Insertar en la tabla Transacciones
                $transaccionFallida = new Transaccion();
                $transaccionFallida->ID_TRANSACCION =  'RT'.$dateTime.'.'.$secuencial->secuencia;
                $transaccionFallida->FECHA_TRANSACCION = Carbon::now();
                $transaccionFallida->TIPO_TRANSACCION = "RT";
                $transaccionFallida->ID_BILLETERA = $billeteraExistente->value("ID_BILLETERA");
                $transaccionFallida->ESTADO_TRANSACCION = "F";
                $transaccionFallida->ID_ERROR = 201;
                $transaccionFallida->USU_CRE = "Admin";
                $transaccionFallida->FEC_CRE = Carbon::now();
                $transaccionFallida->save();

                $secuencia = Sys_Secuencias::where("id", "=",2)->first();
                $secuencia -> SECUENCIA_ANTERIOR =  $secuencia -> SECUENCIA_ANTERIOR + 1;
                $secuencia -> SECUENCIA_SIGUIENTE =  $secuencia -> SECUENCIA_SIGUIENTE + 1;
                $secuencia -> save();

                return response()->json(["ID: " => $transaccionFallida->ID_TRANSACCION = 'RT'.$dateTime.'.'.$secuencial->secuencia,
                    "FECHA" => $transaccionFallida->FECHA_TRANSACCION = Carbon::now() ,
                    "TIPO: " => $transaccionFallida->TIPO_TRANSACCION = "RT",
                    "ESTADO: " => $transaccionFallida->ESTADO_TRANSACCION = "F" ,
                    "MONTO: " => $request->input("MONTO_TRANSACCION"),
                    "ID ERROR: " => $transaccionFallida->ID_ERROR = 201,
                    "ERROR: " => "El saldo es insuficiente para realizar la transaccion"], 200);
            }
        }

        // Si el cliente está inactivo
        if ($cliente->ESTADO_CLIENTE == "I") {
            // la variable secuencial me sirve para octener la secuencia para el id de la transacción
            $secuencial = DB::selectOne("select (f_obtener_secuencias_rt('transacciones')) as secuencia");
            //  la variable $dateTime me ayuda a obtener el formato de fecha y hora para el id de la transaccón
            $dateTime = Carbon::now()->format('Ymd.Hi');

            // Insertar en la tabla Transacciones
            $transaccionFallida = new Transaccion();
            $transaccionFallida->ID_TRANSACCION =  'RT'.$dateTime.'.'.$secuencial->secuencia;
            $transaccionFallida->FECHA_TRANSACCION = Carbon::now();
            $transaccionFallida->TIPO_TRANSACCION = "RT";
            $transaccionFallida->ID_BILLETERA = $billeteraExistente->value("ID_BILLETERA");
            $transaccionFallida->ESTADO_TRANSACCION = "F";
            $transaccionFallida->ID_ERROR = 202;
            $transaccionFallida->USU_CRE = "Admin";
            $transaccionFallida->FEC_CRE = Carbon::now();
            $transaccionFallida->save();

            $secuencia = Sys_Secuencias::where("id", "=",2)->first();
            $secuencia -> SECUENCIA_ANTERIOR =  $secuencia -> SECUENCIA_ANTERIOR + 1;
            $secuencia -> SECUENCIA_SIGUIENTE =  $secuencia -> SECUENCIA_SIGUIENTE + 1;
            $secuencia -> save();

            return response()->json(["ID: " => $transaccionFallida->ID_TRANSACCION = 'RT'.$dateTime.'.'.$secuencial->secuencia,
                "FECHA" => $transaccionFallida->FECHA_TRANSACCION = Carbon::now() ,
                "TIPO: " => $transaccionFallida->TIPO_TRANSACCION = "RT",
                "ESTADO: " => $transaccionFallida->ESTADO_TRANSACCION = "F" ,
                "MONTO: " => $request->input("MONTO_TRANSACCION"),
                "ID ERROR: " => $transaccionFallida->ID_ERROR = 202,
                "ERROR: " => "El cliente actualmente está inactivo"], 200);
        }
    }

    public function retiro_exitoso($id_billetera)
    {
        $transacciones = DB::table('transacciones')
            ->join('movimientos_billeteras', 'movimientos_billeteras.id_transaccion', '=', 'transacciones.id_transaccion')
            ->join('billeteras_clientes', 'billeteras_clientes.id_billetera', '=', 'transacciones.id_billetera')
            ->select('transacciones.id_transaccion', 'transacciones.fecha_transaccion', 'transacciones.tipo_transaccion', 'movimientos_billeteras.monto_transaccion', 'transacciones.estado_transaccion')->
            where('BILLETERAS_CLIENTES.id_billetera', '=', $id_billetera)->get();
        return response()->json(['transaccion' => $transacciones]);
    }

    public function retiro_fallido($id_billetera)
    {
        $transacciones = DB::table('transacciones')
            ->join('movimientos_billeteras', 'movimientos_billeteras.ID_TRANSACCION', '=', 'transacciones.ID_TRANSACCION')
            ->join('billeteras_clientes', 'billeteras_clientes.ID_BILLETERA', '=', 'transacciones.ID_BillETERA')
            ->join('ERRORES', 'ERRORES.id_error', '=', 'transacciones.id_error')
            ->select('transacciones.id_transaccion', 'transacciones.fecha_transaccion', 'transacciones.tipo_transaccion', 'movimientos_billeteras.monto_transaccion', 'transacciones.estado_transaccion', 'ERRORES.id_error', 'ERRORES.mensaje_error')->
            where('BILLETERAS_CLIENTES.id_billetera', '=', $id_billetera)->get();
        return response()->json(['transaccion' => $transacciones]);
    }

}
