<?php

namespace App\Http\Controllers;

use App\Models\Estante;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Almacen;
use App\Models\PaqueteEstante;

class FuncionarioController extends Controller
{
    /* 
    funcionario
    crea en la relacion paquete_estante (mete paquete en estante)
    actualiza en la relación forma (cambia el estado del paquete en el lote)
    actualiza en lote_camion (cambia el estado del lote en el camion)
    */

    public function paqueteEstante(Request $request){
        $validator = Validator::make($request->all(), [
            'ID_Paquete' => 'required',
            'ID_Estante' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        $paquete = Paquete::find($request->ID_Paquete);
        if(!$paquete){
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }
        
        $estante = Estante::find($request->ID_Estante);
        if(!$estante){
            return response()->json(['error' => 'Estante no encontrado'], 404);
        }

        $almacen = $estante -> almacen -> ID;
        
        /*
        PaqueteEstante::create([
            'ID_Paquete' => $request->ID_Paquete,
            'ID_Estante' => $request->ID_Estante,
            'ID_Almacen' => $almacen
        ]);
        */
        
        $paqueteEstante = new PaqueteEstante;
        $paqueteEstante->ID_Paquete = $request->ID_Paquete;
        $paqueteEstante->ID_Estante = $request->ID_Estante;
        $paqueteEstante->ID_Almacen = $almacen;
        $paqueteEstante->save();

        return response()->json(['Paquete en estante' => $paqueteEstante], 200);
    }
}
