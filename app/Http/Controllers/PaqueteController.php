<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Paquete;



class PaqueteController extends Controller
{
    public function altaPaquete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string',
            'peso_kg' => 'numeric|required',
            'lote_id' => 'nullable|exists:lotes,id',
            "estanteria_id" => 'nullable|exists:estanterias,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }
        $infoValidada = $validator->validated();

        $paquete = Paquete::create($infoValidada);

        return response()->json(['message' => 'Paquete creado', 'datos' => $paquete], 201);

    }

    public function editarPaquete($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'string',
            'peso_kg' => 'numeric',
            'lote_id' => 'nullable|exists:lotes,id',
            "estanteria_id" => 'nullable|exists:estanterias,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }
        $paquete = Paquete::find($id);
        if (!$paquete) {
            return response()->json(['message' => 'Paquete no encontrado'], 404);
        }
        $paquete->update($validator->validated());
        return response()->json(['message' => 'Paquete actualizado', 'datos' => $paquete], 200);
    }

    public function listarPaquetes()
    {
        $paquetes = Paquete::all();
        return response()->json(['message' => 'Paquetes listados', 'datos' => $paquetes], 200);
    }

    public function buscarPaquete($id)
    {
        $paquete = Paquete::find($id);
        if (!$paquete) {
            return response()->json(['message' => 'Paquete no encontrado'], 404);
        }
        return response()->json(['message' => 'Paquete encontrado', 'datos' => $paquete], 200);
    }

    public function eliminarPaquete($id)
    {
        $paquete = Paquete::find($id);
        if (!$paquete) {
            return response()->json(['message' => 'Paquete no encontrado'], 404);
        }
        $paquete->deleted_at = Carbon::now();
        return response()->json(['message' => 'Paquete eliminado'], 200);
    }
}