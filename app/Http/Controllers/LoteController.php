<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Lote;



class LoteController extends Controller
{
    public function altaLote(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }
        $infoValidada = $validator->validated();

        $lote = Lote::create($infoValidada);

        return response()->json(['message' => 'Lote creado', 'datos' => $lote], 201);

    }

    public function editarLote($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descripcion' => 'string|required',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }
        $lote = Lote::find($id);
        if (!$lote) {
            return response()->json(['message' => 'Lote no encontrado'], 404);
        }
        $lote->update($validator->validated());
        return response()->json(['message' => 'Lote actualizado', 'datos' => $lote], 200);
    }

    public function listarLotes()
    {
        $lotes = Lote::all();
        return response()->json(['message' => 'Lotes listados', 'datos' => $lotes], 200);
    }
}