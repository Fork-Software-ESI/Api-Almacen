<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Paquete;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Forma;
use App\Models\LoteCamion;

class GerenteController extends Controller
{
    /*
    gerente
    crea paquete
    crea lote
    crea en la relacion forma (paquete lote)
    crea en la relación lote_camion (lote camion)
    crea en la relación chofer_camion
    */

    public function crearPaquete(Request $request){
        $validator = Validator::make($request->all(), [
            'ID_Cliente' => 'required',
            'Descripcion' => 'required',
            'Peso_Kg' => 'required',
            'Estado' => 'required',
            'Destino' => 'required',
            'ID_Lote' => 'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $cliente = Cliente::find($request->ID_Cliente);
        if(!$cliente){
            return response()->json(['error' => 'Cliente no encontrado'], 404);
        }

        $validatedData = $validator->validated();
        $paquete = Paquete::create($validatedData);
        $paquete -> save();
        $id_paquete = $paquete->ID;

        //dd($id_paquete);

        if(!empty($request -> ID_Lote)){
            $lote = Lote::find($request->ID_Lote);
            if(!$lote){
                return response()->json(['error' => 'Lote no encontrado'], 404);
            }
            $forma = new Forma;
            $forma->ID_Paquete = $id_paquete; 
            $forma->ID_Lote = $request->ID_Lote;
            $forma->Estado = 'Pendiente';
            $forma->save();
        }

        return response()->json(['Datos' => $validatedData], 200);
    }
    public function crearLote(Request $request){
        $validator = Validator::make($request->all(), [
            'Descripcion' => 'required',
            'Peso_Kg' => 'required',
            'ID_Camion' => 'nullable'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        $validatedData = $validator->validated();

        $lote = Lote::create($validatedData);
        $lote -> save();
        $id_lote = $lote->ID;

        if(!empty($request -> ID_Camion)){
            $camion = Lote::find($request->ID_Camion);
            if(!$camion){
                return response()->json(['error' => 'Camion no encontrado'], 404);
            }
            $lote_camion = new Forma;
            $lote_camion->ID_Lote = $id_lote; 
            $lote_camion->ID_Camion = $request->ID_Camion;
            $lote_camion->Fecha_Hora_Inicio = now();
            $lote_camion->Estado = 'En almacen';
            $lote_camion->save();
        }

        return response()->json(['Datos' => $validatedData], 200);
    }

    public function choferCamion(Request $request){
        
    }
}
