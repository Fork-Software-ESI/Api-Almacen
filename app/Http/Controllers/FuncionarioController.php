<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Paquete;
use App\Models\PaqueteEstante;
use App\Models\Estante;
use App\Models\FuncionarioPaqueteEstante;
use App\Models\Lote;


class FuncionarioController extends Controller
{
    public function registrarPaqueteEstante(Request $request)
    {
        $validatedData = $request->validate([
            'ID_Funcionario' => 'required|exists:funcionario_almacen,ID',
            'ID_Paquete' => 'required|exists:paquete,ID',
            'ID_Estante' => 'required|exists:estante,ID',
            'ID_Almacen' => 'required|exists:estante,ID_Almacen',   
        ]);
    
        $paquete = Paquete::findOrFail($validatedData['ID_Paquete']);
        $estante = Estante::where('ID', $validatedData['ID_Estante'])
            ->where('ID_Almacen', $validatedData['ID_Almacen'])
            ->firstOrFail();
    
        $paqueteEstante = PaqueteEstante::firstOrCreate([
            'ID_Paquete' => $validatedData['ID_Paquete'],
            'ID_Estante' => $validatedData['ID_Estante'],
            'ID_Almacen' => $validatedData['ID_Almacen'],
        ]);

        FuncionarioPaqueteEstante::firstOrCreate([
            'ID_Funcionario' => $validatedData['ID_Funcionario'],
            'ID_Paquete' => $validatedData['ID_Paquete'],
        ]);
    
        if ($paqueteEstante->wasRecentlyCreated) {
            return response()->json(['success' => 'Paquete ' . $paquete->Codigo . ' registrado en estante con ID ' . $paqueteEstante->ID_Estante . ' en almacén ' . $paqueteEstante->ID_Almacen], 200);
        } else {
            return response()->json(['error' => 'Paquete ya se encuentra en un estante'], 422);
        }
    }

    public function trasladarPaqueteEstante(Request $request)
    {
        $paqueteEstante = PaqueteEstante::where('ID_Paquete', $request->ID_Paquete)->whereNull('deleted_at')->first();

        if ($paqueteEstante == null) {
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }

        $validator = Validator::make($request->all(), [
            'ID_Estante' => 'required|exists:estante,ID',
            'ID_Almacen' => 'required|exists:almacen,ID',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        try {
            $estante = Estante::where('ID', $validatedData['ID_Estante'])
                ->where('ID_Almacen', $validatedData['ID_Almacen'])
                ->firstOrFail();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Estante o almacén no encontrado'], 404);
        }

        $paqueteEstante->ID_Estante = $validatedData['ID_Estante'];

        $paqueteEstante->ID_Almacen = $validatedData['ID_Almacen'];

        $paqueteEstante->save();

        return response()->json(['success' => 'Paquete trasladado a estante con ID ' . $paqueteEstante->ID_Estante . ' en almacén ' . $paqueteEstante->ID_Almacen], 200);
    }

    public function quitarPaqueteDeEstante(Request $request)
    {
        $paqueteEstante = PaqueteEstante::where('ID_Paquete', $request->ID_Paquete)->whereNull('deleted_at')->first();

        if ($paqueteEstante == null) {
            return response()->json(['error' => 'Paquete no encontrado'], 404);
        }

        $paqueteEstante->delete();

        return response()->json(['success' => 'Paquete quitado de estante'], 200);
    }

    public function listarLotes(Request $request)
    {
        $lotes = Lote::all();
        return response()->json(['Lotes' => $lotes], 200);
    }
}
