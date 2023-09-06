<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estanteria;
use App\Models\Almacen;
use App\Models\Paquete;
use Illuminate\Support\Facades\Validator;


class EstanteriaController extends Controller
{
    public function altaEstanteria(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'almacen_id' => 'exists:almacenes,id|required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Error de validación', 'errors' => $validator->errors()], 400);
        }
        $validatedData = $validator->validated();

        $estanteria = Estanteria::create($validatedData);

        return response()->json(['message' => 'Estanteria creada', 'datos' => $estanteria], 201);
    }

    public function listarContenido($id)
    {
        $estanteria = Estanteria::find($id);

        if (!$estanteria) {
            return response()->json(['message' => 'Estanteria no encontrada'], 404);
        }

        $paquetes = Paquete::where('estanteria_id', $id)->get();

        return response()->json(['message' => 'Paquetes en la estanteria', 'data' => $paquetes], 200);
    }
}