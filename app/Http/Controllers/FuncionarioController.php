<?php

namespace App\Http\Controllers;

use App\Models\Estante;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\Forma;
use App\Models\LoteCamion;
use App\Models\PaqueteEstante;
use App\Models\ChoferCamion;

class FuncionarioController extends Controller
{

    public function verPaquete (){
        $paquetes = Paquete::all();
        return response()->json(['Paquetes' => $paquetes], 200);
    }

    public function verLote(){
        $lotes = LoteCamion::all();
        return response()->json(['Lotes' => $lotes], 200);
    }

    public function verPaqueteLote(){
        $paquetes = Forma::all();
        return response()->json(['Paquetes en lote' => $paquetes], 200);
    }

    public function verEstante(){
        $estantes = Estante::all();
        return response()->json(['Estantes' => $estantes], 200);
    }  
    
    public function verPaqueteEstante(){
        $paquetes = PaqueteEstante::all();
        return response()->json(['Paquetes en estante' => $paquetes], 200);
    }

    public function verLoteCamion(){
        $lotes = LoteCamion::all();
        return response()->json(['Lotes en camion' => $lotes], 200);
    }   

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
        
        $paqueteEstante = new PaqueteEstante;
        $paqueteEstante->ID_Paquete = $request->ID_Paquete;
        $paqueteEstante->ID_Estante = $request->ID_Estante;
        $paqueteEstante->ID_Almacen = $almacen;
        $paqueteEstante->save();

        return response()->json(['Paquete en estante' => $paqueteEstante], 200);
    }

    public function paqueteLote(Request $request){
        $validator = Validator::make($request->all(), [
            'ID_Paquete' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        $paquete = Forma::find($request->ID_Paquete);
        if(!$paquete){
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }

        $paquete->Estado = 'Cargado';
        $paquete->save();

        return response()->json(['Paquete en lote' => $paquete], 200);
    }

    public function loteCamion(Request $request){
        $validator = Validator::make($request->all(), [
            'ID_Lote' => 'required',
            'Estado' => 'required|in:Cargado,En transito,Pendiente,Entregado'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        
        $lote = LoteCamion::find($request->ID_Lote);
        if(!$lote){
            return response()->json(['error' => 'Lote no encontrado'], 404);
        }

        $lote->Estado = $request->Estado;
        $lote->save();

        $estadoCamion = ChoferCamion::where('ID_Camion', $lote->ID_Camion)->first();

        $lotes = LoteCamion::where('ID_Camion', $lote->ID_Camion)->get();
        $todosCargados = true;
        foreach($lotes as $lote){
            if($lote->Estado != 'Cargado'){
                $todosCargados = false;
            }
        }
        if($todosCargados){
            $estadoCamion -> Estado = 'Cargado';
            $estadoCamion->save();

            $return = [
                'Camion cargado' => $estadoCamion,
                'Lote' => $lotes
            ];

            return response()->json([$return], 200);
        }

        return response()->json(['Lote en camion' => $lote], 200);
    }
}
