<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Paquete;
use App\Models\Cliente;
use App\Models\Lote;
use App\Models\Forma;
use App\Models\LoteCamion;
use App\Models\Camion;
use App\Models\Chofer;
use App\Models\ChoferCamion;

class GerenteController extends Controller
{
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
        $validator = Validator::make($request->all(), [
            'ID_Camion' => 'required',
            'ID_Chofer' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $validatedData = $validator->validated();

        $chofer = Chofer::find($request->ID_Chofer);

        if(!$chofer){
            return response()->json(['error' => 'Chofer no encontrado'], 400);
        }

        $camion = Camion::find($request->ID_Camion);

        if(!$camion){
            return response()->json(['error' => 'Camion no encontrado'], 400);
        }

        $existe = ChoferCamion::where('ID_Camion', $request->ID_Camion)->where('ID_Chofer', $request->ID_Chofer)->first();
        
        if($existe){
            return response()->json(['error' => 'El chofer ya esta asignado a ese camion'], 400);
        }

        $ninguno_camion = ChoferCamion::where('ID_Camion', $request->ID_Camion)->first();
        $ninguno_chofer = ChoferCamion::where('ID_Chofer', $request->ID_Chofer)->first();

        if($ninguno_camion && $ninguno_chofer){
            return response()->json(['error' => 'Ninguno de los dos esta disponible'], 400);
        }

        $chofer_camion = ChoferCamion::where('ID_Camion', $request->ID_Camion)->first();

        if($chofer_camion){
            return response()->json(['error' => 'Camion no disponible'], 400);
        }

        $chofer_camion = ChoferCamion::where('ID_Chofer', $request->ID_Chofer)->first();

        if($chofer_camion){
            return response()->json(['error' => 'Chofer no disponible'], 400);
        }

        $chofer_camion = new ChoferCamion;
        $chofer_camion->ID_Camion = $request->ID_Camion;
        $chofer_camion->ID_Chofer = $request->ID_Chofer;
        $chofer_camion->Estado = "Pendiente";
        $chofer_camion->save();

        return response()->json(['Datos' => $chofer_camion], 200);
    }
}
